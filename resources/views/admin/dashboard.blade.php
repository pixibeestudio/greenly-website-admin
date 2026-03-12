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
        <div class="relative">
            <select id="timeFilter" class="bg-white border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm font-bold text-gray-600 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all shadow-sm appearance-none cursor-pointer">
                <option value="today">Hôm nay</option>
                <option value="week">Tuần này</option>
                <option value="month" selected>Tháng này</option>
                <option value="year">Năm nay</option>
            </select>
            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- PHẦN 2: KPI CARDS (4 cột) -->
    <!-- ============================================ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Card 1: Tổng Doanh Thu -->
        <div class="kpi-card bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-sack-dollar text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng Doanh Thu</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" data-target="25400000" data-suffix="đ" data-format="currency">0đ</span>
                    </h3>
                    <p class="text-forest-200 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-organic-400"></i>
                        <span class="font-bold text-organic-400">+12.5%</span> so với kỳ trước
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-organic-400 text-xl shadow-inner">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Tổng Lợi Nhuận -->
        <div class="kpi-card bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-chart-line text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng Lợi Nhuận</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" data-target="8200000" data-suffix="đ" data-format="currency">0đ</span>
                    </h3>
                    <p class="text-emerald-100 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-yellow-300"></i>
                        <span class="font-bold text-yellow-300">+8.3%</span> so với kỳ trước
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-yellow-300 text-xl shadow-inner">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Tổng Đơn Hàng -->
        <div class="kpi-card bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-boxes-stacked text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng Đơn Hàng</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" data-target="156" data-suffix=" đơn" data-format="number">0 đơn</span>
                    </h3>
                    <p class="text-blue-100 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-sky-300"></i>
                        <span class="font-bold text-sky-300">+5.2%</span> so với kỳ trước
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-sky-300 text-xl shadow-inner">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>

        <!-- Card 4: Khách Hàng Mới -->
        <div class="kpi-card bg-gradient-to-br from-violet-700 via-violet-600 to-violet-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-user-plus text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-violet-100 text-[11px] font-bold uppercase tracking-wider mb-1">Khách Hàng Mới</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">
                        <span class="kpi-counter" data-target="42" data-suffix=" người" data-format="number">0 người</span>
                    </h3>
                    <p class="text-violet-100 text-xs mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-down text-red-300"></i>
                        <span class="font-bold text-red-300">-2.1%</span> so với kỳ trước
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-violet-200 text-xl shadow-inner">
                    <i class="fa-solid fa-user-plus"></i>
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
            <!-- Legend thủ công -->
            <div class="flex flex-col gap-2 mt-4 px-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span><span class="text-gray-600 font-medium">Đã giao</span></span>
                    <span class="font-bold text-gray-800">98 đơn</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span><span class="text-gray-600 font-medium">Đang xử lý</span></span>
                    <span class="font-bold text-gray-800">38 đơn</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span><span class="text-gray-600 font-medium">Đã hủy</span></span>
                    <span class="font-bold text-gray-800">20 đơn</span>
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
                        <tr class="hover:bg-forest-50/30 transition-colors">
                            <td class="px-6 py-3.5"><span class="w-7 h-7 rounded-lg bg-organic-500 text-white text-xs font-bold flex items-center justify-center shadow-sm">1</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-forest-50 rounded-lg flex items-center justify-center text-forest-600"><i class="fa-solid fa-leaf text-sm"></i></div>
                                    <div>
                                        <p class="font-bold text-gray-800">Cà chua Organic Đà Lạt</p>
                                        <p class="text-xs text-gray-400">Rau củ quả</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-center"><span class="font-bold text-gray-700">320</span></td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-forest-700">8.960.000₫</td>
                        </tr>
                        <tr class="hover:bg-forest-50/30 transition-colors">
                            <td class="px-6 py-3.5"><span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-600 text-xs font-bold flex items-center justify-center">2</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600"><i class="fa-solid fa-seedling text-sm"></i></div>
                                    <div>
                                        <p class="font-bold text-gray-800">Rau cải xoăn Kale</p>
                                        <p class="text-xs text-gray-400">Rau lá xanh</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-center"><span class="font-bold text-gray-700">275</span></td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-forest-700">6.875.000₫</td>
                        </tr>
                        <tr class="hover:bg-forest-50/30 transition-colors">
                            <td class="px-6 py-3.5"><span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-600 text-xs font-bold flex items-center justify-center">3</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600"><i class="fa-solid fa-carrot text-sm"></i></div>
                                    <div>
                                        <p class="font-bold text-gray-800">Cà rốt hữu cơ</p>
                                        <p class="text-xs text-gray-400">Rau củ quả</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-center"><span class="font-bold text-gray-700">210</span></td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-forest-700">4.200.000₫</td>
                        </tr>
                        <tr class="hover:bg-forest-50/30 transition-colors">
                            <td class="px-6 py-3.5"><span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-600 text-xs font-bold flex items-center justify-center">4</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-yellow-50 rounded-lg flex items-center justify-center text-yellow-600"><i class="fa-solid fa-lemon text-sm"></i></div>
                                    <div>
                                        <p class="font-bold text-gray-800">Bưởi da xanh Bến Tre</p>
                                        <p class="text-xs text-gray-400">Trái cây</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-center"><span class="font-bold text-gray-700">185</span></td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-forest-700">5.550.000₫</td>
                        </tr>
                        <tr class="hover:bg-forest-50/30 transition-colors">
                            <td class="px-6 py-3.5"><span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-600 text-xs font-bold flex items-center justify-center">5</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center text-green-600"><i class="fa-solid fa-pepper-hot text-sm"></i></div>
                                    <div>
                                        <p class="font-bold text-gray-800">Ớt chuông hữu cơ</p>
                                        <p class="text-xs text-gray-400">Rau củ quả</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-center"><span class="font-bold text-gray-700">150</span></td>
                            <td class="px-6 py-3.5 text-right font-mono font-bold text-forest-700">3.750.000₫</td>
                        </tr>
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
                <!-- Đơn 1 -->
                <div class="px-6 py-4 hover:bg-forest-50/30 transition-colors">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-mono font-bold text-sm text-gray-800">#ORD-001</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-200">Đã giao</span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Nguyễn Văn An</p>
                    <div class="flex justify-between items-center mt-1.5">
                        <span class="text-xs text-gray-400">2 phút trước</span>
                        <span class="font-mono font-bold text-sm text-forest-700">450.000₫</span>
                    </div>
                </div>
                <!-- Đơn 2 -->
                <div class="px-6 py-4 hover:bg-forest-50/30 transition-colors">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-mono font-bold text-sm text-gray-800">#ORD-002</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-200">Đang xử lý</span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Trần Thị Bích</p>
                    <div class="flex justify-between items-center mt-1.5">
                        <span class="text-xs text-gray-400">15 phút trước</span>
                        <span class="font-mono font-bold text-sm text-forest-700">1.230.000₫</span>
                    </div>
                </div>
                <!-- Đơn 3 -->
                <div class="px-6 py-4 hover:bg-forest-50/30 transition-colors">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-mono font-bold text-sm text-gray-800">#ORD-003</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-200">Đã giao</span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Lê Hoàng Minh</p>
                    <div class="flex justify-between items-center mt-1.5">
                        <span class="text-xs text-gray-400">1 giờ trước</span>
                        <span class="font-mono font-bold text-sm text-forest-700">780.000₫</span>
                    </div>
                </div>
                <!-- Đơn 4 -->
                <div class="px-6 py-4 hover:bg-forest-50/30 transition-colors">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-mono font-bold text-sm text-gray-800">#ORD-004</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700 border border-red-200">Đã hủy</span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Phạm Quốc Đạt</p>
                    <div class="flex justify-between items-center mt-1.5">
                        <span class="text-xs text-gray-400">2 giờ trước</span>
                        <span class="font-mono font-bold text-sm text-gray-400 line-through">320.000₫</span>
                    </div>
                </div>
                <!-- Đơn 5 -->
                <div class="px-6 py-4 hover:bg-forest-50/30 transition-colors">
                    <div class="flex justify-between items-start mb-1.5">
                        <span class="font-mono font-bold text-sm text-gray-800">#ORD-005</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-200">Đang xử lý</span>
                    </div>
                    <p class="text-sm text-gray-600 font-medium">Võ Thị Hương</p>
                    <div class="flex justify-between items-center mt-1.5">
                        <span class="text-xs text-gray-400">3 giờ trước</span>
                        <span class="font-mono font-bold text-sm text-forest-700">2.100.000₫</span>
                    </div>
                </div>
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
    // 1. HIỆU ỨNG NUMBER COUNTER CHO KPI CARDS
    // ============================================
    function animateCounters() {
        const counters = document.querySelectorAll('.kpi-counter');
        const duration = 1500;

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const suffix = counter.getAttribute('data-suffix') || '';
            const format = counter.getAttribute('data-format');
            const startTime = performance.now();

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                // Easing: ease-out cubic
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(easeOut * target);

                if (format === 'currency') {
                    counter.textContent = current.toLocaleString('vi-VN') + suffix;
                } else {
                    counter.textContent = current.toLocaleString('vi-VN') + suffix;
                }

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString('vi-VN') + suffix;
                }
            }

            requestAnimationFrame(updateCounter);
        });
    }

    // Chạy counter khi trang load xong
    document.addEventListener('DOMContentLoaded', animateCounters);

    // ============================================
    // 2. BIỂU ĐỒ DOANH THU & LỢI NHUẬN (Area Chart)
    // ============================================
    const revenueOptions = {
        series: [
            {
                name: 'Doanh thu',
                data: [3200000, 4100000, 3800000, 4500000, 3600000, 2900000, 3300000]
            },
            {
                name: 'Lợi nhuận',
                data: [1100000, 1500000, 1200000, 1800000, 1300000, 900000, 1400000]
            }
        ],
        chart: {
            type: 'area',
            height: 320,
            fontFamily: 'Quicksand, sans-serif',
            toolbar: { show: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 1000,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            }
        },
        colors: ['#2e7d32', '#f9a825'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
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
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            padding: { left: 10, right: 10 }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toLocaleString('vi-VN') + '₫';
                }
            }
        },
        legend: { show: false }
    };

    const revenueChart = new ApexCharts(document.querySelector('#revenueChart'), revenueOptions);
    revenueChart.render();

    // ============================================
    // 3. BIỂU ĐỒ TRẠNG THÁI ĐƠN HÀNG (Donut Chart)
    // ============================================
    const orderStatusOptions = {
        series: [98, 38, 20],
        chart: {
            type: 'donut',
            height: 260,
            fontFamily: 'Quicksand, sans-serif',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 1200,
                animateGradually: { enabled: true, delay: 200 }
            }
        },
        colors: ['#10b981', '#f59e0b', '#ef4444'],
        labels: ['Đã giao', 'Đang xử lý', 'Đã hủy'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 700,
                            color: '#374151'
                        },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontWeight: 700,
                            color: '#1f2937',
                            formatter: function(val) { return val + ' đơn'; }
                        },
                        total: {
                            show: true,
                            label: 'Tổng đơn',
                            fontSize: '12px',
                            fontWeight: 600,
                            color: '#9ca3af',
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
        tooltip: {
            y: {
                formatter: function(val) { return val + ' đơn'; }
            }
        }
    };

    const orderStatusChart = new ApexCharts(document.querySelector('#orderStatusChart'), orderStatusOptions);
    orderStatusChart.render();
</script>
@endpush
