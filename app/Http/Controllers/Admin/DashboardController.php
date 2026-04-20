<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Controller xử lý trang Dashboard của admin.
 *
 * - index(): hiển thị giao diện Dashboard lần đầu với data mặc định "Tháng này"
 * - getStats(): endpoint AJAX trả về số liệu theo khoảng thời gian:
 *               today | week | month | year
 */
class DashboardController extends Controller
{
    /**
     * Hiển thị trang Dashboard.
     */
    public function index()
    {
        // Mặc định khi load trang: Tháng này
        $stats = $this->buildStats('month');

        // Các dữ liệu phụ không phụ thuộc vào bộ lọc
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $topProducts = Product::withSum('batches', 'current_quantity')
            ->withCount('orderDetails')
            ->orderByDesc('order_details_count')
            ->take(5)
            ->get();

        // Tổng sản phẩm (không phụ thuộc vào filter)
        $totalProducts = Product::count();

        return view('admin.dashboard', [
            'stats'            => $stats,
            'recentOrders'     => $recentOrders,
            'topProducts'      => $topProducts,
            'totalProducts'    => $totalProducts,
            'initialPeriod'    => 'month',
        ]);
    }

    /**
     * Endpoint AJAX - trả về dữ liệu thống kê theo period.
     * GET /admin/dashboard/stats?period=today|week|month|year
     */
    public function getStats(Request $request)
    {
        $period = $request->query('period', 'month');

        if (!in_array($period, ['today', 'week', 'month', 'year'], true)) {
            $period = 'month';
        }

        return response()->json([
            'success' => true,
            'data'    => $this->buildStats($period),
        ]);
    }

    /**
     * Tính toàn bộ số liệu thống kê cho một khoảng thời gian.
     *
     * @param  string  $period  today | week | month | year
     * @return array
     */
    protected function buildStats(string $period): array
    {
        [$from, $to]  = $this->getDateRange($period);
        $labels       = $this->getPeriodLabels($period);

        // --- 1. KPI cards ---
        // Doanh thu: chỉ tính đơn đã giao trong khoảng thời gian (dùng updated_at)
        $revenue = Order::where('order_status', 'delivered')
            ->whereBetween('updated_at', [$from, $to])
            ->sum('total_money');

        // Đơn hàng: tổng đơn đặt trong khoảng thời gian (dùng created_at)
        $orderCount = Order::whereBetween('created_at', [$from, $to])->count();

        // Khách hàng mới đăng ký trong khoảng thời gian
        $newCustomers = User::where('role', 'customer')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        // Tổng sản phẩm: không phụ thuộc vào filter
        $totalProducts = Product::count();

        // --- 2. Trạng thái đơn hàng (donut) trong khoảng ---
        $orderStatusCounts = [
            'delivered'  => Order::where('order_status', 'delivered')->whereBetween('created_at', [$from, $to])->count(),
            'pending'    => Order::where('order_status', 'pending')->whereBetween('created_at', [$from, $to])->count(),
            'processing' => Order::where('order_status', 'processing')->whereBetween('created_at', [$from, $to])->count(),
            'shipping'   => Order::where('order_status', 'shipping')->whereBetween('created_at', [$from, $to])->count(),
            'cancelled'  => Order::where('order_status', 'cancelled')->whereBetween('created_at', [$from, $to])->count(),
        ];

        // --- 3. Dữ liệu biểu đồ doanh thu theo mốc thời gian ---
        $chart = $this->buildChartSeries($period, $from, $to);

        return [
            'period'           => $period,
            'period_label'     => $labels['short'],       // "hôm nay", "tuần này"...
            'period_label_full'=> $labels['full'],        // "Hôm nay", "Tuần này"...
            'revenue'          => (int) $revenue,
            'order_count'      => $orderCount,
            'new_customers'    => $newCustomers,
            'total_products'   => $totalProducts,
            'order_status'     => $orderStatusCounts,
            'chart'            => $chart,
        ];
    }

    /**
     * Tính khoảng thời gian [from, to] theo period.
     */
    protected function getDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(),   $now->copy()->endOfDay()],
            'week'  => [$now->copy()->startOfWeek(),  $now->copy()->endOfWeek()],
            'year'  => [$now->copy()->startOfYear(),  $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()], // month
        };
    }

    /**
     * Nhãn hiển thị cho KPI cards.
     */
    protected function getPeriodLabels(string $period): array
    {
        return match ($period) {
            'today' => ['short' => 'hôm nay',  'full' => 'Hôm nay'],
            'week'  => ['short' => 'tuần này', 'full' => 'Tuần này'],
            'year'  => ['short' => 'năm nay',  'full' => 'Năm nay'],
            default => ['short' => 'tháng này','full' => 'Tháng này'], // month
        };
    }

    /**
     * Xây dựng dữ liệu biểu đồ doanh thu & lợi nhuận (lợi nhuận ước tính 30% doanh thu).
     *
     * - today: theo 24 giờ (00h-23h)
     * - week : theo 7 ngày (T2-CN)
     * - month: theo 30 ngày gần nhất của tháng hiện tại
     * - year : theo 12 tháng (T1-T12)
     */
    protected function buildChartSeries(string $period, Carbon $from, Carbon $to): array
    {
        $categories = [];
        $revenue    = [];

        switch ($period) {
            case 'today':
                // 24 khung giờ
                for ($h = 0; $h < 24; $h++) {
                    $categories[] = str_pad((string) $h, 2, '0', STR_PAD_LEFT) . 'h';
                    $start = $from->copy()->setTime($h, 0, 0);
                    $end   = $from->copy()->setTime($h, 59, 59);
                    $revenue[] = (int) Order::where('order_status', 'delivered')
                        ->whereBetween('updated_at', [$start, $end])
                        ->sum('total_money');
                }
                break;

            case 'week':
                // 7 ngày trong tuần (T2-CN)
                $days = ['T2','T3','T4','T5','T6','T7','CN'];
                for ($i = 0; $i < 7; $i++) {
                    $day = $from->copy()->addDays($i);
                    $categories[] = $days[$i];
                    $revenue[] = (int) Order::where('order_status', 'delivered')
                        ->whereDate('updated_at', $day->toDateString())
                        ->sum('total_money');
                }
                break;

            case 'year':
                // 12 tháng
                for ($m = 1; $m <= 12; $m++) {
                    $categories[] = 'T' . $m;
                    $start = Carbon::create($from->year, $m, 1)->startOfMonth();
                    $end   = $start->copy()->endOfMonth();
                    $revenue[] = (int) Order::where('order_status', 'delivered')
                        ->whereBetween('updated_at', [$start, $end])
                        ->sum('total_money');
                }
                break;

            case 'month':
            default:
                // Số ngày trong tháng hiện tại
                $totalDays = $from->daysInMonth;
                for ($d = 1; $d <= $totalDays; $d++) {
                    $date = Carbon::create($from->year, $from->month, $d);
                    $categories[] = (string) $d;
                    $revenue[] = (int) Order::where('order_status', 'delivered')
                        ->whereDate('updated_at', $date->toDateString())
                        ->sum('total_money');
                }
                break;
        }

        // Lợi nhuận ước tính = 30% doanh thu (do chưa có cost trong DB)
        $profit = array_map(fn ($v) => (int) round($v * 0.3), $revenue);

        return [
            'categories' => $categories,
            'revenue'    => $revenue,
            'profit'     => $profit,
        ];
    }
}
