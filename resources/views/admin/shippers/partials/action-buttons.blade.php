<!-- Cụm nút hành động cho mỗi shipper -->
<div class="flex items-center justify-end gap-2">
    <!-- Nút Xem chi tiết -->
    <button onclick="openShipperModal({{ $shipper->id }})" class="w-8 h-8 rounded-full text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center justify-center border border-gray-200 hover:border-blue-200 shadow-sm" title="Xem hành trình">
        <i class="fa-solid fa-eye text-xs"></i>
    </button>

    <!-- Nút Gán đơn -->
    @if($isOffline)
        <button class="bg-gray-100 text-gray-300 px-3 py-1.5 rounded-lg border border-gray-200 flex items-center justify-center gap-1.5 font-bold text-xs cursor-not-allowed" title="Không thể gán đơn" disabled>
            <i class="fa-solid fa-box-open"></i> Gán đơn
        </button>
    @elseif($isOnDelivery && $deliveringCount >= 5)
        <button class="bg-gray-100 text-gray-400 px-3 py-1.5 rounded-lg border border-gray-200 flex items-center justify-center gap-1.5 font-bold text-xs cursor-not-allowed" title="Đã đạt giới hạn đơn" disabled>
            <i class="fa-solid fa-box-open"></i> Gán đơn
        </button>
    @elseif($isOnDelivery)
        <button class="bg-gray-100 text-gray-400 px-3 py-1.5 rounded-lg border border-gray-200 flex items-center justify-center gap-1.5 font-bold text-xs cursor-not-allowed" title="Đang bận giao hàng" disabled>
            <i class="fa-solid fa-box-open"></i> Gán đơn
        </button>
    @else
        <a href="{{ route('admin.orders.index', ['assign_shipper' => $shipper->id]) }}" class="hover:bg-forest-800 bg-forest-700 text-white px-3 py-1.5 rounded-lg shadow-md shadow-forest-500/20 transition-all flex items-center justify-center gap-1.5 font-bold text-xs">
            <i class="fa-solid fa-box-open"></i> Gán đơn
        </a>
    @endif
</div>
