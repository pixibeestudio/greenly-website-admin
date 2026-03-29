<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // 1. Truy vấn danh sách đơn hàng, Eager Loading
        $query = Order::with(['user', 'orderDetails.product', 'shipper']);

        // 2. Lọc theo Tab trạng thái đơn hàng
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // 3. Tìm kiếm theo mã đơn hoặc SĐT khách hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('shipping_phone', 'like', '%' . $search . '%')
                  ->orWhere('shipping_name', 'like', '%' . $search . '%');
            });
        }

        // 4. Lọc theo ngày tạo đơn
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 5. Sắp xếp mới nhất trước
        $query->latest();

        // 6. Phân trang
        $perPage = $request->input('per_page', 10);
        $orders = $query->paginate($perPage)->appends($request->query());

        // 7. Thống kê cho 4 Card
        $pendingCount = Order::where('order_status', 'pending')->count();
        $shippingCount = Order::where('order_status', 'shipping')->count();
        $completedTodayCount = Order::where('order_status', 'completed')
            ->whereDate('updated_at', Carbon::today())->count();
        $completedTodayRevenue = Order::where('order_status', 'completed')
            ->whereDate('updated_at', Carbon::today())
            ->selectRaw('SUM(total_money + shipping_fee) as total')
            ->value('total') ?? 0;
        $cancelledCount = Order::where('order_status', 'cancelled')->count();

        // 8. Đếm theo từng trạng thái cho badge trên Tabs
        $statusCounts = [
            'pending' => $pendingCount,
            'confirmed' => Order::where('order_status', 'confirmed')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'shipping' => $shippingCount,
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => $cancelledCount,
        ];

        // 9. Lấy danh sách shipper để gán đơn
        $availableShippers = User::where('role', 'shipper')->get();

        return view('admin.orders.index', compact(
            'orders', 'pendingCount', 'shippingCount',
            'completedTodayCount', 'completedTodayRevenue', 'cancelledCount', 'statusCounts',
            'availableShippers'
        ));
    }

    // Cập nhật trạng thái đơn hàng và thanh toán
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,shipping,completed,cancelled',
            'payment_status' => 'required|in:pending,completed,failed',
        ]);

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
        ]);

        // Nếu chuyển sang completed, ghi nhận thời gian giao hàng
        if ($request->order_status === 'completed' && !$order->delivery_date) {
            $order->update(['delivery_date' => now()]);
        }

        return redirect()->route('admin.orders.index', $request->query())
            ->with('success', 'Cập nhật đơn hàng ' . $order->order_code . ' thành công!');
    }

    // Gán shipper cho đơn hàng
    public function assignShipper(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'shipper_id' => 'required|exists:users,id',
        ]);

        $order->update([
            'shipper_id'   => $request->shipper_id,
            'order_status' => 'shipping',
        ]);

        // Cập nhật trạng thái shipper thành đang giao
        User::where('id', $request->shipper_id)->update(['work_status' => 'on_delivery']);

        $shipper = User::find($request->shipper_id);

        return redirect()->back()
            ->with('success', 'Đã gán đơn ' . $order->order_code . ' cho Shipper ' . $shipper->fullname . '!');
    }
}
