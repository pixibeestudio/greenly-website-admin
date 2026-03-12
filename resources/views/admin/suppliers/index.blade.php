@extends('layouts.admin')

@section('title', 'Quản lý Nhà Cung Cấp - Greenly Admin')

@section('page-title', 'Nhà Cung Cấp')

@section('content')
<div class="fade-in">

    <!-- 1. 3 CARD THỐNG KÊ -->
    <div class="flex flex-nowrap lg:grid lg:grid-cols-3 gap-6 mb-8 overflow-x-auto pb-4 custom-scrollbar">
         <!-- Thẻ 1: Tổng nhà cung cấp -->
         <div class="min-w-[250px] bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-handshake text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng nhà cung cấp</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $totalSuppliers ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-organic-400 text-xl shadow-inner">
                    <i class="fa-solid fa-building-wheat"></i>
                </div>
            </div>
         </div>

         <!-- Thẻ 2: Đang hợp tác -->
         <div class="min-w-[250px] bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-truck-fast text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider mb-1">Đang hợp tác</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $activeSuppliers ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-white text-xl shadow-inner">
                    <i class="fa-solid fa-check-double"></i>
                </div>
            </div>
         </div>

         <!-- Thẻ 3: Ngừng hợp tác -->
         <div class="min-w-[250px] bg-gradient-to-br from-slate-700 via-slate-600 to-slate-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-ban text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-slate-200 text-[11px] font-bold uppercase tracking-wider mb-1">Ngừng hợp tác</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $inactiveSuppliers ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-slate-100 text-xl shadow-inner">
                    <i class="fa-solid fa-link-slash"></i>
                </div>
            </div>
         </div>
    </div>

    <!-- 2. TOOLBAR: TÌM KIẾM, LỌC & THÊM MỚI -->
    <form method="GET" id="filterForm" action="{{ route('admin.suppliers.index') }}" class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên NCC, SĐT..." class="bg-transparent text-sm text-gray-600 outline-none w-full sm:w-48 placeholder-gray-400">
                </div>
            </div>

            <!-- Lọc theo Trạng thái -->
            <select name="is_active" onchange="document.getElementById('filterForm').submit();" class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
                <option value="">Tất cả trạng thái</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Đang hợp tác</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Ngừng hợp tác</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            <!-- Nút Thêm Nhà Cung Cấp -->
            <button type="button" onclick="openAddSupplierModal()" class="w-full sm:w-auto bg-forest-700 hover:bg-forest-800 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-forest-500/30 flex items-center justify-center gap-2 transition-all font-bold">
                <i class="fa-solid fa-plus"></i> Thêm Nhà Cung Cấp
            </button>
        </div>
    </form>

    <!-- 3. BẢNG DỮ LIỆU NHÀ CUNG CẤP -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left whitespace-nowrap min-w-[900px]">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-xs font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-center w-16">ID</th>
                        <th class="px-6 py-4">Nhà cung cấp</th>
                        <th class="px-6 py-4">Thông tin liên hệ</th>
                        <th class="px-6 py-4">Khu vực (Địa chỉ)</th>
                        <th class="px-6 py-4">Chứng chỉ</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-center">Ngày tạo</th>
                        <th class="px-6 py-4 text-center">Cập nhật</th>
                        <th class="px-6 py-4 text-center sticky right-0 bg-cream-100/50 shadow-[-4px_0_10px_rgba(0,0,0,0.02)] z-[5]">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse($suppliers as $supplier)
                    @php
                        $colors = ['bg-green-100 text-green-700 border-green-200', 'bg-blue-100 text-blue-700 border-blue-200', 'bg-orange-100 text-orange-700 border-orange-200', 'bg-red-100 text-red-600 border-red-200', 'bg-purple-100 text-purple-700 border-purple-200'];
                        $colorIndex = $supplier->id % count($colors);
                        $avatarColor = $colors[$colorIndex];
                        $initial = mb_strtoupper(mb_substr($supplier->name, 0, 1));
                    @endphp
                    <tr class="{{ $supplier->is_active ? 'hover:bg-forest-50/30' : 'bg-gray-50/50 hover:bg-gray-100/50' }} transition-colors group">
                        <!-- Cột 1: ID -->
                        <td class="px-6 py-4 text-center font-mono text-gray-500 text-xs">#S{{ str_pad($supplier->id, 2, '0', STR_PAD_LEFT) }}</td>
                        <!-- Cột 2: Nhà cung cấp (Avatar + Tên + Ngày hợp tác) -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3 {{ !$supplier->is_active ? 'opacity-60' : '' }}">
                                <div class="w-10 h-10 rounded-full {{ $avatarColor }} flex items-center justify-center font-bold text-lg border shrink-0">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-sm group-hover:text-forest-700 transition-colors {{ !$supplier->is_active ? 'line-through text-gray-500' : '' }}">{{ $supplier->name }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">Hợp tác từ: {{ $supplier->created_at->format('Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <!-- Cột 3: Thông tin liên hệ -->
                        <td class="px-6 py-4 {{ !$supplier->is_active ? 'opacity-60' : '' }}">
                            <div class="font-bold text-gray-700">{{ $supplier->contact_name ?? 'Chưa cập nhật' }}</div>
                            @if($supplier->phone)
                            <div class="text-[11px] text-gray-500 mt-0.5"><i class="fa-solid fa-phone mr-1"></i> {{ $supplier->phone }}</div>
                            @endif
                        </td>
                        <!-- Cột 4: Khu vực -->
                        <td class="px-6 py-4 {{ !$supplier->is_active ? 'opacity-60' : '' }}">
                            <div class="font-medium text-gray-800">{{ $supplier->address }}</div>
                        </td>
                        <!-- Cột 5: Chứng chỉ -->
                        <td class="px-6 py-4 {{ !$supplier->is_active ? 'opacity-60' : '' }}">
                            @if($supplier->certificate)
                                <div class="flex flex-wrap gap-1">
                                    @foreach(explode(',', $supplier->certificate) as $cert)
                                        @php $cert = trim($cert); @endphp
                                        @if($cert)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">{{ $cert }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Không có</span>
                            @endif
                        </td>
                        <!-- Cột 6: Trạng thái -->
                        <td class="px-6 py-4 text-center">
                            @if($supplier->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Đang hợp tác
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-gray-200 text-gray-600 border border-gray-300">
                                    <i class="fa-solid fa-ban mr-1.5 text-[10px]"></i> Ngừng hợp tác
                                </span>
                            @endif
                        </td>
                        <!-- Cột 7: Ngày tạo -->
                        <td class="px-6 py-4 text-center">
                            <div class="text-xs text-gray-600 font-medium">{{ $supplier->created_at->format('d/m/Y') }}</div>
                            <div class="text-[11px] text-gray-400">{{ $supplier->created_at->format('H:i') }}</div>
                        </td>
                        <!-- Cột 8: Cập nhật -->
                        <td class="px-6 py-4 text-center">
                            @if($supplier->updated_at->eq($supplier->created_at))
                                <span class="text-xs text-gray-400 italic">Chưa cập nhật</span>
                            @else
                                <div class="text-xs text-gray-600 font-medium">{{ $supplier->updated_at->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-gray-400">{{ $supplier->updated_at->format('H:i') }}</div>
                            @endif
                        </td>
                        <!-- Cột 9: Hành động (sticky) -->
                        <td class="px-6 py-4 sticky right-0 bg-white shadow-[-4px_0_10px_rgba(0,0,0,0.02)] z-[5]">
                            @include('admin.suppliers.partials.action-buttons', ['supplier' => $supplier])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                                <p>Chưa có nhà cung cấp nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-b-2xl">
            <span class="text-sm text-gray-500 font-medium">Hiển thị {{ $suppliers->firstItem() ?? 0 }} - {{ $suppliers->lastItem() ?? 0 }} trên tổng số {{ $suppliers->total() }} nhà cung cấp</span>
            <div class="w-full sm:w-auto">
                {{ $suppliers->links('vendor.pagination.greenly') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('modals')
    <!-- Modal Xem Chi Tiết Nhà Cung Cấp -->
    @include('admin.suppliers.partials.show-modal')

    <!-- Modal Thêm Nhà Cung Cấp -->
    @include('admin.suppliers.partials.add-modal')

    <!-- Modal Sửa Nhà Cung Cấp -->
    @include('admin.suppliers.partials.edit-modal')

    <!-- Modal Xóa Nhà Cung Cấp -->
    @include('admin.suppliers.partials.delete-modal')
@endpush

@push('scripts')
<script>
    // ============================================
    // MODAL XEM CHI TIẾT NHÀ CUNG CẤP
    // ============================================
    function openShowSupplierModal(button) {
        let supplier = JSON.parse(button.getAttribute('data-supplier'));

        // Điền thông tin cơ bản
        document.getElementById('show_supplier_id').textContent = '#S' + String(supplier.id).padStart(2, '0');
        document.getElementById('show_supplier_name').textContent = supplier.name;

        // Thông tin liên hệ
        const contactEl = document.getElementById('show_supplier_contact_name');
        contactEl.querySelector('span').textContent = supplier.contact_name || 'Chưa cập nhật';

        const phoneEl = document.getElementById('show_supplier_phone');
        phoneEl.querySelector('span').textContent = supplier.phone || 'Chưa cập nhật';

        const addressEl = document.getElementById('show_supplier_address');
        addressEl.querySelector('span').textContent = supplier.address || 'Chưa cập nhật';

        // Chứng chỉ (render badges)
        const certContainer = document.getElementById('show_supplier_certificates');
        const noCertEl = document.getElementById('show_supplier_no_cert');
        certContainer.innerHTML = '';

        if (supplier.certificate && supplier.certificate.trim()) {
            noCertEl.classList.add('hidden');
            supplier.certificate.split(',').forEach(cert => {
                cert = cert.trim();
                if (cert) {
                    const badge = document.createElement('span');
                    badge.className = 'px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100';
                    badge.textContent = cert;
                    certContainer.appendChild(badge);
                }
            });
        } else {
            noCertEl.classList.remove('hidden');
        }

        // Trạng thái
        const statusBadge = document.getElementById('show_supplier_status_badge');
        if (supplier.is_active) {
            statusBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-green-50 text-green-700 border border-green-200';
            statusBadge.innerHTML = '<span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Đang hợp tác';
        } else {
            statusBadge.className = 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-gray-200 text-gray-600 border border-gray-300';
            statusBadge.innerHTML = '<i class="fa-solid fa-ban mr-2 text-xs"></i> Ngừng hợp tác';
        }

        // Thời gian
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const d = new Date(dateStr);
            const pad = n => String(n).padStart(2, '0');
            return pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
        }
        document.getElementById('show_supplier_created_at').textContent = formatDate(supplier.created_at);
        document.getElementById('show_supplier_updated_at').textContent = (supplier.created_at === supplier.updated_at) ? 'Chưa cập nhật' : formatDate(supplier.updated_at);

        // Mở modal
        const modal = document.getElementById('showSupplierModal');
        const content = document.getElementById('showSupplierContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeShowSupplierModal() {
        const modal = document.getElementById('showSupplierModal');
        const content = document.getElementById('showSupplierContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // ============================================
    // MODAL THÊM NHÀ CUNG CẤP
    // ============================================
    function openAddSupplierModal() {
        const modal = document.getElementById('addSupplierModal');
        const content = document.getElementById('addSupplierContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeAddSupplierModal() {
        const modal = document.getElementById('addSupplierModal');
        const content = document.getElementById('addSupplierContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Xử lý submit form thêm (Loading state)
    document.getElementById('addSupplierForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('add_supplier_submit_btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // MODAL SỬA NHÀ CUNG CẤP
    // ============================================
    function openEditSupplierModal(button) {
        let supplier = JSON.parse(button.getAttribute('data-supplier'));

        // Set action form + hidden supplier_id
        document.getElementById('editSupplierForm').action = '/admin/suppliers/' + supplier.id;
        document.getElementById('edit_supplier_hidden_id').value = supplier.id;

        // Điền dữ liệu text
        document.getElementById('edit_supplier_id').value = supplier.id;
        document.getElementById('edit_supplier_name').value = supplier.name;
        document.getElementById('edit_supplier_contact_name').value = supplier.contact_name || '';
        document.getElementById('edit_supplier_phone').value = supplier.phone || '';
        document.getElementById('edit_supplier_address').value = supplier.address || '';
        document.getElementById('edit_supplier_certificate').value = supplier.certificate || '';

        // Set radio trạng thái
        const isActive = supplier.is_active ? '1' : '0';
        const radioEl = document.querySelector('#editSupplierForm input[name="is_active"][value="' + isActive + '"]');
        if (radioEl) radioEl.checked = true;

        // Mở modal
        const modal = document.getElementById('editSupplierModal');
        const content = document.getElementById('editSupplierContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeEditSupplierModal() {
        const modal = document.getElementById('editSupplierModal');
        const content = document.getElementById('editSupplierContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('editSupplierForm').reset();
        }, 300);
    }

    // Xử lý submit form sửa (Loading state)
    document.getElementById('editSupplierForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('edit_supplier_submit_btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // MODAL XÓA NHÀ CUNG CẤP
    // ============================================
    function openDeleteSupplierModal(id, name) {
        document.getElementById('delete_supplier_name').textContent = name;
        document.getElementById('deleteSupplierForm').action = '/admin/suppliers/' + id;

        const modal = document.getElementById('deleteSupplierModal');
        const content = document.getElementById('deleteSupplierContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function closeDeleteSupplierModal() {
        const modal = document.getElementById('deleteSupplierModal');
        const content = document.getElementById('deleteSupplierContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Xử lý submit form xóa (Loading state)
    document.getElementById('deleteSupplierForm').addEventListener('submit', function (e) {
        const btn = document.getElementById('confirmDeleteSupplierBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });
</script>
@endpush
