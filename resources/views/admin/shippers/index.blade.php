@extends('layouts.admin')

@section('title', 'Quản lý Shipper - Greenly Admin')

@section('page-title', 'Quản lý Shipper')

@push('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .order-tooltip-group:hover .order-tooltip {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
<div class="fade-in bg-cream-50 rounded-2xl relative min-h-[80vh]">

    <!-- 1. 4 STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8 mt-2">
        <!-- Card 1: Tổng Shipper -->
        <div class="bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-forest-800">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-users-gear text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider">Tổng nhân sự</p>
                    <div class="w-8 h-8 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/20"><i class="fa-solid fa-motorcycle text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $totalShippers }}</h3>
                    <span class="text-xs text-forest-100 font-medium">Shipper</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Sẵn sàng -->
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-emerald-500">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-motorcycle text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider">Đang chờ lệnh (Rảnh)</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-location-dot text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ str_pad($availableCount, 2, '0', STR_PAD_LEFT) }}</h3>
                    <span class="text-[10px] text-emerald-700 bg-white/90 px-2 py-0.5 rounded font-bold shadow-sm">Sẵn sàng gán đơn</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Đang giao -->
        <div class="bg-gradient-to-br from-orange-500 to-yellow-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-orange-400">
            <div class="absolute -right-4 -bottom-6 opacity-20 group-hover:scale-110 transition-transform duration-500 transform -rotate-12">
                <i class="fa-solid fa-motorcycle text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-orange-50 text-[11px] font-bold uppercase tracking-wider">Đang trên đường</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-route text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ str_pad($onDeliveryCount, 2, '0', STR_PAD_LEFT) }}</h3>
                    <span class="text-xs text-yellow-100 font-medium">Người</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Đã giao hôm nay -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-400 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-blue-500">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-box-open text-8xl"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-100 text-[11px] font-bold uppercase tracking-wider">Hiệu suất hôm nay</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-check-double text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $completedTodayCount }}</h3>
                    <span class="text-xs text-blue-100 font-medium">Đơn đã giao xong</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TOOLBAR: TABS & SEARCH -->
    <form method="GET" id="shipperFilterForm" action="{{ route('admin.shippers.index') }}" class="flex flex-col xl:flex-row justify-between items-center gap-4 mb-6">
        <!-- Tabs Navigation -->
        <div class="w-full xl:w-auto overflow-x-auto pb-2 xl:pb-0 hide-scrollbar">
            @php
                $currentStatus = request('work_status', '');
                $tabs = [
                    '' => ['label' => 'Tất cả', 'badge' => null, 'color' => ''],
                    'available' => ['label' => 'Sẵn sàng', 'badge' => $availableCount, 'color' => 'bg-green-500'],
                    'on_delivery' => ['label' => 'Đang giao hàng', 'badge' => $onDeliveryCount, 'color' => 'bg-orange-500'],
                    'offline' => ['label' => 'Nghỉ phép', 'badge' => $offlineCount, 'color' => 'bg-gray-500'],
                ];
            @endphp
            <div class="inline-flex bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm items-center min-w-max gap-1">
                @foreach($tabs as $statusKey => $tab)
                    <a href="{{ route('admin.shippers.index', array_merge(request()->except('work_status', 'page'), $statusKey ? ['work_status' => $statusKey] : [])) }}"
                       class="px-5 py-2 rounded-lg text-sm transition-all focus:outline-none flex items-center gap-1.5
                              {{ $currentStatus === $statusKey ? 'bg-forest-100 text-forest-700 font-bold' : 'text-gray-600 font-medium hover:bg-gray-50 hover:text-forest-700' }}">
                        {{ $tab['label'] }}
                        @if($tab['badge'] && $tab['badge'] > 0)
                            <span class="{{ $tab['color'] }} text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $tab['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Search Box -->
        <div class="w-full xl:w-80 relative group shrink-0">
            <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500 transition-all">
                <i class="fa-solid fa-search text-gray-400 mr-3 group-focus-within:text-forest-500 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên Shipper, SĐT..."
                    class="bg-transparent text-sm text-gray-700 outline-none w-full placeholder-gray-400">
            </div>
            @if(request('work_status'))
                <input type="hidden" name="work_status" value="{{ request('work_status') }}">
            @endif
        </div>
    </form>

    <!-- 3. BẢNG ĐIỀU PHỐI (DISPATCH TABLE) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-[11px] font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Shipper</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-center">Đơn đang giữ</th>
                        <th class="px-6 py-4 text-center">Đã giao (Tháng)</th>
                        <th class="px-6 py-4 text-center">Tỷ lệ thành công</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($shippers as $shipper)
                        @php
                            $isOffline = $shipper->work_status === 'offline';
                            $isOnDelivery = $shipper->work_status === 'on_delivery';
                            $deliveringCount = $deliveringCounts[$shipper->id] ?? 0;
                            $monthlyTotal = $monthlyTotalCounts[$shipper->id] ?? 0;
                            $monthlyCompleted = $monthlyCompletedCounts[$shipper->id] ?? 0;
                            $successRate = $monthlyTotal > 0 ? round(($monthlyCompleted / $monthlyTotal) * 100) : 0;
                            $shipperOrders = $deliveringOrders[$shipper->id] ?? collect();
                            $avatarUrl = $shipper->avatar ? asset('storage/' . $shipper->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($shipper->fullname) . '&background=random&size=40';

                            // Màu status dot
                            $dotColor = match($shipper->work_status) {
                                'available' => 'bg-green-500',
                                'on_delivery' => 'bg-orange-500',
                                default => 'bg-gray-400',
                            };
                        @endphp
                        <tr class="{{ $isOffline ? 'bg-gray-50/50 hover:bg-gray-100/50' : 'hover:bg-forest-50/30' }} transition-colors group">
                            <!-- Cột: Shipper Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 {{ $isOffline ? 'opacity-60' : '' }}">
                                    <div class="relative">
                                        <img src="{{ $avatarUrl }}" alt="{{ $shipper->fullname }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm {{ $isOffline ? 'grayscale' : '' }}">
                                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 {{ $dotColor }} border-2 border-white rounded-full"></span>
                                    </div>
                                    <div>
                                        <div class="font-bold {{ $isOffline ? 'text-gray-500' : 'text-gray-800 group-hover:text-forest-700' }} text-sm transition-colors">{{ $shipper->fullname }}</div>
                                        <div class="text-[11px] {{ $isOffline ? 'text-gray-400' : 'text-gray-500' }} mt-1"><i class="fa-solid fa-phone text-[9px] mr-1"></i>{{ $shipper->phone ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Cột: Trạng thái -->
                            <td class="px-6 py-4 text-center {{ $isOffline ? 'opacity-60' : '' }}">
                                @if($shipper->work_status === 'available')
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 w-24">
                                        <i class="fa-solid fa-mug-hot mr-1"></i> Sẵn sàng
                                    </span>
                                @elseif($shipper->work_status === 'on_delivery')
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-200 w-24">
                                        <i class="fa-solid fa-motorcycle mr-1 animate-bounce"></i> Đang giao
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-gray-200 text-gray-600 border border-gray-300 w-24">
                                        <i class="fa-solid fa-bed mr-1"></i> Nghỉ phép
                                    </span>
                                @endif
                            </td>

                            <!-- Cột: Đơn đang giữ -->
                            <td class="px-6 py-4 text-center {{ $isOffline ? 'opacity-60' : '' }}">
                                @if($deliveringCount > 0)
                                    <div class="relative inline-block order-tooltip-group cursor-help">
                                        <span class="font-bold {{ $deliveringCount >= 5 ? 'text-red-600 bg-red-50 border-red-100' : 'text-orange-600 bg-orange-50 border-orange-100' }} px-2 py-1 rounded border border-dashed">
                                            {{ str_pad($deliveringCount, 2, '0', STR_PAD_LEFT) }} đơn{{ $deliveringCount >= 5 ? ' (Full)' : '' }}
                                        </span>
                                        @if($shipperOrders->count() > 0)
                                            <div class="order-tooltip absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-40 bg-gray-800 text-white text-xs rounded-lg p-2 shadow-xl opacity-0 invisible translate-y-2 transition-all duration-200 z-10 before:content-[''] before:absolute before:top-full before:left-1/2 before:-translate-x-1/2 before:border-4 before:border-transparent before:border-t-gray-800">
                                                <div class="font-bold text-gray-300 border-b border-gray-600 pb-1 mb-1 text-left">Đang giữ mã:</div>
                                                @if($shipperOrders->count() <= 4)
                                                    <ul class="text-left space-y-1 font-mono text-[10px]">
                                                        @foreach($shipperOrders as $so)
                                                            <li class="flex justify-between"><span>{{ $so->order_code }}</span> <i class="fa-solid fa-clock text-orange-400"></i></li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <div class="text-left text-[10px] italic text-gray-400">(Quá nhiều đơn, không hiển thị hết)</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 font-medium italic">-</span>
                                @endif
                            </td>

                            <!-- Cột: Đã giao (Tháng) -->
                            <td class="px-6 py-4 text-center {{ $isOffline ? 'opacity-60' : '' }}">
                                <div class="font-bold {{ $isOffline ? 'text-gray-500' : 'text-gray-800' }}">{{ $monthlyTotal }} <span class="text-[10px] font-normal {{ $isOffline ? 'text-gray-400' : 'text-gray-500' }}">đơn</span></div>
                            </td>

                            <!-- Cột: Tỷ lệ thành công -->
                            <td class="px-6 py-4 text-center {{ $isOffline ? 'opacity-60' : '' }}">
                                @php
                                    if ($successRate >= 95) {
                                        $rateClass = 'bg-green-50 text-green-700';
                                        $rateIcon = 'fa-arrow-trend-up';
                                    } elseif ($successRate >= 85) {
                                        $rateClass = 'bg-yellow-50 text-yellow-700';
                                        $rateIcon = 'fa-minus';
                                    } else {
                                        $rateClass = 'bg-red-50 text-red-600';
                                        $rateIcon = 'fa-arrow-trend-down';
                                    }
                                    if ($isOffline) {
                                        $rateClass = 'bg-gray-200 text-gray-500';
                                        $rateIcon = 'fa-minus';
                                    }
                                @endphp
                                <div class="inline-flex items-center gap-1.5 {{ $rateClass }} px-2 py-0.5 rounded font-bold text-xs">
                                    {{ $successRate }}% <i class="fa-solid {{ $rateIcon }} text-[10px]"></i>
                                </div>
                            </td>

                            <!-- Cột: Hành động -->
                            <td class="px-6 py-4 text-right">
                                @include('admin.shippers.partials.action-buttons', ['shipper' => $shipper, 'isOffline' => $isOffline, 'isOnDelivery' => $isOnDelivery, 'deliveringCount' => $deliveringCount])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <i class="fa-solid fa-motorcycle text-6xl text-gray-200 mb-4"></i>
                                <p class="text-gray-500 text-lg font-bold">Chưa có Shipper nào</p>
                                <p class="text-gray-400 text-sm mt-1">Thêm nhân sự giao hàng trong mục Quản lý Người dùng.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        @if($shippers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center bg-white rounded-b-2xl">
                <span class="text-sm text-gray-500 font-medium">Hiển thị {{ $shippers->firstItem() }} - {{ $shippers->lastItem() }} trên tổng số {{ $shippers->total() }} nhân sự</span>
                {{ $shippers->links('vendor.pagination.greenly') }}
            </div>
        @endif
    </div>

</div>

{{-- Include Modal Chi tiết --}}
@include('admin.shippers.partials.show-modal')

@endsection

@push('scripts')
<script>
    // ============================================
    // MODAL XEM CHI TIẾT SHIPPER (AJAX)
    // ============================================
    const modal = document.getElementById('shipperDetailModal');
    const modalContent = document.getElementById('shipperModalContent');

    function openShipperModal(shipperId) {
        // Hiển thị modal + loading
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');

        document.getElementById('modalBody').innerHTML = '<div class="flex items-center justify-center py-20"><i class="fa-solid fa-spinner fa-spin text-3xl text-forest-600"></i></div>';
        document.getElementById('modalHeader').innerHTML = '<div class="animate-pulse flex items-center gap-4"><div class="w-16 h-16 bg-white/20 rounded-full"></div><div class="space-y-2"><div class="h-5 bg-white/20 rounded w-32"></div><div class="h-3 bg-white/20 rounded w-48"></div></div></div>';

        // Gọi API
        fetch('/admin/shippers/' + shipperId, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => renderShipperModal(data))
        .catch(err => {
            document.getElementById('modalBody').innerHTML = '<div class="text-center py-10 text-red-500"><i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i><p>Lỗi tải dữ liệu</p></div>';
        });
    }

    function closeShipperModal() {
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    function renderShipperModal(data) {
        const s = data.shipper;
        const st = data.stats;
        const orders = data.orders;

        // Trạng thái badge
        const statusMap = {
            'available': '<span class="px-2 py-0.5 bg-green-500 text-white text-[10px] font-bold rounded">Sẵn sàng</span>',
            'on_delivery': '<span class="px-2 py-0.5 bg-orange-500 text-white text-[10px] font-bold rounded">Đang giao</span>',
            'offline': '<span class="px-2 py-0.5 bg-gray-500 text-white text-[10px] font-bold rounded">Nghỉ phép</span>',
        };

        // Header
        document.getElementById('modalHeader').innerHTML = `
            <div class="flex items-center gap-4">
                <img src="${s.avatar}" alt="${s.fullname}" class="w-16 h-16 rounded-full object-cover border-4 border-white shadow-md">
                <div class="text-white">
                    <h2 class="text-xl font-bold">${s.fullname}</h2>
                    <p class="text-sm text-forest-100 flex items-center gap-2 mt-1">
                        <i class="fa-solid fa-phone"></i> ${s.phone || 'N/A'}
                        ${statusMap[s.work_status] || ''}
                    </p>
                </div>
            </div>`;

        // Tính tiền COD hiển thị
        const codDisplay = st.cod_holding >= 1000000
            ? (st.cod_holding / 1000000).toFixed(1).replace('.', ',') + ' Tr'
            : st.cod_holding >= 1000
            ? Math.round(st.cod_holding / 1000) + 'k'
            : st.cod_holding.toLocaleString('vi-VN') + 'đ';

        // Stars cho rating
        let starsHtml = '';
        if (st.avg_rating > 0) {
            starsHtml = st.avg_rating + ' <i class="fa-solid fa-star text-sm"></i>';
        } else {
            starsHtml = '- <span class="text-xs font-normal">chưa có</span>';
        }

        // Danh sách đơn hàng
        let ordersHtml = '';
        if (orders.length > 0) {
            orders.forEach(o => {
                const isCancelled = o.order_status === 'cancelled';
                const statusBadge = isCancelled
                    ? '<span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded font-bold">Boom hàng</span>'
                    : o.order_status === 'completed'
                    ? '<span class="bg-green-100 text-green-600 text-[10px] px-2 py-1 rounded font-bold">Thành công</span>'
                    : '<span class="bg-orange-100 text-orange-600 text-[10px] px-2 py-1 rounded font-bold">Đang giao</span>';

                const codAmount = o.payment_method === 'COD' && !isCancelled
                    ? '<span class="text-red-600">' + Number(o.final_amount).toLocaleString('vi-VN') + 'đ</span>'
                    : isCancelled
                    ? '<span class="text-gray-400">-</span>'
                    : '<span class="text-gray-400">Đã thanh toán (0đ)</span>';

                ordersHtml += `
                    <tr>
                        <td class="px-4 py-3 font-bold text-gray-700 ${isCancelled ? 'line-through' : ''}">${o.order_code}</td>
                        <td class="px-4 py-3 text-gray-600">${o.created_at}</td>
                        <td class="px-4 py-3 ${isCancelled ? 'text-gray-400 italic' : 'text-gray-600'}">${o.delivery_date || (isCancelled ? 'Đã hủy' : 'Đang giao...')}</td>
                        <td class="px-4 py-3 text-right font-medium">${codAmount}</td>
                        <td class="px-4 py-3 text-center">${statusBadge}</td>
                    </tr>`;
            });
        } else {
            ordersHtml = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">Chưa có đơn hàng nào hôm nay</td></tr>';
        }

        // Body
        document.getElementById('modalBody').innerHTML = `
            <!-- Thống kê ca làm việc -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-cream-50 p-4 rounded-xl border border-gray-100 text-center">
                    <div class="text-xs text-gray-500 font-bold uppercase mb-1">Đơn đã giao hôm nay</div>
                    <div class="text-2xl font-bold text-forest-700">${st.completed_today}</div>
                </div>
                <div class="bg-cream-50 p-4 rounded-xl border border-gray-100 text-center">
                    <div class="text-xs text-gray-500 font-bold uppercase mb-1">Tiền COD đang giữ</div>
                    <div class="text-2xl font-bold text-red-600">${codDisplay}</div>
                </div>
                <div class="bg-cream-50 p-4 rounded-xl border border-gray-100 text-center">
                    <div class="text-xs text-gray-500 font-bold uppercase mb-1">Đánh giá khách hàng</div>
                    <div class="text-2xl font-bold text-organic-500 flex items-center justify-center gap-1">${starsHtml}</div>
                </div>
            </div>

            <h3 class="font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">Chuyến hàng hôm nay (Today's Routes)</h3>

            <!-- Bảng Lịch trình -->
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 text-[11px] font-bold">
                        <tr>
                            <th class="px-4 py-3">Mã đơn</th>
                            <th class="px-4 py-3">Nhận đơn lúc</th>
                            <th class="px-4 py-3">Giao xong lúc</th>
                            <th class="px-4 py-3 text-right">Tiền thu hộ (COD)</th>
                            <th class="px-4 py-3 text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">${ordersHtml}</tbody>
                </table>
            </div>`;
    }
</script>
@endpush
