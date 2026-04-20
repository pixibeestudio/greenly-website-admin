@extends('layouts.admin')

@section('title', ($pageTitle ?? 'Dashboard') . ' - Greenly Admin')

@section('page-title', $pageTitle ?? 'Dashboard')

@push('styles')
<style>
    .kpi-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
    }
    .chart-card {
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .chart-card:hover {
        border-color: var(--color-forest-500);
        box-shadow: 0 10px 25px -5px rgba(46, 125, 50, 0.08);
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endpush

@section('content')
<div class="fade-in">

    <!-- ============================================ -->
    <!-- PHẦN 1: HEADER DASHBOARD -->
    <!-- ============================================ -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tổng quan hệ thống</h2>
            <p class="text-sm text-gray-500 mt-0.5">Xin chào! Dưới đây là tóm tắt hoạt động kinh doanh của bạn.</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Spinner loading khi AJAX chạy -->
            <span id="filterLoading" class="hidden text-forest-600 text-sm font-bold"><i class="fa-solid fa-circle-notch fa-spin"></i> Đang tải...</span>
            <div class="relative">
                <select id="timeFilter" class="bg-white border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm font-bold text-gray-600 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all shadow-sm appearance-none cursor-pointer">
                    <option value="today"  {{ ($initialPeriod ?? 'month') === 'today' ? 'selected' : '' }}>Hôm nay</option>
                    <option value="week"   {{ ($initialPeriod ?? 'month') === 'week'  ? 'selected' : '' }}>Tuần này</option>
                    <option value="month"  {{ ($initialPeriod ?? 'month') === 'month' ? 'selected' : '' }}>Tháng này</option>
                    <option value="year"   {{ ($initialPeriod ?? 'month') === 'year'  ? 'selected' : '' }}>Năm nay</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- PHẦN 2: KPI CARDS (4 cột) -->
    <!-- ============================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Card 1: Doanh thu (theo filter) -->
        <div class="kpi-card bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-sack-dollar text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider mb-1">Doanh thu <span class="kpi-period-label">{{ $stats['period_label'] }}</span></p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" id="kpi-revenue" data-target="{{ (int) $stats['revenue'] }}" data-suffix="đ" data-format="currency">0đ</span>
                    </h3>
                    <p class="text-forest-200 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-calendar-day text-organic-400"></i>
                        <span class="font-bold text-organic-400">{{ now()->format('d/m/Y') }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-organic-400 text-xl shadow-inner">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Đơn hàng (theo filter) -->
        <div class="kpi-card bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-boxes-stacked text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[11px] font-bold uppercase tracking-wider mb-1">Đơn hàng <span class="kpi-period-label">{{ $stats['period_label'] }}</span></p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" id="kpi-orders" data-target="{{ $stats['order_count'] }}" data-suffix=" đơn" data-format="number">0 đơn</span>
                    </h3>
                    <p class="text-blue-100 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-clock text-sky-300"></i>
                        <span class="font-bold text-sky-300">Realtime</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-sky-300 text-xl shadow-inner">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Khách hàng mới (theo filter) -->
        <div class="kpi-card bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-users text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider mb-1">Khách hàng mới <span class="kpi-period-label">{{ $stats['period_label'] }}</span></p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" id="kpi-customers" data-target="{{ $stats['new_customers'] }}" data-suffix=" người" data-format="number">0 người</span>
                    </h3>
                    <p class="text-emerald-100 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-user-plus text-yellow-300"></i>
                        <span class="font-bold text-yellow-300">Mới đăng ký</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-yellow-300 text-xl shadow-inner">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Tổng sản phẩm (không phụ thuộc filter) -->
        <div class="kpi-card bg-gradient-to-br from-violet-700 via-violet-600 to-violet-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-leaf text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-violet-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng sản phẩm</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" id="kpi-products" data-target="{{ $totalProducts }}" data-suffix=" SP" data-format="number">0 SP</span>
                    </h3>
                    <p class="text-violet-100 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-store text-violet-200"></i>
                        <span class="font-bold text-violet-200">Đang kinh doanh</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-violet-200 text-xl shadow-inner">
                    <i class="fa-solid fa-leaf"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- PHẦN 3: BIỂU ĐỒ (2 cột: 2/3 + 1/3) -->
    <!-- ============================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Cột trái: Biểu đồ Doanh thu & Lợi nhuận (Area/Line) -->
        <div class="lg:col-span-2 chart-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-area text-forest-600"></i> Doanh thu & Lợi nhuận
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">7 ngày gần nhất</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-bold">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-forest-600 inline-block"></span> Doanh thu</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-organic-500 inline-block"></span> Lợi nhuận</span>
                </div>
            </div>
            <div id="revenueChart" class="w-full" style="min-height: 320px;"></div>
        </div>

        <!-- Cột phải: Biểu đồ Trạng thái Đơn hàng (Doughnut) -->
        <div class="chart-card bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-blue-600"></i> Trạng thái Đơn hàng
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">Phân bổ theo trạng thái</p>
            </div>
            <div id="orderStatusChart" class="w-full flex justify-center" style="min-height: 280px;"></div>
            <!-- Legend thật từ DB (dùng id để JS cập nhật theo filter) -->
            <div class="flex flex-col gap-2 mt-4 px-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span><span class="text-gray-600 font-medium">Đã giao</span></span>
                    <span class="font-bold text-gray-800"><span id="status-delivered">{{ $stats['order_status']['delivered'] }}</span> đơn</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span><span class="text-gray-600 font-medium">Đang xử lý</span></span>
                    <span class="font-bold text-gray-800"><span id="status-processing">{{ $stats['order_status']['pending'] + $stats['order_status']['processing'] + $stats['order_status']['shipping'] }}</span> đơn</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span><span class="text-gray-600 font-medium">Đã hủy</span></span>
                    <span class="font-bold text-gray-800"><span id="status-cancelled">{{ $stats['order_status']['cancelled'] }}</span> đơn</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- PHẦN 4: BẢNG DỮ LIỆU (2 cột: 2/3 + 1/3) -->
    <!-- ============================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-4">

        <!-- Cột trái: Top 5 Sản phẩm bán chạy -->
        <div class="lg:col-span-2 chart-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-fire-flame-curved text-organic-500"></i> Top 5 Sản phẩm bán chạy
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Xếp hạng theo số lượt bán</p>
                </div>
                <a href="#" class="text-xs font-bold text-forest-600 hover:text-forest-800 transition-colors">Xem tất cả <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm">
                    <thead class="bg-cream-100/50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left font-bold">#</th>
                            <th class="px-6 py-3 text-left font-bold">Sản phẩm</th>
                            <th class="px-6 py-3 text-center font-bold">Lượt bán</th>
                            <th class="px-6 py-3 text-right font-bold">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($topProducts as $index => $product)
                            @php
                                $imgUrl = $product->image
                                    ? asset('storage/' . str_replace('storage/', '', $product->image))
                                    : null;
                                $totalSold = $product->order_details_count;
                                $totalRevenue = \App\Models\OrderDetail::where('product_id', $product->id)->sum(\DB::raw('quantity * price'));
                            @endphp
                            <tr class="hover:bg-forest-50/30 transition-colors">
                                <td class="px-6 py-3.5">
                                    <span class="w-7 h-7 rounded-lg {{ $index === 0 ? 'bg-organic-500 text-white' : 'bg-gray-200 text-gray-600' }} text-xs font-bold flex items-center justify-center {{ $index === 0 ? 'shadow-sm' : '' }}">{{ $index + 1 }}</span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-9 h-9 rounded-lg object-cover border border-gray-100">
                                        @else
                                            <div class="w-9 h-9 bg-forest-50 rounded-lg flex items-center justify-center text-forest-600"><i class="fa-solid fa-leaf text-sm"></i></div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $product->category->name ?? 'Chưa phân loại' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-center"><span class="font-bold text-gray-700">{{ $totalSold }}</span></td>
                                <td class="px-6 py-3.5 text-right font-mono font-bold text-forest-700">{{ number_format($totalRevenue, 0, ',', '.') }}₫</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">Chưa có dữ liệu bán hàng</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cột phải: Đơn hàng mới nhất -->
        <div class="chart-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-600"></i> Đơn hàng mới nhất
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Cập nhật realtime</p>
                </div>
                <a href="#" class="text-xs font-bold text-forest-600 hover:text-forest-800 transition-colors">Tất cả <i class="fa-solid fa-arrow-right ml-1"></i></a>
            </div>
            <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto custom-scrollbar">
                @forelse($recentOrders as $order)
                    @php
                        $isCancelled = $order->order_status === 'cancelled';
                        $statusBadge = match($order->order_status) {
                            'delivered' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'shipping' => 'bg-blue-100 text-blue-700 border-blue-200',
                            'ready_for_pickup' => 'bg-orange-100 text-orange-700 border-orange-200',
                            'processing' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'cancelled' => 'bg-red-100 text-red-700 border-red-200',
                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                        };
                        $statusLabel = match($order->order_status) {
                            'delivered' => 'Đã giao',
                            'shipping' => 'Đang giao',
                            'ready_for_pickup' => 'Chờ lấy hàng',
                            'processing' => 'Đang xử lý',
                            'pending' => 'Chờ xác nhận',
                            'cancelled' => 'Đã hủy',
                            default => $order->order_status,
                        };
                    @endphp
                    <div class="px-6 py-4 hover:bg-forest-50/30 transition-colors">
                        <div class="flex justify-between items-start mb-1.5">
                            <span class="font-mono font-bold text-sm text-gray-800">{{ $order->order_code }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusBadge }} border">{{ $statusLabel }}</span>
                        </div>
                        <p class="text-sm text-gray-600 font-medium">{{ $order->user->fullname ?? ($order->shipping_name ?? 'Khách vãng lai') }}</p>
                        <div class="flex justify-between items-center mt-1.5">
                            <span class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
                            <span class="font-mono font-bold text-sm {{ $isCancelled ? 'text-gray-400 line-through' : 'text-forest-700' }}">{{ number_format($order->total_money + $order->shipping_fee, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 italic">Chưa có đơn hàng nào</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // ============================================
    // DỮ LIỆU BAN ĐẦU (server-side, được render lần đầu)
    // ============================================
    const initialStats = @json($stats);
    const statsUrl = "{{ route('admin.dashboard.stats') }}";

    // ============================================
    // 1. COUNTER ANIMATION (chạy khi load lần đầu + khi filter thay đổi)
    // ============================================
    function animateCounter(counterEl, targetValue) {
        const suffix   = counterEl.getAttribute('data-suffix') || '';
        const duration = 900;
        const startValue = parseInt((counterEl.textContent.replace(/[^\d]/g, '')) || '0');
        const startTime  = performance.now();

        function update(currentTime) {
            const elapsed  = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease     = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            const value    = Math.floor(startValue + (targetValue - startValue) * ease);
            counterEl.textContent = value.toLocaleString('vi-VN') + suffix;

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                counterEl.textContent = targetValue.toLocaleString('vi-VN') + suffix;
                counterEl.setAttribute('data-target', targetValue);
            }
        }
        requestAnimationFrame(update);
    }

    function animateAllCounters() {
        document.querySelectorAll('.kpi-counter').forEach(el => {
            animateCounter(el, parseInt(el.getAttribute('data-target')) || 0);
        });
    }

    document.addEventListener('DOMContentLoaded', animateAllCounters);

    // ============================================
    // 2. BIỂU ĐỒ DOANH THU & LỢI NHUẬN (Area Chart) - dùng dữ liệu thật
    // ============================================
    const revenueOptions = {
        series: [
            { name: 'Doanh thu',  data: initialStats.chart.revenue },
            { name: 'Lợi nhuận', data: initialStats.chart.profit  }
        ],
        chart: {
            type: 'area',
            height: 320,
            fontFamily: 'Quicksand, sans-serif',
            toolbar: { show: false },
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#2e7d32', '#f9a825'],
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
        },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        xaxis: {
            categories: initialStats.chart.categories,
            labels: { style: { fontWeight: 600, colors: '#9ca3af' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { fontWeight: 600, colors: '#9ca3af' },
                formatter: function(val) {
                    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'tr';
                    if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                    return val;
                }
            }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { left: 10, right: 10 } },
        tooltip: {
            y: { formatter: function(val) { return (val || 0).toLocaleString('vi-VN') + '₫'; } }
        },
        legend: { show: false },
        noData: { text: 'Chưa có dữ liệu doanh thu trong khoảng thời gian này.' }
    };

    const revenueChart = new ApexCharts(document.querySelector('#revenueChart'), revenueOptions);
    revenueChart.render();

    // ============================================
    // 3. BIỂU ĐỒ TRẠNG THÁI ĐƠN HÀNG (Donut Chart)
    // ============================================
    function computeDonutSeries(orderStatus) {
        return [
            orderStatus.delivered,
            orderStatus.pending + orderStatus.processing + orderStatus.shipping,
            orderStatus.cancelled,
        ];
    }

    const orderStatusOptions = {
        series: computeDonutSeries(initialStats.order_status),
        chart: {
            type: 'donut',
            height: 260,
            fontFamily: 'Quicksand, sans-serif',
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#10b981', '#f59e0b', '#ef4444'],
        labels: ['Đã giao', 'Đang xử lý', 'Đã hủy'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name:  { show: true, fontSize: '14px', fontWeight: 700, color: '#374151' },
                        value: {
                            show: true, fontSize: '24px', fontWeight: 700, color: '#1f2937',
                            formatter: function(val) { return val + ' đơn'; }
                        },
                        total: {
                            show: true, label: 'Tổng đơn', fontSize: '12px', fontWeight: 600, color: '#9ca3af',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' đơn';
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        stroke: { width: 2, colors: ['#fff'] },
        legend: { show: false },
        tooltip: { y: { formatter: function(val) { return val + ' đơn'; } } },
        noData: { text: 'Không có đơn hàng trong khoảng thời gian này.' }
    };

    const orderStatusChart = new ApexCharts(document.querySelector('#orderStatusChart'), orderStatusOptions);
    orderStatusChart.render();

    // ============================================
    // 4. BỘ LỌC THỜI GIAN - AJAX cập nhật toàn bộ Dashboard
    // ============================================
    const timeFilter    = document.getElementById('timeFilter');
    const filterLoading = document.getElementById('filterLoading');

    async function fetchStats(period) {
        filterLoading.classList.remove('hidden');
        timeFilter.disabled = true;

        try {
            const res = await fetch(`${statsUrl}?period=${period}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Không tải được số liệu');
            const json = await res.json();
            if (!json.success) throw new Error('Phản hồi không hợp lệ');

            applyStats(json.data);
        } catch (err) {
            if (typeof showErrorNotification === 'function') {
                showErrorNotification('Không thể tải số liệu: ' + err.message);
            } else {
                alert('Không thể tải số liệu: ' + err.message);
            }
        } finally {
            filterLoading.classList.add('hidden');
            timeFilter.disabled = false;
        }
    }

    function applyStats(data) {
        // 1. Cập nhật nhãn "hôm nay / tuần này..."
        document.querySelectorAll('.kpi-period-label').forEach(el => {
            el.textContent = data.period_label;
        });

        // 2. Cập nhật KPI với animation
        const revenueEl   = document.getElementById('kpi-revenue');
        const ordersEl    = document.getElementById('kpi-orders');
        const customersEl = document.getElementById('kpi-customers');
        // Products là tổng tất cả, không đổi theo filter
        if (revenueEl)   animateCounter(revenueEl,   data.revenue);
        if (ordersEl)    animateCounter(ordersEl,    data.order_count);
        if (customersEl) animateCounter(customersEl, data.new_customers);

        // 3. Cập nhật legend donut
        const delEl  = document.getElementById('status-delivered');
        const procEl = document.getElementById('status-processing');
        const canEl  = document.getElementById('status-cancelled');
        if (delEl)  delEl.textContent  = data.order_status.delivered;
        if (procEl) procEl.textContent = data.order_status.pending + data.order_status.processing + data.order_status.shipping;
        if (canEl)  canEl.textContent  = data.order_status.cancelled;

        // 4. Cập nhật biểu đồ doanh thu
        revenueChart.updateOptions({ xaxis: { categories: data.chart.categories } }, false, true);
        revenueChart.updateSeries([
            { name: 'Doanh thu',  data: data.chart.revenue },
            { name: 'Lợi nhuận', data: data.chart.profit  },
        ], true);

        // 5. Cập nhật donut chart
        orderStatusChart.updateSeries(computeDonutSeries(data.order_status), true);
    }

    // Lắng nghe thay đổi bộ lọc
    timeFilter.addEventListener('change', function () {
        fetchStats(this.value);
    });
</script>
@endpush
