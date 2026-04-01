@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng - Greenly Admin')

@section('page-title', 'Quản lý Đơn hàng')

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="fade-in bg-cream-50 rounded-2xl relative min-h-[80vh]">

    <!-- 1. 4 CARD THỐNG KÊ -->
    <div class="flex flex-nowrap lg:grid lg:grid-cols-4 gap-6 mb-8 overflow-x-auto pb-4 custom-scrollbar">
        <!-- Card 1: Cần xác nhận -->
        <div class="min-w-[250px] bg-gradient-to-br from-orange-500 to-yellow-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-20 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-headset text-8xl"></i>
            </div>
            <div class="relative z-[1] flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-orange-50 text-[11px] font-bold uppercase tracking-wider">Cần xác nhận</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-phone text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $pendingCount }}</h3>
                    <span class="text-xs text-yellow-100 font-medium">Đơn mới</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Đang giao hàng -->
        <div class="min-w-[250px] bg-gradient-to-br from-blue-600 to-blue-400 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-20 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-motorcycle text-8xl"></i>
            </div>
            <div class="relative z-[1] flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-100 text-[11px] font-bold uppercase tracking-wider">Đang giao hàng</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-truck-fast text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $shippingCount }}</h3>
                    <span class="text-xs text-blue-100 font-medium">Trên đường</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Hôm nay (Đã giao) & Doanh thu -->
        <div class="min-w-[250px] bg-gradient-to-br from-emerald-700 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-20 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-sack-dollar text-8xl"></i>
            </div>
            <div class="relative z-[1] flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider">Hôm nay (Đã giao)</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-check-double text-sm"></i></div>
                </div>
                <div>
                    <div class="flex items-baseline gap-2 mb-1">
                        @php
                            $revenueDisplay = $deliveredTodayRevenue >= 1000000
                                ? number_format($deliveredTodayRevenue / 1000000, 1, ',', '.') . ' Tr'
                                : number_format($deliveredTodayRevenue, 0, ',', '.');
                        @endphp
                        <h3 class="text-2xl font-bold text-white drop-shadow-md">{{ $revenueDisplay }}</h3>
                        <span class="text-xs text-emerald-100 font-medium">VNĐ</span>
                    </div>
                    <div class="text-[11px] text-white/90 bg-black/10 inline-block px-2 py-0.5 rounded backdrop-blur-md">
                        {{ $deliveredTodayCount }} đơn hoàn tất
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Đơn hủy -->
        <div class="min-w-[250px] bg-gradient-to-br from-slate-600 to-gray-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-20 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-file-circle-xmark text-8xl"></i>
            </div>
            <div class="relative z-[1] flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-200 text-[11px] font-bold uppercase tracking-wider">Đơn hủy (Rớt đơn)</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-ban text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ str_pad($cancelledCount, 2, '0', STR_PAD_LEFT) }}</h3>
                    <span class="text-xs text-gray-200 font-medium">Đơn</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TOOLBAR: TABS & SEARCH -->
    <form method="GET" id="orderFilterForm" action="{{ route('admin.orders.index') }}" class="flex flex-col xl:flex-row justify-between items-center gap-4 mb-6">
        <!-- Tabs Navigation -->
        <div class="w-full xl:w-auto overflow-x-auto pb-2 xl:pb-0 hide-scrollbar">
            <div class="inline-flex bg-gray-200/60 p-1.5 rounded-xl border border-gray-200 items-center min-w-max">
                @php
                    $currentStatus = request('status', '');
                    $tabs = [
                        '' => ['label' => 'Tất cả', 'badge' => null, 'color' => ''],
                        'pending' => ['label' => 'Chờ xác nhận', 'badge' => $statusCounts['pending'] ?? 0, 'color' => 'bg-orange-500'],
                        'processing' => ['label' => 'Đang xử lý', 'badge' => $statusCounts['processing'] ?? 0, 'color' => 'bg-purple-500'],
                        'ready_for_pickup' => ['label' => 'Chờ lấy hàng', 'badge' => $statusCounts['ready_for_pickup'] ?? 0, 'color' => 'bg-cyan-500'],
                        'shipping' => ['label' => 'Đang giao', 'badge' => $statusCounts['shipping'] ?? 0, 'color' => 'bg-blue-500'],
                        'delivered' => ['label' => 'Đã giao', 'badge' => $statusCounts['delivered'] ?? 0, 'color' => 'bg-green-600'],
                        'cancelled' => ['label' => 'Đã hủy', 'badge' => $statusCounts['cancelled'] ?? 0, 'color' => 'bg-gray-500'],
                    ];
                @endphp
                @foreach($tabs as $statusKey => $tab)
                    <a href="{{ route('admin.orders.index', array_merge(request()->except('status', 'page'), $statusKey ? ['status' => $statusKey] : [])) }}"
                       class="px-5 py-2 rounded-lg text-sm transition-all focus:outline-none flex items-center gap-1.5
                              {{ $currentStatus === $statusKey ? 'bg-white text-forest-700 font-bold shadow-sm' : 'text-gray-600 font-medium hover:text-forest-700' }}">
                        {{ $tab['label'] }}
                        @if($tab['badge'] && $tab['badge'] > 0)
                            <span class="{{ $tab['color'] }} text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $tab['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Search Box & Date Filter -->
        <div class="flex items-center gap-3 w-full xl:w-auto shrink-0">
            <div class="relative group w-full xl:w-72">
                <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500 transition-all">
                    <i class="fa-solid fa-search text-gray-400 mr-3 group-focus-within:text-forest-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm Mã đơn, SĐT, tên KH..."
                        class="bg-transparent text-sm text-gray-700 outline-none w-full placeholder-gray-400">
                </div>
            </div>
            <input type="date" name="date" value="{{ request('date') }}"
                onchange="document.getElementById('orderFilterForm').submit();"
                class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
        </div>
    </form>

    <!-- 3. BẢNG DỮ LIỆU ĐƠN HÀNG -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-[11px] font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Mã ĐH & Thời gian</th>
                        <th class="px-6 py-4">Khách hàng</th>
                        <th class="px-6 py-4">Thanh toán</th>
                        <th class="px-6 py-4">Trạng thái TT</th>
                        <th class="px-6 py-4">Trạng thái ĐH</th>
                        <th class="px-6 py-4">Shipper</th>
                        <th class="px-6 py-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $order)
                        <tr class="{{ $order->order_status === 'cancelled' ? 'bg-gray-50/50 hover:bg-gray-100/50' : 'hover:bg-forest-50/30' }} transition-colors group">
                            <!-- Cột 1: Mã ĐH & Thời gian -->
                            <td class="px-6 py-4">
                                <div class="font-bold {{ $order->order_status === 'cancelled' ? 'text-gray-400 line-through' : ($order->order_status === 'pending' ? 'text-forest-700' : 'text-gray-700') }} text-sm">{{ $order->order_code }}</div>
                                <div class="text-[11px] text-gray-500 mt-1"><i class="fa-regular fa-clock mr-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <!-- Cột 2: Khách hàng -->
                            <td class="px-6 py-4 {{ $order->order_status === 'cancelled' ? 'opacity-70' : '' }}">
                                <div class="font-bold text-gray-800">{{ $order->shipping_name ?? ($order->user->name ?? 'N/A') }}</div>
                                <div class="text-[11px] text-gray-500 mt-1"><i class="fa-solid fa-phone text-[9px] mr-1"></i>{{ $order->shipping_phone ?? 'N/A' }}</div>
                            </td>
                            <!-- Cột 3: Thanh toán -->
                            <td class="px-6 py-4 {{ $order->order_status === 'cancelled' ? 'opacity-70' : '' }}">
                                <div class="font-bold {{ $order->payment_status === 'completed' ? 'text-green-600' : 'text-red-600' }} text-sm">{{ number_format($order->final_amount, 0, ',', '.') }}đ</div>
                                @php
                                    $methodColors = [
                                        'COD' => 'text-gray-500 bg-gray-100',
                                        'bank_transfer' => 'text-blue-600 bg-blue-50 border border-blue-100',
                                        'vnpay' => 'text-blue-600 bg-blue-50 border border-blue-100',
                                        'momo' => 'text-pink-600 bg-pink-50 border border-pink-100',
                                    ];
                                    $methodLabels = [
                                        'COD' => 'COD',
                                        'bank_transfer' => 'Chuyển khoản',
                                        'vnpay' => 'VNPAY',
                                        'momo' => 'MoMo',
                                    ];
                                    $pmClass = $methodColors[$order->payment_method] ?? 'text-gray-500 bg-gray-100';
                                    $pmLabel = $methodLabels[$order->payment_method] ?? $order->payment_method;
                                @endphp
                                <div class="text-[10px] font-bold {{ $pmClass }} px-1.5 py-0.5 rounded inline-block mt-1">{{ $pmLabel }}</div>
                            </td>
                            <!-- Cột 4: Trạng thái Thanh toán -->
                            <td class="px-6 py-4 {{ $order->order_status === 'cancelled' ? 'opacity-70' : '' }}">
                                @if($order->payment_status === 'completed')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                        <i class="fa-solid fa-check mr-1 text-[9px]"></i> Đã thanh toán
                                    </span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">
                                        <i class="fa-solid fa-xmark mr-1 text-[9px]"></i> Thất bại
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span> Chưa thanh toán
                                    </span>
                                @endif
                            </td>
                            <!-- Cột 5: Trạng thái Đơn hàng -->
                            <td class="px-6 py-4">
                                @switch($order->order_status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-200">Chờ xác nhận</span>
                                        @break
                                    @case('processing')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-purple-50 text-purple-600 border border-purple-200"><i class="fa-solid fa-box-open mr-1.5 text-[10px]"></i> Đang xử lý</span>
                                        @break
                                    @case('ready_for_pickup')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-cyan-50 text-cyan-600 border border-cyan-200"><i class="fa-solid fa-boxes-packing mr-1.5 text-[10px]"></i> Chờ lấy hàng</span>
                                        @break
                                    @case('shipping')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-200"><i class="fa-solid fa-truck-fast mr-1.5 text-[10px]"></i> Đang giao</span>
                                        @break
                                    @case('delivered')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-green-50 text-green-700 border border-green-200"><i class="fa-solid fa-check-double mr-1.5 text-[10px]"></i> Đã giao</span>
                                        @break
                                    @case('cancelled')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-gray-200 text-gray-600 border border-gray-300"><i class="fa-solid fa-ban mr-1.5 text-[10px]"></i> Đã hủy</span>
                                        @if($order->note)
                                            <div class="text-[9px] text-red-500 mt-1">{{ Str::limit($order->note, 30) }}</div>
                                        @endif
                                        @break
                                @endswitch
                            </td>
                            <!-- Cột 6: Shipper -->
                            <td class="px-6 py-4 {{ $order->order_status === 'cancelled' ? 'opacity-70' : '' }}">
                                @if($order->shipper)
                                    <div class="font-bold text-gray-800 text-sm">{{ $order->shipper->fullname }}</div>
                                    <div class="text-[11px] text-gray-500 mt-1"><i class="fa-solid fa-phone text-[9px] mr-1"></i>{{ $order->shipper->phone }}</div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Chưa gán</span>
                                @endif
                            </td>
                            <!-- Cột 7: Hành động -->
                            <td class="px-6 py-4 text-center">
                                @include('admin.orders.partials.action-buttons', ['order' => $order])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-16">
                                <div class="flex flex-col items-center gap-3">
                                    <i class="fa-solid fa-inbox text-4xl text-gray-300"></i>
                                    <p class="text-gray-400 font-medium">Không tìm thấy đơn hàng nào.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white rounded-b-2xl">
                {{ $orders->links('vendor.pagination.greenly') }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Xem & Xử lý -->
@include('admin.orders.partials.show-modal')

<!-- Modal Gán Shipper -->
@foreach($orders as $order)
<div id="assignShipperModal-{{ $order->id }}" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeAssignShipperModal({{ $order->id }})"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div id="assignShipperContent-{{ $order->id }}" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col transform scale-95 transition-transform duration-300">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-forest-50 to-emerald-50 rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-bold text-forest-800"><i class="fa-solid fa-truck-fast mr-2 text-forest-600"></i>Gán Shipper cho Đơn {{ $order->order_code }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Chọn shipper để giao đơn hàng này</p>
                </div>
                <button onclick="closeAssignShipperModal({{ $order->id }})" class="w-8 h-8 rounded-full bg-white hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-all shadow-sm">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <!-- Body -->
            <div class="overflow-y-auto flex-1 custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] font-bold tracking-wider sticky top-0">
                        <tr>
                            <th class="px-5 py-3">ID</th>
                            <th class="px-5 py-3">Thông tin</th>
                            <th class="px-5 py-3">Trạng thái</th>
                            <th class="px-5 py-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($availableShippers as $shipper)
                        <tr class="hover:bg-forest-50/30 transition-colors">
                            <td class="px-5 py-3 text-gray-500 font-mono text-xs">#{{ $shipper->id }}</td>
                            <td class="px-5 py-3">
                                <div class="font-bold text-gray-800">{{ $shipper->fullname }}</div>
                                <div class="text-[11px] text-gray-500 mt-0.5"><i class="fa-solid fa-phone text-[9px] mr-1"></i>{{ $shipper->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-5 py-3">
                                @switch($shipper->work_status)
                                    @case('available')
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-700 border border-green-200"><span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>Sẵn sàng</span>
                                        @break
                                    @case('on_delivery')
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-orange-50 text-orange-600 border border-orange-200"><i class="fa-solid fa-motorcycle text-[9px] mr-1"></i>Đang giao</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">Offline</span>
                                @endswitch
                            </td>
                            <td class="px-5 py-3 text-center">
                                <form action="{{ route('admin.orders.assign-shipper', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="shipper_id" value="{{ $shipper->id }}">
                                    <button type="submit" class="px-3 py-1.5 bg-forest-500 hover:bg-forest-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm hover:shadow-md {{ $shipper->work_status === 'offline' ? 'opacity-40 cursor-not-allowed' : '' }}" {{ $shipper->work_status === 'offline' ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-user-check mr-1 text-[10px]"></i>Gán
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    // ============================================
    // MODAL XEM & XỬ LÝ ĐƠN HÀNG
    // ============================================

    // Hàm format tiền tệ
    function formatOrderCurrency(value) {
        const num = parseInt(value) || 0;
        return num.toLocaleString('vi-VN') + 'đ';
    }

    // Mở Modal
    function openShowOrderModal(button) {
        const order = JSON.parse(button.getAttribute('data-order'));

        // Thông tin đơn hàng
        document.getElementById('show_order_code').innerText = order.order_code;
        document.getElementById('show_order_created_at').innerText = order.created_at;

        // Thông tin khách hàng
        document.getElementById('show_order_customer_name').innerText = order.shipping_name || 'N/A';
        document.getElementById('show_order_customer_phone').innerText = order.shipping_phone || 'N/A';
        document.getElementById('show_order_customer_address').innerText = order.shipping_address || 'N/A';
        document.getElementById('show_order_note').innerText = order.note || 'Không có ghi chú';

        // Thanh toán
        document.getElementById('show_order_payment_method').innerText = order.payment_method_label;
        document.getElementById('show_order_total_money').innerText = formatOrderCurrency(order.total_money);
        document.getElementById('show_order_shipping_fee').innerText = formatOrderCurrency(order.shipping_fee);
        document.getElementById('show_order_final_amount').innerText = formatOrderCurrency(order.final_amount);

        // Badge trạng thái TT
        const ptBadge = document.getElementById('show_order_payment_status_badge');
        if (order.payment_status === 'completed') {
            ptBadge.innerHTML = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700"><i class="fa-solid fa-check mr-1"></i> Đã thanh toán</span>';
        } else if (order.payment_status === 'failed') {
            ptBadge.innerHTML = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600"><i class="fa-solid fa-xmark mr-1"></i> Thất bại</span>';
        } else {
            ptBadge.innerHTML = '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Chưa thanh toán</span>';
        }

        // Badge trạng thái ĐH
        const osBadge = document.getElementById('show_order_status_badge');
        const statusMap = {
            'pending': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-600">Chờ xác nhận</span>',
            'processing': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-600">Đang xử lý</span>',
            'ready_for_pickup': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-600">Chờ lấy hàng</span>',
            'shipping': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-600">Đang giao</span>',
            'delivered': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Đã giao</span>',
            'cancelled': '<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-600">Đã hủy</span>',
        };
        osBadge.innerHTML = statusMap[order.order_status] || order.order_status;

        // Set form action & giá trị select
        document.getElementById('updateOrderStatusForm').action = '/admin/orders/' + order.id + '/status';
        document.getElementById('select_order_status').value = order.order_status;
        document.getElementById('select_payment_status').value = order.payment_status;

        // Render danh sách mặt hàng
        const itemsContainer = document.getElementById('show_order_items');
        itemsContainer.innerHTML = '';

        if (order.order_details && order.order_details.length > 0) {
            order.order_details.forEach(function(item) {
                const productName = item.product_name || 'Sản phẩm không xác định';
                const productImage = item.product_image || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(productName) + '&background=e8f5e9&color=2e7d32&size=80';
                const subtotal = item.quantity * item.price;

                const row = document.createElement('div');
                row.className = 'flex items-center gap-4 py-3 border-b border-gray-50 last:border-b-0';
                row.innerHTML = `
                    <img src="${productImage}" alt="${productName}" class="w-14 h-14 rounded-xl object-cover border border-gray-100 shadow-sm shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate">${productName}</p>
                        <p class="text-xs text-gray-500 mt-0.5">SL: ${item.quantity} × ${formatOrderCurrency(item.price)}</p>
                    </div>
                    <div class="text-sm font-bold text-forest-700 shrink-0">${formatOrderCurrency(subtotal)}</div>
                `;
                itemsContainer.appendChild(row);
            });
        } else {
            itemsContainer.innerHTML = '<p class="text-sm text-gray-400 italic py-4">Không có mặt hàng nào.</p>';
        }

        // Hiện Modal
        const modal = document.getElementById('showOrderModal');
        const content = document.getElementById('showOrderContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    // Đóng Modal
    function closeShowOrderModal() {
        const modal = document.getElementById('showOrderModal');
        const content = document.getElementById('showOrderContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Submit form cập nhật trạng thái + loading
    document.getElementById('updateOrderStatusForm').addEventListener('submit', function() {
        const btn = document.getElementById('update_order_submit_btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // MODAL GÁN SHIPPER
    // ============================================
    function openAssignShipperModal(orderId) {
        const modal = document.getElementById('assignShipperModal-' + orderId);
        const content = document.getElementById('assignShipperContent-' + orderId);
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeAssignShipperModal(orderId) {
        const modal = document.getElementById('assignShipperModal-' + orderId);
        const content = document.getElementById('assignShipperContent-' + orderId);
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
