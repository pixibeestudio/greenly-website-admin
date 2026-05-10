<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MomoTransaction;
use App\Models\Order;
use App\Services\MomoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller xử lý các API tích hợp thanh toán MoMo.
 *
 * Có 3 endpoint chính:
 *  1. POST /api/momo/create-payment   (auth)   - App gọi để tạo giao dịch
 *  2. POST /api/momo/ipn              (public) - MoMo gọi callback
 *  3. GET  /api/momo/status/{orderId} (auth)   - App polling check status
 */
class MomoController extends Controller
{
    private MomoService $momoService;

    public function __construct(MomoService $momoService)
    {
        $this->momoService = $momoService;
    }

    /**
     * App gọi để tạo giao dịch MoMo cho 1 đơn hàng.
     *
     * Request body:
     *   - order_id (int)    : ID đơn hàng
     *   - type    (string)  : 'app' hoặc 'qr'
     *
     * Response:
     *   {
     *     success: true,
     *     data: {
     *       momo_order_id: "GREENLY_123_...",
     *       amount: 50000,
     *       pay_url: "...",       // URL web mở MoMo
     *       deeplink: "momo://...",  // Mở app MoMo trực tiếp (type=app)
     *       qr_code_url: "https://..."  // URL ảnh QR (type=qr)
     *     }
     *   }
     */
    public function createPayment(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'type'     => 'required|in:app,qr',
        ]);

        $userId = auth()->id();

        // Tìm đơn hàng và kiểm tra quyền sở hữu
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng hoặc không có quyền truy cập',
            ], 404);
        }

        // Kiểm tra đơn hàng đã thanh toán chưa
        if ($order->payment_status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng đã được thanh toán',
            ], 400);
        }

        // Kiểm tra phương thức thanh toán
        if ($order->payment_method !== 'banking') {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng không sử dụng phương thức MoMo',
            ], 400);
        }

        // Tạo giao dịch
        try {
            $transaction = $this->momoService->createPayment($order, $request->type);

            return response()->json([
                'success' => true,
                'message' => 'Tạo giao dịch MoMo thành công',
                'data'    => [
                    'momo_order_id' => $transaction->momo_order_id,
                    'amount'        => (int) $transaction->amount,
                    'payment_type'  => $transaction->payment_type,
                    'pay_url'       => $transaction->pay_url,
                    'deeplink'      => $transaction->deeplink,
                    'qr_code_url'   => $transaction->qr_code_url,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('MoMo createPayment thất bại', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tạo giao dịch MoMo thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * IPN endpoint - MoMo gọi sau khi user thanh toán.
     *
     * Endpoint này PHẢI public (không auth) vì MoMo gọi từ server của họ.
     * URL phải là PUBLIC INTERNET → cần ngrok khi dev local.
     *
     * MoMo yêu cầu trả về JSON theo format chuẩn:
     *   { "partnerCode": "MOMO", "requestId": "...", "orderId": "...",
     *     "resultCode": 0, "message": "OK", "responseTime": ... }
     */
    public function ipn(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('[MoMo IPN] Nhận callback', $data);

        // Bước 1: Verify chữ ký HMAC
        if (!$this->momoService->verifyIpnSignature($data)) {
            Log::warning('[MoMo IPN] Chữ ký không hợp lệ - từ chối', $data);

            // Vẫn trả 204 để MoMo không retry liên tục, nhưng KHÔNG cập nhật DB
            return response()->json([
                'partnerCode' => $data['partnerCode'] ?? '',
                'requestId'   => $data['requestId']   ?? '',
                'orderId'     => $data['orderId']     ?? '',
                'resultCode'  => 97,
                'message'     => 'Invalid signature',
                'responseTime' => round(microtime(true) * 1000),
            ], 204);
        }

        // Bước 2: Cập nhật trạng thái giao dịch
        $transaction = $this->momoService->processIpn($data);

        if (!$transaction) {
            return response()->json([
                'partnerCode' => $data['partnerCode'] ?? '',
                'requestId'   => $data['requestId']   ?? '',
                'orderId'     => $data['orderId']     ?? '',
                'resultCode'  => 1,
                'message'     => 'Order not found',
                'responseTime' => round(microtime(true) * 1000),
            ], 200);
        }

        // Bước 3: Trả response chuẩn cho MoMo (resultCode=0 nghĩa là đã ack)
        return response()->json([
            'partnerCode'  => $data['partnerCode'] ?? '',
            'requestId'    => $data['requestId']   ?? '',
            'orderId'      => $data['orderId']     ?? '',
            'resultCode'   => 0,
            'message'      => 'OK',
            'responseTime' => round(microtime(true) * 1000),
        ], 200);
    }

    /**
     * App polling check trạng thái giao dịch (gọi mỗi 3 giây).
     *
     * Trả về trạng thái thanh toán dựa trên bảng momo_transactions
     * (record mới nhất) hoặc trên bảng orders.payment_status.
     */
    public function checkStatus(Request $request, int $orderId): JsonResponse
    {
        $userId = auth()->id();

        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        // Lấy giao dịch MoMo mới nhất của đơn hàng (nếu có)
        $latestTransaction = MomoTransaction::where('order_id', $orderId)
            ->latest('id')
            ->first();

        return response()->json([
            'success'        => true,
            'order_id'       => $order->id,
            'payment_status' => $order->payment_status, // 'pending' | 'completed'
            'momo_status'    => $latestTransaction?->status, // 'pending' | 'success' | 'failed' | null
            'momo_trans_id'  => $latestTransaction?->momo_trans_id,
        ], 200);
    }
}
