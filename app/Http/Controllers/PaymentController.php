<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tạo token xác thực cho trang thanh toán giả lập.
     * Token = sha256(orderId + secret) → không cần lưu DB.
     */
    private function generateToken($orderId)
    {
        return hash('sha256', $orderId . 'greenly_payment_secret_2026');
    }

    /**
     * Hiển thị trang xác nhận thanh toán (quét QR sẽ mở trang này).
     * GET /payment/{orderId}/{token}
     */
    public function showPaymentPage($orderId, $token)
    {
        // Xác thực token
        if ($token !== $this->generateToken($orderId)) {
            abort(403, 'Link thanh toán không hợp lệ.');
        }

        $order = Order::find($orderId);

        if (!$order) {
            abort(404, 'Không tìm thấy đơn hàng.');
        }

        $grandTotal = $order->total_money + $order->shipping_fee;

        return view('payment.confirm', [
            'order'      => $order,
            'orderCode'  => $order->order_code,
            'grandTotal' => $grandTotal,
            'token'      => $token,
            'alreadyPaid' => $order->payment_status === 'completed',
        ]);
    }

    /**
     * Xử lý xác nhận thanh toán từ trang web giả lập.
     * POST /payment/{orderId}/{token}/confirm
     */
    public function processPayment($orderId, $token)
    {
        // Xác thực token
        if ($token !== $this->generateToken($orderId)) {
            return response()->json(['message' => 'Token không hợp lệ'], 403);
        }

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Không tìm thấy đơn hàng'], 404);
        }

        if ($order->payment_status === 'completed') {
            return response()->json(['message' => 'Đơn hàng đã được thanh toán trước đó'], 200);
        }

        // Cập nhật trạng thái thanh toán
        $order->update(['payment_status' => 'completed']);

        return response()->json(['message' => 'Thanh toán thành công!'], 200);
    }

    /**
     * Tạo URL thanh toán cho một đơn hàng.
     * Dùng trong API checkout response.
     */
    public static function generatePaymentUrl($orderId)
    {
        $token = hash('sha256', $orderId . 'greenly_payment_secret_2026');
        return url("/payment/{$orderId}/{$token}");
    }
}
