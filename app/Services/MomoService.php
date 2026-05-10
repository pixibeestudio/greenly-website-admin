<?php

namespace App\Services;

use App\Models\MomoTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service tích hợp thanh toán MoMo (Sandbox).
 *
 * Trách nhiệm:
 *  - Tạo request thanh toán (gồm 2 luồng: 'app' deeplink hoặc 'qr')
 *  - Ký HMAC-SHA256 các tham số gửi tới MoMo
 *  - Xác thực chữ ký IPN MoMo gửi về
 *  - Cập nhật trạng thái giao dịch vào DB
 *
 * Tham chiếu: https://developers.momo.vn/v3/docs/payment/api/wallet/onetime/
 */
class MomoService
{
    /**
     * Tạo giao dịch MoMo cho 1 đơn hàng.
     *
     * @param  Order  $order  Đơn hàng cần thanh toán (đã được tạo ở DB)
     * @param  string $type   'app' (mở app MoMo) hoặc 'qr' (hiển thị QR)
     * @return MomoTransaction Record giao dịch vừa tạo (chứa pay_url/qr_code_url)
     *
     * @throws \Exception Khi MoMo trả về lỗi hoặc không cấu hình IPN URL
     */
    public function createPayment(Order $order, string $type): MomoTransaction
    {
        // Validate type
        if (!in_array($type, ['app', 'qr'], true)) {
            throw new \InvalidArgumentException("Loại thanh toán không hợp lệ: {$type}");
        }

        // Bắt buộc phải cấu hình IPN URL (ngrok URL)
        $ipnUrl = config('momo.ipn_url');
        if (empty($ipnUrl)) {
            throw new \Exception(
                'Chưa cấu hình MOMO_IPN_URL trong .env. ' .
                'Cần URL public (ngrok) để MoMo gửi callback.'
            );
        }

        // Sinh orderId & requestId duy nhất theo định dạng GREENLY_<orderId>_<timestamp>_<random>
        $timestamp     = (string) round(microtime(true) * 1000); // millisecond
        $momoOrderId   = "GREENLY_{$order->id}_{$timestamp}";
        $momoRequestId = $momoOrderId . '_' . Str::random(6);

        // Tổng tiền = tiền hàng + ship (số nguyên đồng)
        $amount = (int) ($order->total_money + $order->shipping_fee);

        // Validate số tiền tối thiểu (MoMo Sandbox tối thiểu 1.000đ)
        if ($amount < 1000) {
            throw new \Exception("Số tiền thanh toán quá nhỏ (tối thiểu 1.000đ). Hiện tại: {$amount}");
        }

        // Build params - thứ tự alphabet là QUAN TRỌNG khi ký
        $partnerCode = config('momo.partner_code');
        $accessKey   = config('momo.access_key');
        $secretKey   = config('momo.secret_key');
        $endpoint    = config('momo.endpoint');
        $redirectUrl = config('momo.redirect_url');
        $requestType = config('momo.request_type');

        $orderInfo = "Thanh toan don hang #{$order->id} - Greenly";
        $extraData = base64_encode(json_encode([
            'order_id'   => $order->id,
            'order_code' => $order->order_code,
        ]));

        // Chuỗi ký - bắt buộc đúng thứ tự alphabet, nối bằng '&'
        $rawSignature = "accessKey={$accessKey}"
            . "&amount={$amount}"
            . "&extraData={$extraData}"
            . "&ipnUrl={$ipnUrl}"
            . "&orderId={$momoOrderId}"
            . "&orderInfo={$orderInfo}"
            . "&partnerCode={$partnerCode}"
            . "&redirectUrl={$redirectUrl}"
            . "&requestId={$momoRequestId}"
            . "&requestType={$requestType}";

        $signature = $this->hmacSHA256($rawSignature, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'accessKey'   => $accessKey,
            'requestId'   => $momoRequestId,
            'amount'      => (string) $amount,
            'orderId'     => $momoOrderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
            'lang'        => 'vi',
        ];

        $this->debugLog('MoMo createPayment - Request', [
            'order_id'      => $order->id,
            'momo_order_id' => $momoOrderId,
            'amount'        => $amount,
            'type'          => $type,
            'raw_signature' => $rawSignature,
        ]);

        // Gọi MoMo API
        try {
            $response = Http::timeout(config('momo.http_timeout', 15))
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);
        } catch (\Throwable $e) {
            Log::error('MoMo API gọi thất bại', ['error' => $e->getMessage()]);
            throw new \Exception('Không kết nối được MoMo: ' . $e->getMessage());
        }

