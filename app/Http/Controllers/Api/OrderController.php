<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * API lấy danh sách đơn hàng của khách hàng đang đăng nhập
     */
    public function getUserOrders(Request $request)
    {
        $orders = \App\Models\Order::with(['orderDetails.product'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Format dữ liệu theo cấu trúc Android app cần
        $data = $orders->map(function ($order) {
            // Lấy tên sản phẩm đầu tiên làm title
            $firstDetail = $order->orderDetails->first();
            $title = $firstDetail && $firstDetail->product
                ? $firstDetail->product->name
                : 'Đơn hàng';

            // Nếu có nhiều sản phẩm, thêm số lượng
            $detailCount = $order->orderDetails->count();
            if ($detailCount > 1) {
                $title .= ' (+' . ($detailCount - 1) . ' sản phẩm khác)';
            }

            // Lấy ảnh sản phẩm đầu tiên
            $imageUrl = null;
            if ($firstDetail && $firstDetail->product && $firstDetail->product->image) {
                $imagePath = $firstDetail->product->image;
                $imagePath = str_replace('storage/storage/', 'storage/', $imagePath);
                if (!str_starts_with($imagePath, 'storage/') && !str_starts_with($imagePath, 'http')) {
                    $imagePath = 'storage/' . $imagePath;
                }
                $imageUrl = asset($imagePath);
            }

            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'title'          => $title,
                'time_ago'       => $order->created_at->diffForHumans(),
                'image_url'      => $imageUrl,
                'address'        => $order->shipping_address,
                'shipping_name'  => $order->shipping_name,
                'shipping_phone' => $order->shipping_phone,
                'total_price'    => $order->total_money + $order->shipping_fee,
                'shipping_fee'   => $order->shipping_fee,
                'status'         => $order->order_status,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Lấy danh sách đơn hàng thành công',
        ]);
    }
}
