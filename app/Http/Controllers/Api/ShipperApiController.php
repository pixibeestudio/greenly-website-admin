<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShipperApiController extends Controller
{
    /**
     * Lấy danh sách đơn hàng mới được gán cho shipper đang đăng nhập
     * Điều kiện: shipper_id = auth user, order_status = 'ready_for_pickup'
     */
    public function getNewOrders(Request $request)
    {
        $shipperId = auth()->id();

        $orders = Order::where('shipper_id', $shipperId)
            ->where('order_status', 'ready_for_pickup')
            ->with(['orderDetails.product'])
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

            // Danh sách sản phẩm chi tiết
            $items = $order->orderDetails->map(function ($detail) {
                $imgPath = null;
                if ($detail->product && $detail->product->image) {
                    $imgPath = $detail->product->image;
                    $imgPath = str_replace('storage/storage/', 'storage/', $imgPath);
                    if (!str_starts_with($imgPath, 'storage/') && !str_starts_with($imgPath, 'http')) {
                        $imgPath = 'storage/' . $imgPath;
                    }
                    $imgPath = asset($imgPath);
                }
                return [
                    'product_name'  => $detail->product->name ?? 'SP không xác định',
                    'product_image' => $imgPath,
                    'quantity'      => $detail->quantity,
                    'price'         => $detail->price,
                ];
            });

            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'title'          => $title,
                'time_ago'       => $order->created_at->diffForHumans(),
                'created_at'     => $order->created_at->format('d/m/Y H:i'),
                'image_url'      => $imageUrl,
                'address'        => $order->shipping_address,
                'shipping_name'  => $order->shipping_name,
                'shipping_phone' => $order->shipping_phone,
                'total_price'    => $order->total_money + $order->shipping_fee,
                'shipping_fee'   => $order->shipping_fee,
                'status'         => $order->order_status,
                'items'          => $items,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Lấy danh sách đơn hàng mới thành công',
        ]);
    }

    /**
     * Shipper nhận đơn hàng (chuyển trạng thái sang 'shipping')
     */
    public function acceptOrder(Request $request, $id)
    {
        $shipperId = auth()->id();

        $order = Order::where('id', $id)
            ->where('shipper_id', $shipperId)
            ->where('order_status', 'ready_for_pickup')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng hoặc đơn đã được xử lý!',
            ], 404);
        }

        $order->update([
            'order_status' => 'shipping',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nhận đơn hàng ' . $order->order_code . ' thành công!',
        ]);
    }

    /**
     * Shipper từ chối đơn hàng (trả về trạng thái 'processing', xóa shipper_id)
     */
    public function rejectOrder(Request $request, $id)
    {
        $shipperId = auth()->id();

        $order = Order::where('id', $id)
            ->where('shipper_id', $shipperId)
            ->where('order_status', 'ready_for_pickup')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng hoặc đơn đã được xử lý!',
            ], 404);
        }

        $order->update([
            'shipper_id'   => null,
            'order_status' => 'processing',
        ]);

        // Cập nhật trạng thái shipper nếu không còn đơn nào
        $remainingOrders = Order::where('shipper_id', $shipperId)
            ->whereIn('order_status', ['ready_for_pickup', 'shipping'])
            ->count();

        if ($remainingOrders === 0) {
            User::where('id', $shipperId)->update(['work_status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối đơn hàng ' . $order->order_code,
        ]);
    }

    /**
     * Lấy danh sách đơn hàng đang giao của shipper
     * Điều kiện: shipper_id = auth user, order_status = 'shipping'
     */
    public function getShippingOrders()
    {
        $shipperId = auth()->id();

        $orders = Order::where('shipper_id', $shipperId)
            ->where('order_status', 'shipping')
            ->with(['user', 'orderDetails.product'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $data = $orders->map(function ($order) {
            // Lấy tên sản phẩm đầu tiên làm title
            $firstDetail = $order->orderDetails->first();
            $title = $firstDetail && $firstDetail->product
                ? $firstDetail->product->name
                : 'Đơn hàng';

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

            // Danh sách sản phẩm chi tiết
            $items = $order->orderDetails->map(function ($detail) {
                $imgPath = null;
                if ($detail->product && $detail->product->image) {
                    $imgPath = $detail->product->image;
                    $imgPath = str_replace('storage/storage/', 'storage/', $imgPath);
                    if (!str_starts_with($imgPath, 'storage/') && !str_starts_with($imgPath, 'http')) {
                        $imgPath = 'storage/' . $imgPath;
                    }
                    $imgPath = asset($imgPath);
                }
                return [
                    'product_name'  => $detail->product->name ?? 'SP không xác định',
                    'product_image' => $imgPath,
                    'quantity'      => $detail->quantity,
                    'price'         => $detail->price,
                ];
            });

            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'title'          => $title,
                'time_ago'       => $order->updated_at->diffForHumans(),
                'created_at'     => $order->created_at->format('d/m/Y H:i'),
                'image_url'      => $imageUrl,
                'address'        => $order->shipping_address,
                'shipping_name'  => $order->shipping_name,
                'shipping_phone' => $order->shipping_phone,
                'total_price'    => $order->total_money + $order->shipping_fee,
                'shipping_fee'   => $order->shipping_fee,
                'status'         => $order->order_status,
                'items'          => $items,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Lấy danh sách đơn đang giao thành công',
        ]);
    }

    /**
     * Shipper báo giao thất bại → chuyển đơn về 'cancelled', shipper rảnh lại
     */
    public function failOrder(Request $request, $id)
    {
        $shipperId = auth()->id();

        $order = Order::where('id', $id)
            ->where('shipper_id', $shipperId)
            ->where('order_status', 'shipping')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng hoặc đơn không ở trạng thái đang giao!',
            ], 404);
        }

        $order->update([
            'order_status' => 'cancelled',
        ]);

        // Cập nhật trạng thái shipper nếu không còn đơn nào đang giao
        $remainingOrders = Order::where('shipper_id', $shipperId)
            ->whereIn('order_status', ['ready_for_pickup', 'shipping'])
            ->count();

        if ($remainingOrders === 0) {
            User::where('id', $shipperId)->update(['work_status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã báo giao thất bại đơn ' . $order->order_code,
        ]);
    }

    /**
     * Lấy thống kê của shipper (số đơn hôm nay, thu nhập hôm nay, trạng thái)
     */
    public function getStats(Request $request)
    {
        $shipper = auth()->user();
        $today = Carbon::today();

        // Đơn hoàn thành trong ngày
        $todayCompletedOrders = Order::where('shipper_id', $shipper->id)
            ->where('order_status', 'completed')
            ->whereDate('updated_at', $today)
            ->get();

        $todayOrdersCount = $todayCompletedOrders->count();
        // Thu nhập: Giả sử thu nhập là tổng phí ship của các đơn đã giao
        $todayIncome = $todayCompletedOrders->sum('shipping_fee');

        return response()->json([
            'success' => true,
            'data'    => [
                'fullname'     => $shipper->fullname,
                'work_status'  => $shipper->work_status,
                'today_orders' => $todayOrdersCount,
                'today_income' => $todayIncome,
            ],
            'message' => 'Lấy thống kê thành công',
        ]);
    }

    /**
     * Cập nhật trạng thái làm việc (available / offline)
     */
    public function updateWorkStatus(Request $request)
    {
        $request->validate([
            'work_status' => 'required|in:available,offline,on_delivery',
        ]);

        $shipper = auth()->user();
        
        // Không cho phép đổi thành offline nếu đang có đơn giao dở
        if ($request->work_status === 'offline') {
            $activeOrders = Order::where('shipper_id', $shipper->id)
                ->whereIn('order_status', ['ready_for_pickup', 'shipping'])
                ->count();
                
            if ($activeOrders > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể đổi trạng thái khi đang có đơn hàng cần giao!',
                ], 400);
            }
        }

        $shipper->update(['work_status' => $request->work_status]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái làm việc',
        ]);
    }
}