        $body = $response->json() ?? [];

        $this->debugLog('MoMo createPayment - Response', $body);

        // resultCode = 0 nghĩa là tạo giao dịch thành công (chưa thanh toán)
        $resultCode = $body['resultCode'] ?? -1;
        $message    = $body['message'] ?? 'Lỗi không xác định';

        if ($resultCode !== 0) {
            throw new \Exception("MoMo từ chối: [{$resultCode}] {$message}");
        }

        // Lưu giao dịch vào DB
        $payUrl   = $body['payUrl']   ?? null;
        $deeplink = $body['deeplink'] ?? null;

        // ========================================================================
        // Tạo QR code URL với thứ tự ưu tiên:
        //   1. qrCodeUrl trực tiếp từ MoMo response (nếu MoMo trả về)
        //   2. Generate QR từ deeplink momo://...
        //      → Khi scan bằng MoMo App, MoMo nhận diện là payment intent
        //        và mở form xác nhận trong app (giống luồng deeplink)
        //   3. Fallback cuối: generate từ payUrl (https://test-payment.momo.vn/...)
        //      → KHÔNG khuyến nghị: MoMo App sẽ mở WebView hiển thị trang web,
        //        không có form xác nhận trong app như mong đợi.
        //
        // Lưu ý: deeplink chứa các thông tin cần thiết (orderId, amount, signature)
        // mà MoMo App có thể parse và xử lý nội bộ giống như khi user bấm "Mở app MoMo"
        // ngay trên thiết bị thanh toán.
        // ========================================================================
        $qrCodeUrl = null;
        if ($type === 'qr') {
            if (!empty($body['qrCodeUrl'])) {
                // Ưu tiên 1: dùng QR có sẵn từ MoMo (chuẩn nhất)
                $qrCodeUrl = $body['qrCodeUrl'];
            } elseif (!empty($deeplink)) {
                // Ưu tiên 2: encode deeplink momo:// → MoMo App nhận diện ngay
                $qrCodeUrl = config('momo.qr_generator_url') . urlencode($deeplink);
            } elseif (!empty($payUrl)) {
                // Fallback cuối: encode payUrl (mở WebView, không có form xác nhận)
                Log::warning('MoMo: thiếu deeplink, fallback QR sang payUrl', [
                    'momo_order_id' => $momoOrderId,
                ]);
                $qrCodeUrl = config('momo.qr_generator_url') . urlencode($payUrl);
            }
        }

