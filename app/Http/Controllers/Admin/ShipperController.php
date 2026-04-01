<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;

class ShipperController extends Controller
{
    public function index(Request $request)
    {
        // 1. Truy vấn danh sách shipper
        $query = User::where('role', 'shipper');

        // 2. Lọc theo work_status
        if ($request->filled('work_status')) {
            $query->where('work_status', $request->work_status);
        }

        // 3. Tìm kiếm theo tên hoặc SĐT
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // 4. Sắp xếp: available trước, rồi on_delivery, cuối cùng offline
        $query->orderByRaw("FIELD(work_status, 'available', 'on_delivery', 'offline')");

        // 5. Phân trang
        $shippers = $query->paginate(10)->appends($request->query());

        // 6. Thống kê cho 4 Cards
        $totalShippers = User::where('role', 'shipper')->count();
        $availableCount = User::where('role', 'shipper')->where('work_status', 'available')->count();
        $onDeliveryCount = User::where('role', 'shipper')->where('work_status', 'on_delivery')->count();
        $offlineCount = User::where('role', 'shipper')->where('work_status', 'offline')->count();

        // Đơn đã giao thành công hôm nay (tất cả shipper)
        $completedTodayCount = Order::where('order_status', 'delivered')
            ->whereNotNull('shipper_id')
            ->whereDate('updated_at', Carbon::today())
            ->count();

        // 7. Tính tỷ lệ thành công + đơn đang giữ cho từng shipper
        $shipperIds = $shippers->pluck('id')->toArray();

        // Đếm đơn đang giao (shipping) cho mỗi shipper
        $deliveringCounts = Order::whereIn('shipper_id', $shipperIds)
            ->where('order_status', 'shipping')
            ->selectRaw('shipper_id, COUNT(*) as count')
            ->groupBy('shipper_id')
            ->pluck('count', 'shipper_id')
            ->toArray();

        // Đếm tổng đơn đã nhận (tháng này) cho mỗi shipper
        $monthlyTotalCounts = Order::whereIn('shipper_id', $shipperIds)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('shipper_id, COUNT(*) as count')
            ->groupBy('shipper_id')
            ->pluck('count', 'shipper_id')
            ->toArray();

        // Đếm đơn giao thành công (tháng này)
        $monthlyCompletedCounts = Order::whereIn('shipper_id', $shipperIds)
            ->where('order_status', 'delivered')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('shipper_id, COUNT(*) as count')
            ->groupBy('shipper_id')
            ->pluck('count', 'shipper_id')
            ->toArray();

        // Đếm đơn đã giao hôm nay cho từng shipper
        $deliveredTodayCounts = Order::whereIn('shipper_id', $shipperIds)
            ->where('order_status', 'delivered')
            ->whereDate('updated_at', Carbon::today())
            ->selectRaw('shipper_id, COUNT(*) as count')
            ->groupBy('shipper_id')
            ->pluck('count', 'shipper_id')
            ->toArray();

        // Tiền COD đang giữ hôm nay (đơn delivered + COD + chưa hoàn tất thanh toán)
        $codKeptAmounts = Order::whereIn('shipper_id', $shipperIds)
            ->where('order_status', 'delivered')
            ->where('payment_method', 'COD')
            ->where('payment_status', '!=', 'completed')
            ->whereDate('updated_at', Carbon::today())
            ->selectRaw('shipper_id, SUM(total_money) as total')
            ->groupBy('shipper_id')
            ->pluck('total', 'shipper_id')
            ->toArray();

        // Tỷ lệ thành công toàn thời gian: delivered / (delivered + cancelled)
        $allTimeDelivered = Order::whereIn('shipper_id', $shipperIds)
            ->where('order_status', 'delivered')
            ->selectRaw('shipper_id, COUNT(*) as count')
            ->groupBy('shipper_id')
            ->pluck('count', 'shipper_id')
            ->toArray();

        $allTimeCancelled = Order::whereIn('shipper_id', $shipperIds)
            ->where('order_status', 'cancelled')
            ->selectRaw('shipper_id, COUNT(*) as count')
            ->groupBy('shipper_id')
            ->pluck('count', 'shipper_id')
            ->toArray();

        // Lấy danh sách đơn đang giao (để tooltip)
        $deliveringOrders = Order::whereIn('shipper_id', $shipperIds)
            ->where('order_status', 'shipping')
            ->select('id', 'shipper_id', 'created_at')
            ->get()
            ->groupBy('shipper_id');

        return view('admin.shippers.index', compact(
            'shippers', 'totalShippers', 'availableCount', 'onDeliveryCount', 'offlineCount',
            'completedTodayCount', 'deliveringCounts', 'monthlyTotalCounts',
            'monthlyCompletedCounts', 'deliveringOrders',
            'deliveredTodayCounts', 'codKeptAmounts', 'allTimeDelivered', 'allTimeCancelled'
        ));
    }

    // Xem chi tiết shipper (trả JSON cho modal AJAX)
    public function show(User $shipper)
    {
        $today = Carbon::today();

        // Đơn hàng hôm nay của shipper
        $todayOrders = Order::where('shipper_id', $shipper->id)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        // Thống kê mini
        $completedToday = $todayOrders->where('order_status', 'delivered')->count();
        $cancelledToday = $todayOrders->where('order_status', 'cancelled')->count();

        // Tiền COD đang giữ (đơn COD đã giao thành công hôm nay, payment chưa hoàn tất)
        $codHolding = Order::where('shipper_id', $shipper->id)
            ->where('payment_method', 'COD')
            ->where('order_status', 'delivered')
            ->where('payment_status', '!=', 'completed')
            ->whereDate('updated_at', $today)
            ->sum('total_money');

        // Đánh giá trung bình
        $avgRating = Order::where('shipper_id', $shipper->id)
            ->whereNotNull('shipper_rating')
            ->avg('shipper_rating');
        $avgRating = $avgRating ? round($avgRating, 1) : 0;

        // Format dữ liệu đơn hàng
        $ordersData = $todayOrders->map(function ($order) {
            return [
                'order_code' => $order->order_code,
                'created_at' => $order->created_at->format('h:i A'),
                'delivery_date' => $order->delivery_date ? $order->delivery_date->format('h:i A') : null,
                'order_status' => $order->order_status,
                'payment_method' => $order->payment_method,
                'final_amount' => $order->final_amount,
                'shipper_rating' => $order->shipper_rating,
                'shipper_review' => $order->shipper_review,
            ];
        });

        return response()->json([
            'shipper' => [
                'id' => $shipper->id,
                'fullname' => $shipper->fullname,
                'phone' => $shipper->phone,
                'avatar' => $shipper->avatar ? asset('storage/' . $shipper->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($shipper->fullname) . '&background=random&size=80',
                'work_status' => $shipper->work_status,
            ],
            'stats' => [
                'completed_today' => $completedToday,
                'cancelled_today' => $cancelledToday,
                'cod_holding' => $codHolding,
                'avg_rating' => $avgRating,
            ],
            'orders' => $ordersData,
        ]);
    }

    // Cập nhật trạng thái làm việc
    public function updateWorkStatus(User $shipper, Request $request)
    {
        $request->validate([
            'work_status' => 'required|in:available,on_delivery,offline',
        ]);

        $shipper->update(['work_status' => $request->work_status]);

        $labels = [
            'available' => 'Sẵn sàng',
            'on_delivery' => 'Đang giao hàng',
            'offline' => 'Nghỉ phép',
        ];

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái "' . $labels[$request->work_status] . '" cho ' . $shipper->fullname);
    }
}
