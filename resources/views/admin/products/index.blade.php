@extends('layouts.admin')

@section('title', 'Danh sách Sản phẩm - Greenly Admin')

@section('page-title', 'Danh sách Sản phẩm')

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

    <!-- 1. 4 CARD THỐNG KÊ -->
    <div class="flex flex-nowrap lg:grid lg:grid-cols-4 gap-6 mb-8 overflow-x-auto pb-4 custom-scrollbar">
         <!-- Thẻ 1: Tổng sản phẩm -->
         <div class="min-w-[250px] bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-boxes-stacked text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider mb-1">Tổng sản phẩm</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $totalProducts ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-organic-400 text-xl shadow-inner">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
         </div>

         <!-- Thẻ 2: Hết hàng -->
         <div class="min-w-[250px] bg-gradient-to-br from-red-700 via-red-600 to-red-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-triangle-exclamation text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-[11px] font-bold uppercase tracking-wider mb-1">Đang hết hàng</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $outOfStockProducts ?? 0 }}</h3>
                        <span class="text-[9px] text-red-100 font-bold uppercase bg-red-900/40 px-1.5 py-0.5 rounded shadow-sm animate-pulse border border-red-400/50">Cần nhập!</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-yellow-300 text-xl shadow-inner">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
         </div>

         <!-- Thẻ 3: Đang bán -->
         <div class="min-w-[250px] bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-store text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider mb-1">Đang mở bán</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $discountedProducts ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-white text-xl shadow-inner">
                    <i class="fa-solid fa-store"></i>
                </div>
            </div>
         </div>

         <!-- Thẻ 4: Ngừng kinh doanh -->
         <div class="min-w-[250px] bg-gradient-to-br from-slate-700 via-slate-600 to-slate-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
            <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-ban text-8xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-slate-200 text-[11px] font-bold uppercase tracking-wider mb-1">Ngừng kinh doanh</p>
                    <h3 class="text-3xl font-bold text-white drop-shadow-md">{{ $inactiveProducts ?? 0 }}</h3>
                </div>
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-slate-100 text-xl shadow-inner">
                    <i class="fa-solid fa-ban"></i>
                </div>
            </div>
         </div>
    </div>

    <!-- 2. TOOLBAR: TÌM KIẾM, LỌC & THÊM MỚI -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Combobox chọn số lượng hiển thị -->
            <select name="per_page" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên sản phẩm..." class="bg-transparent text-sm text-gray-600 outline-none w-full sm:w-48 placeholder-gray-400">
                </div>
            </div>
        </form>

        <div class="flex items-center gap-3">
            <!-- Nút & Dropdown Filter -->
            <div class="relative">
                <button id="filterBtn" onclick="toggleFilter()" class="bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl shadow-sm hover:bg-gray-50 hover:text-forest-700 transition-all font-bold text-sm flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Bộ Lọc <span id="filterCount" class="hidden bg-forest-600 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1">0</span>
                </button>

                <!-- Filter Dropdown Menu (Ban đầu ẩn) -->
                <div id="filterMenu" class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden origin-top-right transition-all">
                    
                    <div class="p-5 max-h-[400px] overflow-y-auto custom-scrollbar">
                        <!-- Nhóm 1: Giá sản phẩm -->
                        <div class="mb-5">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Sắp xếp Giá</h4>
                            <div class="space-y-2">
                                <label class="flex items-center p-2 rounded-lg hover:bg-forest-50 cursor-pointer group transition-colors">
                                    <input type="radio" name="priceFilter" class="peer hidden" value="expensive">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-forest-600 peer-checked:bg-forest-600 flex items-center justify-center mr-3 transition-colors">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-forest-700">Giá cao nhất</span>
                                </label>
                                <label class="flex items-center p-2 rounded-lg hover:bg-forest-50 cursor-pointer group transition-colors">
                                    <input type="radio" name="priceFilter" class="peer hidden" value="cheap">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-forest-600 peer-checked:bg-forest-600 flex items-center justify-center mr-3 transition-colors">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-forest-700">Giá rẻ nhất</span>
                                </label>
                            </div>
                        </div>

                        <hr class="border-gray-100 mb-5">

                        <!-- Nhóm 2: Trạng thái sản phẩm -->
                        <div class="mb-5">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Trạng thái (is_active / Tồn kho)</h4>
                            <div class="space-y-2">
                                <label class="flex items-center p-2 rounded-lg hover:bg-green-50 cursor-pointer group transition-colors">
                                    <input type="radio" name="statusFilter" class="peer hidden" value="active">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-green-600 peer-checked:bg-green-600 flex items-center justify-center mr-3 transition-colors">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-green-700">Đang bán</span>
                                </label>
                                <label class="flex items-center p-2 rounded-lg hover:bg-gray-100 cursor-pointer group transition-colors">
                                    <input type="radio" name="statusFilter" class="peer hidden" value="inactive">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-gray-600 peer-checked:bg-gray-600 flex items-center justify-center mr-3 transition-colors">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800">Ngừng bán</span>
                                </label>
                                <label class="flex items-center p-2 rounded-lg hover:bg-red-50 cursor-pointer group transition-colors">
                                    <input type="radio" name="statusFilter" class="peer hidden" value="outofstock">
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-red-600 peer-checked:bg-red-600 flex items-center justify-center mr-3 transition-colors">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-red-700">Hết hàng</span>
                                </label>
                            </div>
                        </div>

                        <hr class="border-gray-100 mb-5">

                        <!-- Nhóm 3: Khuyến mãi -->
                        <div class="mb-2">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Khuyến mãi (discount_price)</h4>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center justify-center p-2 rounded-lg border border-gray-200 cursor-pointer group transition-all peer-checked:bg-organic-50 hover:bg-gray-50 has-[:checked]:border-organic-500 has-[:checked]:bg-organic-50 has-[:checked]:text-organic-700 text-gray-600">
                                    <input type="radio" name="discountFilter" class="peer hidden" value="has_discount">
                                    <span class="text-xs font-bold text-center"><i class="fa-solid fa-tag mr-1"></i> Có sale</span>
                                </label>
                                <label class="flex items-center justify-center p-2 rounded-lg border border-gray-200 cursor-pointer group transition-all hover:bg-gray-50 has-[:checked]:border-forest-500 has-[:checked]:bg-forest-50 has-[:checked]:text-forest-700 text-gray-600">
                                    <input type="radio" name="discountFilter" class="peer hidden" value="no_discount">
                                    <span class="text-xs font-bold text-center">Không sale</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Các nút Action của Dropdown -->
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-2">
                        <button onclick="toggleFilter()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Hủy</button>
                        <div class="flex gap-2">
                            <button onclick="resetFilter()" class="px-4 py-2 text-sm font-bold text-forest-700 bg-forest-100 hover:bg-forest-200 rounded-lg transition-colors">Làm lại</button>
                            <button onclick="applyFilter()" class="px-5 py-2 text-sm font-bold text-white bg-forest-700 hover:bg-forest-800 rounded-lg shadow-md shadow-forest-500/20 transition-all">Áp dụng</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút Thêm Sản Phẩm -->
            <button onclick="openAddProductModal()" class="w-full sm:w-auto bg-forest-700 hover:bg-forest-800 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-forest-500/30 flex items-center justify-center gap-2 transition-all font-bold">
                <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm
            </button>
        </div>
    </div>

    <!-- 3. BẢNG DỮ LIỆU SẢN PHẨM -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left whitespace-nowrap min-w-[1000px]">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-xs font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 w-20 text-center">Mã & Ảnh</th>
                        <th class="px-6 py-4 w-64">Sản phẩm & Danh mục</th>
                        <th class="px-6 py-4 w-64">Đặc tính (Mô tả & Xuất xứ)</th>
                        <th class="px-6 py-4">Giá bán & Đơn vị</th>
                        <th class="px-6 py-4 text-center">Tồn kho</th>
                        <th class="px-6 py-4 text-center">Trạng thái & Thời gian</th>
                        <th class="px-6 py-4 text-center sticky right-0 bg-cream-100/50 shadow-[-4px_0_10px_rgba(0,0,0,0.02)] z-10">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse ($products as $product)
                    <tr class="hover:bg-forest-50/30 transition-colors group {{ !$product->is_active ? 'bg-gray-50/50 opacity-70' : '' }}">
                        <!-- Cột 1: Mã & Ảnh -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <span class="font-mono text-gray-500 text-xs">#{{ $product->id }}</span>
                                <div class="w-12 h-12 rounded-xl border border-gray-200 overflow-hidden bg-white shadow-sm group-hover:shadow-md transition-shadow p-1 relative">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg {{ !$product->is_active ? 'grayscale' : '' }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=c8e6c9&color=2e7d32'">
                                    @if($product->discount_price > 0 && $product->is_active)
                                        <div class="absolute -top-1 -right-1 bg-organic-500 text-white text-[8px] font-bold px-1 rounded-sm z-10">SALE</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <!-- Cột 2: Sản phẩm & Danh mục -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 text-base group-hover:text-forest-700 transition-colors {{ !$product->is_active ? 'line-through text-gray-500' : '' }}">{{ $product->name }}</span>
                                <span class="text-sm text-gray-500">Danh mục: <span class="font-medium text-forest-600">{{ $product->category->name ?? 'Không có' }}</span></span>
                            </div>
                        </td>
                        <!-- Cột 3: Đặc tính (Mô tả & Xuất xứ) -->
                        <td class="px-6 py-4 max-w-xs whitespace-normal">
                            <p class="text-gray-600 line-clamp-2 text-sm">{{ $product->description ?? 'Chưa có mô tả' }}</p>
                            <span class="text-xs text-gray-400 mt-1 inline-block"><i class="fa-solid fa-location-dot mr-1"></i>{{ $product->origin ?? 'Chưa cập nhật' }}</span>
                        </td>
                        <!-- Cột 4: Giá bán & Đơn vị -->
                        <td class="px-6 py-4">
                            @if($product->discount_price > 0)
                                <div class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ / {{ $product->unit }}</div>
                                <div class="font-bold text-organic-600 text-base">{{ number_format($product->discount_price, 0, ',', '.') }}đ / {{ $product->unit }}</div>
                            @else
                                <div class="font-bold text-forest-700 text-base">{{ number_format($product->price, 0, ',', '.') }}đ / {{ $product->unit }}</div>
                            @endif
                        </td>
                        <!-- Cột 5: Tồn kho -->
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-gray-700">0</span>
                            <span class="text-xs text-gray-400 block">Sản phẩm</span>
                        </td>
                        <!-- Cột 6: Trạng thái & Thời gian -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                @php $stock = 0; @endphp
                                @if($stock <= 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-red-100 text-red-700 border border-red-200 mb-1">
                                        <i class="fa-solid fa-circle-xmark mr-1.5 text-[10px]"></i> Hết hàng
                                    </span>
                                @elseif($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-green-50 text-green-700 border border-green-100 mb-1">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Đang bán
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-gray-200 text-gray-600 border border-gray-300 mb-1">
                                        <i class="fa-solid fa-ban mr-1.5 text-[10px]"></i> Ngừng bán
                                    </span>
                                @endif
                                <div class="text-[11px] text-gray-400">Tạo: {{ $product->created_at->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-gray-400">Sửa: {{ $product->updated_at->format('d/m/Y') }}</div>
                            </div>
                        </td>
                        <!-- Cột 7: Hành động (sticky) -->
                        <td class="px-6 py-4 sticky right-0 bg-white shadow-[-4px_0_10px_rgba(0,0,0,0.02)] z-10">
                            @include('admin.products.partials.action-buttons', ['product' => $product])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                                <p>Chưa có sản phẩm nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-b-2xl">
            <span class="text-sm text-gray-500 font-medium">Hiển thị {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} trên tổng số {{ $products->total() }} sản phẩm</span>
            <div class="w-full sm:w-auto">
                {{ $products->links('vendor.pagination.greenly') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Sản Phẩm -->
@include('admin.products.partials.add-modal')

<!-- Modal Sửa Sản Phẩm -->
@include('admin.products.partials.edit-modal')

<!-- Modal Xem Chi Tiết Sản Phẩm -->
@include('admin.products.partials.show-modal')

<!-- Modal Xóa Sản Phẩm -->
@include('admin.products.partials.delete-modal')

@endsection

@push('scripts')
<script>
    const filterMenu = document.getElementById('filterMenu');
    const filterBtn = document.getElementById('filterBtn');
    const filterCountBadge = document.getElementById('filterCount');

    // Toggle ẩn/hiện Dropdown menu
    function toggleFilter() {
        filterMenu.classList.toggle('hidden');
        if (!filterMenu.classList.contains('hidden')) {
            filterBtn.classList.add('ring-2', 'ring-forest-500', 'border-forest-500');
        } else {
            filterBtn.classList.remove('ring-2', 'ring-forest-500', 'border-forest-500');
        }
    }

    // Ẩn khi click ra ngoài
    document.addEventListener('click', function(event) {
        if (!filterMenu.contains(event.target) && !filterBtn.contains(event.target)) {
            filterMenu.classList.add('hidden');
            filterBtn.classList.remove('ring-2', 'ring-forest-500', 'border-forest-500');
        }
    });

    // Hàm Làm Lại (Reset Filter)
    function resetFilter() {
        const radios = document.querySelectorAll('#filterMenu input[type="radio"]');
        radios.forEach(radio => radio.checked = false);
        updateFilterCount();
    }

    // Hàm Áp dụng (Apply Filter - Demo)
    function applyFilter() {
        updateFilterCount();
        toggleFilter();
        
        const price = document.querySelector('input[name="priceFilter"]:checked')?.value;
        const status = document.querySelector('input[name="statusFilter"]:checked')?.value;
        const discount = document.querySelector('input[name="discountFilter"]:checked')?.value;
        
        console.log("Applied Filters:", { price, status, discount });
    }

    // Hàm cập nhật số lượng bộ lọc đang chọn
    function updateFilterCount() {
        const checkedRadios = document.querySelectorAll('#filterMenu input[type="radio"]:checked');
        const count = checkedRadios.length;
        
        if (count > 0) {
            filterCountBadge.innerText = count;
            filterCountBadge.classList.remove('hidden');
        } else {
            filterCountBadge.classList.add('hidden');
        }
    }

    // ============================================
    // MODAL THÊM SẢN PHẨM
    // ============================================

    // Mở Modal Thêm Sản Phẩm
    function openAddProductModal() {
        const modal = document.getElementById('addProductModal');
        const content = document.getElementById('addProductContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    // Đóng Modal Thêm Sản Phẩm
    function closeAddProductModal() {
        const modal = document.getElementById('addProductModal');
        const content = document.getElementById('addProductContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset form và các trạng thái UI
            document.getElementById('addProductForm').reset();
            // Ẩn ô nhập đơn vị tùy chỉnh
            const unitCustom = document.getElementById('add_product_unit_custom');
            unitCustom.classList.add('hidden');
            unitCustom.removeAttribute('required');
            unitCustom.value = '';
            // Reset preview ảnh đại diện
            document.getElementById('add_product_image_preview').classList.add('hidden');
            document.getElementById('add_product_image_preview').src = '#';
            document.getElementById('add_product_image_placeholder').classList.remove('hidden');
            document.getElementById('add_product_image_overlay').classList.add('hidden');
            // Reset gallery
            resetAddProductGallery();
            // Reset đếm ký tự
            document.getElementById('add_product_char_count').innerText = '0';
            // Reset các thẻ báo lỗi
            ['add_product_name_error', 'add_product_image_error', 'add_product_gallery_error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.classList.add('hidden'); el.innerText = ''; }
            });
        }, 300);
    }

    // Toggle hiển thị ô nhập đơn vị tùy chỉnh
    function toggleAddProductCustomUnit() {
        const select = document.getElementById('add_product_unit_select');
        const customInput = document.getElementById('add_product_unit_custom');
        if (select.value === 'custom') {
            customInput.classList.remove('hidden');
            customInput.setAttribute('required', 'required');
            customInput.focus();
        } else {
            customInput.classList.add('hidden');
            customInput.removeAttribute('required');
            customInput.value = '';
        }
    }

    // Đếm ký tự mô tả sản phẩm
    function updateAddProductCharCount(element) {
        const maxLength = 2000;
        const currentLength = element.value.length;
        const countEl = document.getElementById('add_product_char_count');
        if (currentLength > maxLength) {
            element.value = element.value.substring(0, maxLength);
            countEl.innerText = maxLength;
        } else {
            countEl.innerText = currentLength;
        }
        if (currentLength >= 1900) {
            countEl.classList.add('text-organic-500');
            countEl.classList.remove('text-gray-400');
        } else {
            countEl.classList.remove('text-organic-500');
            countEl.classList.add('text-gray-400');
        }
    }

    // Preview ảnh đại diện sản phẩm (có validate file)
    function previewAddProductImage(input) {
        const errorEl = document.getElementById('add_product_image_error');
        const preview = document.getElementById('add_product_image_preview');
        const placeholder = document.getElementById('add_product_image_placeholder');
        const overlay = document.getElementById('add_product_image_overlay');
        errorEl.classList.add('hidden'); errorEl.innerText = '';

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
            // Validate kích thước và định dạng
            if (file.size > 2 * 1024 * 1024) {
                errorEl.innerText = 'Ảnh đại diện không được quá 2MB.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
            if (!allowedTypes.includes(file.type)) {
                errorEl.innerText = 'Chỉ chấp nhận định dạng: PNG, JPG, WEBP.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                overlay.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // ============================================
    // GALLERY ẢNH PHỤ (Multiple Image Preview)
    // ============================================
    const ADD_PRODUCT_MAX_GALLERY = 10;
    let addProductGalleryFiles = [];

    function previewAddProductGallery(input) {
        const errorEl = document.getElementById('add_product_gallery_error');
        errorEl.classList.add('hidden'); errorEl.innerText = '';

        const files = Array.from(input.files);
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];

        // Validate từng file trước
        for (const file of files) {
            if (file.size > 2 * 1024 * 1024) {
                errorEl.innerText = 'Mỗi ảnh chi tiết không được quá 2MB. File "' + file.name + '" quá lớn.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
            if (!allowedTypes.includes(file.type)) {
                errorEl.innerText = 'Chỉ chấp nhận định dạng: PNG, JPG, WEBP. File "' + file.name + '" không hợp lệ.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
        }

        const remaining = ADD_PRODUCT_MAX_GALLERY - addProductGalleryFiles.length;
        const toAdd = files.slice(0, remaining);

        toAdd.forEach(file => {
            addProductGalleryFiles.push(file);
        });

        renderAddProductGallery();
        // Reset input để có thể chọn lại cùng file
        input.value = '';
    }

    function renderAddProductGallery() {
        const grid = document.getElementById('add_product_gallery_grid');
        const countEl = document.getElementById('add_product_gallery_count');

        // Xóa tất cả ảnh preview cũ (giữ lại nút +)
        grid.querySelectorAll('.gallery-preview-item').forEach(el => el.remove());

        // Cập nhật số lượng
        countEl.innerText = addProductGalleryFiles.length + '/' + ADD_PRODUCT_MAX_GALLERY;

        // Render từng ảnh
        addProductGalleryFiles.forEach((file, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'gallery-preview-item aspect-square rounded-xl border border-gray-200 relative group overflow-hidden bg-white shadow-sm';

            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            img.alt = 'Gallery ' + (index + 1);
            img.src = URL.createObjectURL(file);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 w-5 h-5 bg-white/90 hover:bg-red-500 hover:text-white text-gray-600 rounded-full flex items-center justify-center text-[10px] backdrop-blur-sm transition-colors shadow-sm opacity-0 group-hover:opacity-100';
            removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            removeBtn.onclick = function() {
                addProductGalleryFiles.splice(index, 1);
                updateGalleryFileInput();
                renderAddProductGallery();
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            grid.appendChild(wrapper);
        });

        // Ẩn/hiện nút + nếu đã đạt tối đa
        const addBtn = grid.querySelector('label[for="add_product_gallery"]');
        if (addProductGalleryFiles.length >= ADD_PRODUCT_MAX_GALLERY) {
            addBtn.classList.add('hidden');
        } else {
            addBtn.classList.remove('hidden');
        }
    }

    // Đồng bộ lại DataTransfer vào input file để form submit đúng
    function updateGalleryFileInput() {
        const input = document.getElementById('add_product_gallery');
        const dt = new DataTransfer();
        addProductGalleryFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
    }

    function resetAddProductGallery() {
        addProductGalleryFiles = [];
        const grid = document.getElementById('add_product_gallery_grid');
        grid.querySelectorAll('.gallery-preview-item').forEach(el => el.remove());
        document.getElementById('add_product_gallery_count').innerText = '0/' + ADD_PRODUCT_MAX_GALLERY;
        document.getElementById('add_product_gallery').value = '';
        // Đảm bảo nút + hiển thị lại
        const addBtn = grid.querySelector('label[for="add_product_gallery"]');
        if (addBtn) addBtn.classList.remove('hidden');
    }

    // ============================================
    // FORMAT TIỀN TỆ (Inline handler - không dùng addEventListener)
    // Được gọi từ oninput="formatCurrencyInput(this)" trên input HTML
    // ============================================
    function formatCurrencyInput(el) {
        let raw = el.value.replace(/\D/g, '');
        if (raw.length > 1) raw = raw.replace(/^0+/, '') || '0';
        let formatted = raw !== '' ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
        if (el.value !== formatted) {
            el.value = formatted;
        }
    }

    // Chặn ký tự không phải số - gọi từ onkeydown="return filterCurrencyKeydown(event)"
    function filterCurrencyKeydown(e) {
        // Cho phép: Backspace, Delete, Tab, Escape, Enter, Home, End, Arrows
        if (['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'Home', 'End',
            'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) return true;
        // Cho phép Ctrl/Cmd + A, C, V, X
        if ((e.ctrlKey || e.metaKey) && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) return true;
        // Chỉ cho phép số 0-9
        return /^\d$/.test(e.key);
    }

    // ============================================
    // SUBMIT FORM THÊM: VALIDATE + XÓA DẤU CHẤM + LOADING
    // ============================================
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        // 1. Validate tên sản phẩm (chống nhập chỉ số)
        const nameInput = document.getElementById('add_product_name');
        const nameError = document.getElementById('add_product_name_error');
        if (/^\d+$/.test(nameInput.value.trim())) {
            e.preventDefault();
            nameError.innerText = 'Tên sản phẩm không được chỉ là số.';
            nameError.classList.remove('hidden');
            nameInput.focus();
            return;
        } else {
            nameError.classList.add('hidden'); nameError.innerText = '';
        }

        // 2. Xóa dấu chấm trong các ô giá trước khi gửi lên Backend (chỉ trong form Thêm)
        let addPrice = document.getElementById('add_product_price');
        let addDiscount = document.getElementById('add_product_discount_price');
        if (addPrice) addPrice.value = addPrice.value.replace(/\./g, '');
        if (addDiscount) addDiscount.value = addDiscount.value.replace(/\./g, '');

        // 3. Đồng bộ gallery files
        updateGalleryFileInput();

        // 4. Loading state
        const btn = document.getElementById('add_product_submit_btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // MODAL SỬA SẢN PHẨM
    // ============================================

    // Mở Modal Sửa Sản Phẩm
    function openEditProductModal(button) {
        let product = JSON.parse(button.getAttribute('data-product'));

        // Set action form
        document.getElementById('editProductForm').action = '/admin/products/' + product.id;

        // Điền dữ liệu text
        document.getElementById('edit_product_id').value = product.id;
        document.getElementById('edit_product_name').value = product.name;
        document.getElementById('edit_product_category').value = product.category_id;
        document.getElementById('edit_product_origin').value = product.origin || '';
        document.getElementById('edit_product_description').value = product.description || '';

        // Cập nhật đếm ký tự mô tả
        const descEl = document.getElementById('edit_product_description');
        document.getElementById('edit_product_char_count').innerText = descEl.value.length;

        // Xử lý Đơn vị (Unit)
        const unitSelect = document.getElementById('edit_product_unit_select');
        const unitCustom = document.getElementById('edit_product_unit_custom');
        const unitOptions = Array.from(unitSelect.options).map(opt => opt.value);
        if (unitOptions.includes(product.unit)) {
            unitSelect.value = product.unit;
            unitCustom.classList.add('hidden');
            unitCustom.removeAttribute('required');
            unitCustom.value = '';
        } else {
            unitSelect.value = 'custom';
            unitCustom.classList.remove('hidden');
            unitCustom.setAttribute('required', 'required');
            unitCustom.value = product.unit;
        }

        // Xử lý Giá (Tiền tệ) - format có dấu chấm ngàn
        const priceVal = product.price ? parseInt(product.price) : 0;
        document.getElementById('edit_product_price').value = priceVal > 0 ? priceVal.toLocaleString('vi-VN') : '';
        const discountVal = product.discount_price ? parseInt(product.discount_price) : 0;
        document.getElementById('edit_product_discount_price').value = discountVal > 0 ? discountVal.toLocaleString('vi-VN') : '';

        // Set Radio Button Trạng thái
        if (product.is_active == 1) {
            document.getElementById('edit_product_active_1').checked = true;
        } else {
            document.getElementById('edit_product_active_0').checked = true;
        }

        // Hiện ảnh đại diện hiện tại (nếu có)
        if (product.image) {
            const preview = document.getElementById('edit_product_image_preview');
            preview.src = '/storage/' + product.image;
            preview.classList.remove('hidden');
            document.getElementById('edit_product_image_placeholder').classList.add('hidden');
            document.getElementById('edit_product_image_overlay').classList.remove('hidden');
        }

        // Reset và render gallery (ảnh cũ từ DB + ảnh mới)
        resetEditProductGallery();
        if (product.images && product.images.length > 0) {
            product.images.forEach(img => {
                editProductExistingImages.push({ id: img.id, path: img.image_path });
            });
        }
        renderEditProductGallery();

        // Hiện Modal
        const modal = document.getElementById('editProductModal');
        const content = document.getElementById('editProductContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    // Đóng Modal Sửa Sản Phẩm
    function closeEditProductModal() {
        const modal = document.getElementById('editProductModal');
        const content = document.getElementById('editProductContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset form
            document.getElementById('editProductForm').reset();
            // Ẩn ô nhập đơn vị tùy chỉnh
            const unitCustom = document.getElementById('edit_product_unit_custom');
            unitCustom.classList.add('hidden');
            unitCustom.removeAttribute('required');
            unitCustom.value = '';
            // Reset preview ảnh đại diện
            document.getElementById('edit_product_image_preview').classList.add('hidden');
            document.getElementById('edit_product_image_preview').src = '#';
            document.getElementById('edit_product_image_placeholder').classList.remove('hidden');
            document.getElementById('edit_product_image_overlay').classList.add('hidden');
            // Reset gallery
            resetEditProductGallery();
            // Reset đếm ký tự
            document.getElementById('edit_product_char_count').innerText = '0';
            // Reset các thẻ báo lỗi
            ['edit_product_name_error', 'edit_product_image_error', 'edit_product_gallery_error'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.classList.add('hidden'); el.innerText = ''; }
            });
        }, 300);
    }

    // Toggle hiển thị ô nhập đơn vị tùy chỉnh (Edit)
    function toggleEditProductCustomUnit() {
        const select = document.getElementById('edit_product_unit_select');
        const customInput = document.getElementById('edit_product_unit_custom');
        if (select.value === 'custom') {
            customInput.classList.remove('hidden');
            customInput.setAttribute('required', 'required');
            customInput.focus();
        } else {
            customInput.classList.add('hidden');
            customInput.removeAttribute('required');
            customInput.value = '';
        }
    }

    // Đếm ký tự mô tả sản phẩm (Edit)
    function updateEditProductCharCount(element) {
        const maxLength = 2000;
        const currentLength = element.value.length;
        const countEl = document.getElementById('edit_product_char_count');
        if (currentLength > maxLength) {
            element.value = element.value.substring(0, maxLength);
            countEl.innerText = maxLength;
        } else {
            countEl.innerText = currentLength;
        }
        if (currentLength >= 1900) {
            countEl.classList.add('text-organic-500');
            countEl.classList.remove('text-gray-400');
        } else {
            countEl.classList.remove('text-organic-500');
            countEl.classList.add('text-gray-400');
        }
    }

    // Preview ảnh đại diện sản phẩm (Edit - có validate file)
    function previewEditProductImage(input) {
        const errorEl = document.getElementById('edit_product_image_error');
        const preview = document.getElementById('edit_product_image_preview');
        const placeholder = document.getElementById('edit_product_image_placeholder');
        const overlay = document.getElementById('edit_product_image_overlay');
        errorEl.classList.add('hidden'); errorEl.innerText = '';

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
            if (file.size > 2 * 1024 * 1024) {
                errorEl.innerText = 'Ảnh đại diện không được quá 2MB.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
            if (!allowedTypes.includes(file.type)) {
                errorEl.innerText = 'Chỉ chấp nhận định dạng: PNG, JPG, WEBP.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                overlay.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // ============================================
    // GALLERY ẢNH PHỤ - FORM SỬA
    // ============================================
    const EDIT_PRODUCT_MAX_GALLERY = 10;
    let editProductGalleryFiles = [];
    let editProductExistingImages = []; // Ảnh cũ từ DB: [{id, path}]
    let editProductRemovedIds = []; // ID ảnh cũ bị xóa

    function previewEditProductGallery(input) {
        const errorEl = document.getElementById('edit_product_gallery_error');
        errorEl.classList.add('hidden'); errorEl.innerText = '';

        const files = Array.from(input.files);
        const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];

        for (const file of files) {
            if (file.size > 2 * 1024 * 1024) {
                errorEl.innerText = 'Mỗi ảnh chi tiết không được quá 2MB. File "' + file.name + '" quá lớn.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
            if (!allowedTypes.includes(file.type)) {
                errorEl.innerText = 'Chỉ chấp nhận định dạng: PNG, JPG, WEBP. File "' + file.name + '" không hợp lệ.';
                errorEl.classList.remove('hidden');
                input.value = ''; return;
            }
        }

        const totalCurrent = editProductExistingImages.length + editProductGalleryFiles.length;
        const remaining = EDIT_PRODUCT_MAX_GALLERY - totalCurrent;
        const toAdd = files.slice(0, remaining);
        toAdd.forEach(file => { editProductGalleryFiles.push(file); });

        renderEditProductGallery();
        input.value = '';
    }

    function renderEditProductGallery() {
        const grid = document.getElementById('edit_product_gallery_grid');
        const countEl = document.getElementById('edit_product_gallery_count');
        const removedContainer = document.getElementById('edit_product_removed_gallery_container');

        // Xóa tất cả preview cũ
        grid.querySelectorAll('.gallery-preview-item').forEach(el => el.remove());

        // Cập nhật hidden inputs cho ảnh bị xóa
        removedContainer.innerHTML = '';
        editProductRemovedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'removed_gallery_ids[]';
            input.value = id;
            removedContainer.appendChild(input);
        });

        const totalCount = editProductExistingImages.length + editProductGalleryFiles.length;
        countEl.innerText = totalCount + '/' + EDIT_PRODUCT_MAX_GALLERY;

        // Render ảnh cũ từ DB
        editProductExistingImages.forEach((imgData, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'gallery-preview-item aspect-square rounded-xl border border-gray-200 relative group overflow-hidden bg-white shadow-sm';

            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            img.alt = 'Gallery ' + (index + 1);
            img.src = '/storage/' + imgData.path;

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 w-5 h-5 bg-white/90 hover:bg-red-500 hover:text-white text-gray-600 rounded-full flex items-center justify-center text-[10px] backdrop-blur-sm transition-colors shadow-sm opacity-0 group-hover:opacity-100';
            removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            removeBtn.onclick = function() {
                editProductRemovedIds.push(imgData.id);
                editProductExistingImages.splice(index, 1);
                renderEditProductGallery();
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            grid.appendChild(wrapper);
        });

        // Render ảnh mới (vừa chọn từ máy)
        editProductGalleryFiles.forEach((file, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'gallery-preview-item aspect-square rounded-xl border border-gray-200 relative group overflow-hidden bg-white shadow-sm';

            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            img.alt = 'New ' + (index + 1);
            img.src = URL.createObjectURL(file);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 w-5 h-5 bg-white/90 hover:bg-red-500 hover:text-white text-gray-600 rounded-full flex items-center justify-center text-[10px] backdrop-blur-sm transition-colors shadow-sm opacity-0 group-hover:opacity-100';
            removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            removeBtn.onclick = function() {
                editProductGalleryFiles.splice(index, 1);
                updateEditGalleryFileInput();
                renderEditProductGallery();
            };

            wrapper.appendChild(img);
            wrapper.appendChild(removeBtn);
            grid.appendChild(wrapper);
        });

        // Ẩn/hiện nút + nếu đã đạt tối đa
        const addBtn = grid.querySelector('label[for="edit_product_gallery"]');
        if (totalCount >= EDIT_PRODUCT_MAX_GALLERY) {
            addBtn.classList.add('hidden');
        } else {
            addBtn.classList.remove('hidden');
        }
    }

    function updateEditGalleryFileInput() {
        const input = document.getElementById('edit_product_gallery');
        const dt = new DataTransfer();
        editProductGalleryFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
    }

    function resetEditProductGallery() {
        editProductGalleryFiles = [];
        editProductExistingImages = [];
        editProductRemovedIds = [];
        const grid = document.getElementById('edit_product_gallery_grid');
        grid.querySelectorAll('.gallery-preview-item').forEach(el => el.remove());
        document.getElementById('edit_product_gallery_count').innerText = '0/' + EDIT_PRODUCT_MAX_GALLERY;
        document.getElementById('edit_product_gallery').value = '';
        document.getElementById('edit_product_removed_gallery_container').innerHTML = '';
        const addBtn = grid.querySelector('label[for="edit_product_gallery"]');
        if (addBtn) addBtn.classList.remove('hidden');
    }

    // ============================================
    // SUBMIT FORM SỬA: VALIDATE + XÓA DẤU CHẤM + LOADING
    // ============================================
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        // 1. Validate tên sản phẩm (chống nhập chỉ số)
        const nameInput = document.getElementById('edit_product_name');
        const nameError = document.getElementById('edit_product_name_error');
        if (/^\d+$/.test(nameInput.value.trim())) {
            e.preventDefault();
            nameError.innerText = 'Tên sản phẩm không được chỉ là số.';
            nameError.classList.remove('hidden');
            nameInput.focus();
            return;
        } else {
            nameError.classList.add('hidden'); nameError.innerText = '';
        }

        // 2. Xóa dấu chấm trong các ô giá trước khi gửi lên Backend (chỉ trong form Sửa)
        let editPrice = document.getElementById('edit_product_price');
        let editDiscount = document.getElementById('edit_product_discount_price');
        if (editPrice) editPrice.value = editPrice.value.replace(/\./g, '');
        if (editDiscount) editDiscount.value = editDiscount.value.replace(/\./g, '');

        // 3. Đồng bộ gallery files
        updateEditGalleryFileInput();

        // 4. Loading state
        const btn = document.getElementById('edit_product_submit_btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // MODAL XEM CHI TIẾT SẢN PHẨM
    // ============================================

    // Hàm format tiền tệ (dùng chung cho Show)
    function formatCurrency(value) {
        const num = parseInt(value) || 0;
        return num.toLocaleString('vi-VN') + ' ₫';
    }

    // Mở Modal Xem Chi Tiết
    function openShowProductModal(button) {
        const product = JSON.parse(button.getAttribute('data-product'));

        // Thông tin cơ bản
        document.getElementById('show_product_id').innerText = '#' + product.id;
        document.getElementById('show_product_name').innerText = product.name;
        document.getElementById('show_product_slug').innerText = product.slug;
        document.getElementById('show_product_category').innerText = product.category_name;
        document.getElementById('show_product_origin').querySelector('span').innerText = product.origin;

        // Giá cả & Tồn kho
        document.getElementById('show_product_price').innerText = formatCurrency(product.price);
        const discountPrice = parseInt(product.discount_price) || 0;
        document.getElementById('show_product_discount_price').innerText = discountPrice > 0 ? formatCurrency(discountPrice) : 'Không có';
        document.getElementById('show_product_unit').innerText = product.unit;

        // Tồn kho
        const stock = product.stock || 0;
        const stockEl = document.getElementById('show_product_stock');
        if (stock > 0) {
            stockEl.innerHTML = '<i class="fa-solid fa-box text-forest-500 mr-1.5"></i> ' + stock.toLocaleString('vi-VN') + ' ' + product.unit;
            stockEl.className = 'text-sm font-bold text-forest-700';
        } else {
            stockEl.innerHTML = '<i class="fa-solid fa-box-open text-red-400 mr-1.5"></i> Hết hàng (0)';
            stockEl.className = 'text-sm font-bold text-red-500';
        }

        // Trạng thái (Badge)
        const statusEl = document.getElementById('show_product_status_badge');
        if (stock <= 0) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700"><i class="fa-solid fa-circle-xmark"></i> Hết hàng</span>';
        } else if (product.is_active == 0) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600"><i class="fa-solid fa-ban"></i> Ngừng bán</span>';
        } else {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700"><i class="fa-solid fa-circle-check"></i> Đang bán</span>';
        }

        // Thời gian
        document.getElementById('show_product_created_at').innerText = product.created_at;
        document.getElementById('show_product_updated_at').innerText = product.updated_at;

        // Mô tả
        document.getElementById('show_product_description').innerText = product.description;

        // Ảnh đại diện
        const mainImg = document.getElementById('show_product_image');
        if (product.image) {
            mainImg.src = product.image;
            mainImg.alt = product.name;
        } else {
            mainImg.src = 'https://placehold.co/400x300/f3f4f6/9ca3af?text=No+Image';
            mainImg.alt = 'Không có ảnh';
        }

        // Gallery ảnh chi tiết
        const galleryGrid = document.getElementById('show_product_gallery_grid');
        const noGallery = document.getElementById('show_product_no_gallery');
        const galleryCount = document.getElementById('show_product_gallery_count');
        galleryGrid.innerHTML = '';

        if (product.images && product.images.length > 0) {
            noGallery.classList.add('hidden');
            galleryCount.innerText = product.images.length + ' ảnh';
            product.images.forEach((imgUrl, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'aspect-square rounded-xl border border-gray-200 overflow-hidden bg-white shadow-sm';
                const img = document.createElement('img');
                img.className = 'w-full h-full object-cover';
                img.src = imgUrl;
                img.alt = 'Gallery ' + (index + 1);
                wrapper.appendChild(img);
                galleryGrid.appendChild(wrapper);
            });
        } else {
            noGallery.classList.remove('hidden');
            galleryCount.innerText = '0 ảnh';
        }

        // Hiện Modal
        const modal = document.getElementById('showProductModal');
        const content = document.getElementById('showProductContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    // Đóng Modal Xem Chi Tiết
    function closeShowProductModal() {
        const modal = document.getElementById('showProductModal');
        const content = document.getElementById('showProductContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // ============================================
    // MODAL XÓA SẢN PHẨM
    // ============================================

    // Mở Modal Xác nhận Xóa
    function openDeleteProductModal(id, name) {
        document.getElementById('delete_product_name').innerText = name;
        document.getElementById('deleteProductForm').action = '/admin/products/' + id;

        const modal = document.getElementById('deleteProductModal');
        const content = document.getElementById('deleteProductContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    // Đóng Modal Xác nhận Xóa
    function closeDeleteProductModal() {
        const modal = document.getElementById('deleteProductModal');
        const content = document.getElementById('deleteProductContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
