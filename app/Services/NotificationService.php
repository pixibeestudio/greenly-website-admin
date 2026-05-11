<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;

class NotificationService
{
    /**
     * Bản đồ trạng thái → nội dung thông báo.
     * {order_code} sẽ được thay thế bằng mã đơn thực tế.
     */
    private static array $statusMessages = [
        'pending'          => [
            'title'   => 'Đặt hàng thành công',
            'message' => 'Đơn hàng {order_code} đã được đặt thành công!',
        ],
        'processing'       => [
            'title'   => 'Đang xử lý',
            'message' => 'Đơn hàng {order_code} đang được xử lý.',
        ],
        'ready_for_pickup' => [
            'title'   => 'Sẵn sàng giao hàng',
            'message' => 'Đơn hàng {order_code} đã sẵn sàng giao cho shipper.',
        ],
        'shipping'         => [
            'title'   => 'Đang giao hàng',
            'message' => 'Đơn hàng {order_code} đang được giao đến bạn.',
        ],
        'delivered'        => [
            'title'   => 'Giao hàng thành công',
            'message' => 'Đơn hàng {order_code} đã giao thành công!',
        ],
        'cancelled'        => [
            'title'   => 'Đơn hàng đã hủy',
            'message' => 'Đơn hàng {order_code} đã bị hủy.',
        ],
    ];

    /**
     * Tạo thông báo cho khách hàng khi trạng thái đơn thay đổi.
     *
     * @param Order  $order     Đơn hàng (cần có user_id, order_code)
     * @param string $newStatus Trạng thái mới (pending, processing, ...)
     */
    public static function createOrderNotification(Order $order, string $newStatus): void
    {
        $template = self::$statusMessages[$newStatus] ?? null;

        if (!$template) {
            return;
        }

        $orderCode = $order->order_code; // Accessor tự sinh #ORD-YYMMDD-XXX

        Notification::create([
            'user_id'  => $order->user_id,
            'order_id' => $order->id,
            'type'     => $newStatus,
            'title'    => $template['title'],
            'message'  => str_replace('{order_code}', $orderCode, $template['message']),
        ]);
    }
}