        return MomoTransaction::create([
            'order_id'        => $order->id,
            'momo_order_id'   => $momoOrderId,
            'momo_request_id' => $momoRequestId,
            'payment_type'    => $type,
            'amount'          => $amount,
            'pay_url'         => $payUrl,
            'qr_code_url'     => $qrCodeUrl,
            'deeplink'        => $deeplink,
            'status'          => 'pending',
            'result_code'     => $resultCode,
            'message'         => $message,
            'raw_response'    => $body,
        ]);
    }

    /**
     * Xác thực chữ ký IPN MoMo gửi về.
     *
     * MoMo ký body IPN bằng cùng secretKey. Ta tự tính lại signature
     * theo thứ tự alphabet và so sánh.
     *
     * @param  array $data  Toàn bộ body IPN (đã json_decode)
     * @return bool         true nếu chữ ký hợp lệ
     */
    public function verifyIpnSignature(array $data): bool
    {
        if (empty($data['signature'])) {
            return false;
        }

        $accessKey = config('momo.access_key');
        $secretKey = config('momo.secret_key');

        // Format chuỗi ký theo tài liệu MoMo (alphabet order)
        // https://developers.momo.vn/v3/docs/payment/api/wallet/onetime/#ipn-url
        $rawSignature = "accessKey={$accessKey}"
            . "&amount=" . ($data['amount'] ?? '')
            . "&extraData=" . ($data['extraData'] ?? '')
            . "&message=" . ($data['message'] ?? '')
            . "&orderId=" . ($data['orderId'] ?? '')
            . "&orderInfo=" . ($data['orderInfo'] ?? '')
            . "&orderType=" . ($data['orderType'] ?? '')
            . "&partnerCode=" . ($data['partnerCode'] ?? '')
            . "&payType=" . ($data['payType'] ?? '')
            . "&requestId=" . ($data['requestId'] ?? '')
            . "&responseTime=" . ($data['responseTime'] ?? '')
            . "&resultCode=" . ($data['resultCode'] ?? '')
            . "&transId=" . ($data['transId'] ?? '');

        $expectedSignature = $this->hmacSHA256($rawSignature, $secretKey);

        $valid = hash_equals($expectedSignature, $data['signature']);

        if (!$valid) {
            Log::warning('MoMo IPN signature INVALID', [
                'expected' => $expectedSignature,
                'received' => $data['signature'],
                'raw'      => $rawSignature,
            ]);
        }

        return $valid;
    }

    /**
     * Xử lý IPN từ MoMo: cập nhật trạng thái giao dịch + trạng thái đơn hàng.
     *
     * @param  array $data  Body IPN (đã verify chữ ký)
     * @return MomoTransaction|null
     */
    public function processIpn(array $data): ?MomoTransaction
    {
        $momoOrderId = $data['orderId'] ?? null;

        if (!$momoOrderId) {
            Log::warning('MoMo IPN thiếu orderId', $data);
            return null;
        }

        // Tìm record giao dịch theo momo_order_id
        $transaction = MomoTransaction::where('momo_order_id', $momoOrderId)->first();

        if (!$transaction) {
            Log::warning('MoMo IPN: Không tìm thấy giao dịch', ['momo_order_id' => $momoOrderId]);
            return null;
        }

        // Idempotent: nếu đã success rồi thì bỏ qua
        if ($transaction->status === 'success') {
            $this->debugLog('MoMo IPN: Giao dịch đã success từ trước, bỏ qua', $data);
            return $transaction;
        }

        $resultCode = (int) ($data['resultCode'] ?? -1);
        $isSuccess  = $resultCode === 0;

        // Cập nhật giao dịch
        $transaction->update([
            'momo_trans_id'      => $data['transId']    ?? null,
            'status'             => $isSuccess ? 'success' : 'failed',
            'result_code'        => $resultCode,
            'message'            => $data['message']    ?? '',
            'raw_ipn'            => $data,
            'signature_verified' => true,
            'completed_at'       => $isSuccess ? now() : null,
        ]);

        // Nếu thành công: cập nhật trạng thái thanh toán của đơn hàng
        if ($isSuccess) {
            $transaction->order()->update([
                'payment_status' => 'completed',
            ]);
            $this->debugLog('MoMo IPN: Thanh toán thành công', [
                'order_id'      => $transaction->order_id,
                'momo_order_id' => $momoOrderId,
                'trans_id'      => $data['transId'] ?? null,
            ]);
        } else {
            Log::warning('MoMo IPN: Thanh toán THẤT BẠI', [
                'order_id'    => $transaction->order_id,
                'result_code' => $resultCode,
                'message'     => $data['message'] ?? '',
            ]);
        }

        return $transaction;
    }

    /**
     * Hàm ký HMAC-SHA256 chuẩn MoMo.
     */
    private function hmacSHA256(string $data, string $key): string
    {
        return hash_hmac('sha256', $data, $key);
    }

    /**
     * Ghi log debug nếu bật MOMO_DEBUG_LOG=true.
     */
    private function debugLog(string $message, $context = []): void
    {
        if (config('momo.debug_log', false)) {
            Log::channel(config('logging.default'))->info("[MoMo] {$message}", (array) $context);
        }
    }
}
