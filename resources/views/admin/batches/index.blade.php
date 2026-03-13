@extends('layouts.admin')

@section('title', 'Quản lý Lô hàng - Greenly Admin')

@section('page-title', 'Lô Hàng Nhập')

@push('styles')
<style>
    /* Custom scrollbar cho filter dropdown và card thống kê */
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
</style>
@endpush

@section('content')
<div class="fade-in bg-cream-50 rounded-2xl relative min-h-[80vh]">

    <!-- 1. 3 CARD THỐNG KÊ -->
    <div class="flex flex-nowrap lg:grid lg:grid-cols-3 gap-6 mb-8 overflow-x-auto pb-4 custom-scrollbar">
        <!-- Card 1: Tổng Lô hàng -->
        <div class="min-w-[250px] bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-2 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-boxes-packing text-8xl"></i>
            </div>
            <div class="relative z-[1] flex items-center justify-between">
                <div>
                    <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng lô hàng</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $totalBatches }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-organic-400 text-xl shadow-inner">
                    <i class="fa-solid fa-pallet"></i>
                </div>
            </div>
        </div>

        <!-- Card 2: Tổng Vốn Đầu Tư -->
        <div class="min-w-[250px] bg-gradient-to-br from-teal-700 via-teal-600 to-teal-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-2 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-sack-dollar text-8xl"></i>
            </div>
            <div class="relative z-[1] flex items-center justify-between">
                <div>
                    <p class="text-teal-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng vốn đầu tư</p>
                    <h3 class="text-2xl font-bold text-white drop-shadow-md">
                        @if($totalInvestment >= 1000000)
                            {{ number_format($totalInvestment / 1000000, 1, ',', '.') }} <span class="text-base font-normal">Triệu</span>
                        @else
                            {{ number_format($totalInvestment, 0, ',', '.') }}đ
                        @endif
                    </h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-yellow-300 text-xl shadow-inner">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
        </div>

        <!-- Card 3: Cảnh báo tồn kho -->
        <div class="min-w-[250px] bg-gradient-to-br from-orange-600 via-orange-500 to-red-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-2 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-triangle-exclamation text-8xl"></i>
            </div>
            <div class="relative z-[1] flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-[11px] font-bold uppercase tracking-wider mb-1">Cảnh báo tồn kho</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ str_pad($warningBatches, 2, '0', STR_PAD_LEFT) }}</h3>
                        <span class="text-[10px] text-white font-medium mb-1 opacity-80">Lô cần chú ý</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-white text-xl shadow-inner">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TOOLBAR: TÌM KIẾM, LỌC & THÊM MỚI -->
    <form method="GET" id="filterForm" action="{{ route('admin.batches.index') }}" class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Combobox chọn số lượng hiển thị -->
            <select name="per_page" onchange="document.getElementById('filterForm').submit();" class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 mục</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 mục</option>
                <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30 mục</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 mục</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 mục</option>
            </select>

            <!-- Tìm kiếm -->
            <div class="relative group w-full sm:w-auto">
                <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500 transition-all">
                    <i class="fa-solid fa-search text-gray-400 mr-3 group-focus-within:text-forest-500"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã lô, sản phẩm, NCC..." class="bg-transparent text-sm text-gray-600 outline-none w-full sm:w-52 placeholder-gray-400">
                </div>
            </div>

            <!-- Lọc theo Trạng thái tồn kho -->
            <select name="status" onchange="document.getElementById('filterForm').submit();" class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Đang còn</option>
                <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Sắp hết</option>
                <option value="empty" {{ request('status') === 'empty' ? 'selected' : '' }}>Đã hết</option>
            </select>

            <!-- Lọc theo Sản phẩm -->
            <select name="product_id" onchange="document.getElementById('filterForm').submit();" class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
                <option value="">Tất cả sản phẩm</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>

            <!-- Lọc theo Nhà cung cấp -->
            <select name="supplier_id" onchange="document.getElementById('filterForm').submit();" class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
                <option value="">Tất cả NCC</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Nút Nhập Lô Hàng -->
        <button type="button" onclick="openAddBatchModal()" class="w-full md:w-auto bg-forest-700 hover:bg-forest-800 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-forest-500/30 flex items-center justify-center gap-2 transition-all font-bold">
            <i class="fa-solid fa-plus"></i> Nhập Lô Hàng
        </button>
    </form>

    <!-- 3. BẢNG DỮ LIỆU LÔ HÀNG -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left whitespace-nowrap min-w-[1000px]">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-xs font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-center w-12">ID</th>
                        <th class="px-6 py-4">Sản phẩm</th>
                        <th class="px-6 py-4">Nhà cung cấp</th>
                        <th class="px-6 py-4">Giá nhập</th>
                        <th class="px-6 py-4 w-48">Tồn kho / Ban đầu</th>
                        <th class="px-6 py-4">Thời gian</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-center sticky right-0 bg-cream-100/50 shadow-[-4px_0_10px_rgba(0,0,0,0.02)] z-[5]">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse ($batches as $batch)
                    @php
                        // Xác định trạng thái tồn kho
                        $isOutOfStock = $batch->current_quantity == 0;
                        $isLowStock = !$isOutOfStock && $batch->current_quantity <= 10;
                        $isAvailable = !$isOutOfStock && !$isLowStock;

                        // Tính phần trăm tồn kho
                        $stockPercent = $batch->quantity > 0 ? round(($batch->current_quantity / $batch->quantity) * 100) : 0;

                        // Màu sắc theo trạng thái
                        if ($isOutOfStock) {
                            $rowClass = 'bg-gray-50/50 hover:bg-gray-100/50';
                            $stockColor = 'text-gray-500';
                            $barColor = 'bg-gray-300';
                        } elseif ($isLowStock) {
                            $rowClass = 'bg-orange-50/20 hover:bg-orange-50/40';
                            $stockColor = 'text-organic-600';
                            $barColor = 'bg-organic-500';
                        } else {
                            $rowClass = 'hover:bg-forest-50/30';
                            $stockColor = 'text-green-600';
                            $barColor = 'bg-green-500';
                        }
                    @endphp
                    <tr class="{{ $rowClass }} transition-colors group">
                        <!-- Cột 1: ID -->
                        <td class="px-6 py-4 text-center font-mono text-gray-500 text-xs">#B{{ str_pad($batch->id, 2, '0', STR_PAD_LEFT) }}</td>

                        <!-- Cột 2: Sản phẩm -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $batch->product && $batch->product->image ? asset('storage/' . $batch->product->image) : asset('images/no-image.png') }}"
                                    alt="{{ $batch->product->name ?? 'N/A' }}"
                                    class="w-10 h-10 rounded-lg object-cover border border-gray-200 shrink-0 {{ $isOutOfStock ? 'grayscale' : '' }}"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($batch->product->name ?? 'N') }}&background=c8e6c9&color=2e7d32'">
                                <div>
                                    <div class="font-bold text-gray-800 text-sm group-hover:text-forest-700 transition-colors {{ $isOutOfStock ? 'line-through text-gray-500' : '' }}">{{ $batch->product->name ?? 'Không xác định' }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5 font-mono">PID: #{{ $batch->product_id }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- Cột 3: Nhà cung cấp -->
                        <td class="px-6 py-4 {{ $isOutOfStock ? 'opacity-60' : '' }}">
                            <div class="font-bold text-gray-700">{{ $batch->supplier->name ?? 'Không xác định' }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5 font-mono">SID: #{{ $batch->supplier_id }}</div>
                        </td>

                        <!-- Cột 4: Giá nhập -->
                        <td class="px-6 py-4 {{ $isOutOfStock ? 'opacity-60' : '' }}">
                            <div class="font-bold text-forest-700 text-sm">{{ number_format($batch->import_price, 0, ',', '.') }}đ</div>
                        </td>

                        <!-- Cột 5: Tồn kho / Ban đầu (Progress Bar) -->
                        <td class="px-6 py-4 {{ $isOutOfStock ? 'opacity-60' : '' }}">
                            <div class="flex justify-between items-end text-[11px] mb-1">
                                <span class="font-bold {{ $stockColor }} text-sm {{ $isOutOfStock && $batch->current_quantity == 0 ? '' : '' }} {{ $isLowStock ? '' : '' }}">{{ $batch->current_quantity }}</span>
                                <span class="text-gray-400 font-medium">/ {{ $batch->quantity }} {{ $batch->product->unit ?? '' }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $stockPercent }}%"></div>
                            </div>
                        </td>

                        <!-- Cột 6: Thời gian -->
                        <td class="px-6 py-4 {{ $isOutOfStock ? 'opacity-60' : '' }}">
                            <div class="text-gray-700 text-xs font-medium">
                                <i class="fa-solid fa-plus text-[10px] {{ $isOutOfStock ? 'text-gray-400' : 'text-green-500' }} mr-1 w-3"></i>
                                {{ $batch->created_at->format('d/m/Y') }}
                            </div>
                            @if($batch->updated_at->eq($batch->created_at))
                                <div class="text-gray-400 italic text-xs mt-1">-</div>
                            @else
                                <div class="text-gray-500 text-xs mt-1">
                                    <i class="fa-solid fa-pen text-[10px] {{ $isOutOfStock ? 'text-gray-400' : 'text-blue-500' }} mr-1 w-3"></i>
                                    {{ $batch->updated_at->format('d/m/Y') }}
                                </div>
                            @endif
                        </td>

                        <!-- Cột 7: Trạng thái -->
                        <td class="px-6 py-4 text-center">
                            @if($isOutOfStock)
                                <span class="inline-flex items-center px-2 py-1 rounded border border-gray-200 bg-gray-100 text-gray-600 text-[11px] font-bold">
                                    Đã hết
                                </span>
                            @elseif($isLowStock)
                                <span class="inline-flex items-center px-2 py-1 rounded border border-orange-200 bg-orange-50 text-organic-600 text-[11px] font-bold">
                                    Sắp hết
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded border border-green-200 bg-green-50 text-green-700 text-[11px] font-bold">
                                    Đang còn
                                </span>
                            @endif
                        </td>

                        <!-- Cột 8: Hành động (sticky) -->
                        <td class="px-6 py-4 sticky right-0 bg-white shadow-[-4px_0_10px_rgba(0,0,0,0.02)] z-[5]">
                            @include('admin.batches.partials.action-buttons', ['batch' => $batch])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                                <p>Chưa có lô hàng nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-b-2xl">
            <span class="text-sm text-gray-500 font-medium">Hiển thị {{ $batches->firstItem() ?? 0 }} - {{ $batches->lastItem() ?? 0 }} trên tổng số {{ $batches->total() }} lô hàng</span>
            <div class="w-full sm:w-auto">
                {{ $batches->links('vendor.pagination.greenly') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('modals')
    <!-- Modal Thêm Lô Hàng -->
    @include('admin.batches.partials.add-modal')

    <!-- Modal Sửa Lô Hàng -->
    @include('admin.batches.partials.edit-modal')

    <!-- Modal Xóa Lô Hàng -->
    @include('admin.batches.partials.delete-modal')

    <!-- Modal Xem Chi Tiết Lô Hàng -->
    @include('admin.batches.partials.show-modal')
@endpush

@push('scripts')
<script>
    // ============================================
    // MODAL XEM CHI TIẾT LÔ HÀNG
    // ============================================
    function openShowBatchModal(button) {
        let batch = JSON.parse(button.getAttribute('data-batch'));

        // Điền thông tin cơ bản
        document.getElementById('show_batch_id').textContent = '#B' + String(batch.id).padStart(2, '0');
        document.getElementById('show_batch_code').textContent = batch.batch_code;
        document.getElementById('show_batch_product').querySelector('span').textContent = batch.product ? batch.product.name : 'N/A';
        document.getElementById('show_batch_supplier').querySelector('span').textContent = batch.supplier ? batch.supplier.name : 'N/A';
        document.getElementById('show_batch_import_price').textContent = parseInt(batch.import_price).toLocaleString('vi-VN') + 'đ';
        document.getElementById('show_batch_quantity').textContent = batch.quantity;
        document.getElementById('show_batch_current_quantity').textContent = batch.current_quantity;

        // Trạng thái
        const statusBadge = document.getElementById('show_batch_status_badge');
        if (batch.current_quantity == 0) {
            statusBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-gray-100 text-gray-600 border border-gray-200';
            statusBadge.innerHTML = '<i class="fa-solid fa-circle-xmark mr-2 text-xs"></i> Đã hết hàng';
        } else if (batch.current_quantity <= 10) {
            statusBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-orange-50 text-orange-600 border border-orange-200';
            statusBadge.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-2 text-xs"></i> Sắp hết';
        } else {
            statusBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-green-50 text-green-700 border border-green-200';
            statusBadge.innerHTML = '<span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Đang còn';
        }

        // Thời gian
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const d = new Date(dateStr);
            const pad = n => String(n).padStart(2, '0');
            return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
        }
        document.getElementById('show_batch_created_at').textContent = formatDate(batch.created_at);
        document.getElementById('show_batch_updated_at').textContent = (batch.created_at === batch.updated_at) ? 'Chưa cập nhật' : formatDate(batch.updated_at);

        // Mở modal
        const modal = document.getElementById('showBatchModal');
        const content = document.getElementById('showBatchContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeShowBatchModal() {
        const modal = document.getElementById('showBatchModal');
        const content = document.getElementById('showBatchContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // ============================================
    // MODAL THÊM LÔ HÀNG
    // ============================================
    function openAddBatchModal() {
        const modal = document.getElementById('addBatchModal');
        const content = document.getElementById('addBatchContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeAddBatchModal() {
        const modal = document.getElementById('addBatchModal');
        const content = document.getElementById('addBatchContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('addBatchForm').reset();
        }, 300);
    }

    // Xử lý submit form thêm (Loading state)
    document.getElementById('addBatchForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('add_batch_submit_btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // MODAL SỬA LÔ HÀNG
    // ============================================
    function openEditBatchModal(button) {
        let batch = JSON.parse(button.getAttribute('data-batch'));

        // Set action form
        document.getElementById('editBatchForm').action = '/admin/batches/' + batch.id;

        // Điền dữ liệu
        document.getElementById('edit_batch_id').value = batch.id;
        document.getElementById('edit_batch_code').value = batch.batch_code;
        document.getElementById('edit_batch_product_id').value = batch.product_id;
        document.getElementById('edit_batch_supplier_id').value = batch.supplier_id;

        // Format giá nhập
        const price = parseInt(batch.import_price) || 0;
        document.getElementById('edit_batch_import_price').value = price > 0 ? price.toLocaleString('vi-VN') : '';

        document.getElementById('edit_batch_quantity').value = batch.quantity;
        document.getElementById('edit_batch_current_quantity').value = batch.current_quantity;

        // Mở modal
        const modal = document.getElementById('editBatchModal');
        const content = document.getElementById('editBatchContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeEditBatchModal() {
        const modal = document.getElementById('editBatchModal');
        const content = document.getElementById('editBatchContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('editBatchForm').reset();
        }, 300);
    }

    // Xử lý submit form sửa (Loading state)
    document.getElementById('editBatchForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('edit_batch_submit_btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // MODAL XÓA LÔ HÀNG
    // ============================================
    function openDeleteBatchModal(id, code) {
        document.getElementById('delete_batch_code').textContent = code;
        document.getElementById('deleteBatchForm').action = '/admin/batches/' + id;

        const modal = document.getElementById('deleteBatchModal');
        const content = document.getElementById('deleteBatchContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeDeleteBatchModal() {
        const modal = document.getElementById('deleteBatchModal');
        const content = document.getElementById('deleteBatchContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // Xử lý submit form xóa (Loading state)
    document.getElementById('deleteBatchForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('confirmDeleteBatchBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // FORMAT TIỀN TỆ (dùng chung cho giá nhập)
    // ============================================
    function formatBatchCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            input.value = parseInt(value).toLocaleString('vi-VN');
        }
    }

    function filterBatchCurrencyKeydown(event) {
        // Cho phép: Backspace, Delete, Tab, Escape, Enter, Arrow keys
        if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(event.keyCode) !== -1 ||
            (event.keyCode >= 48 && event.keyCode <= 57) ||
            (event.keyCode >= 96 && event.keyCode <= 105)) {
            return true;
        }
        event.preventDefault();
        return false;
    }
</script>
@endpush
