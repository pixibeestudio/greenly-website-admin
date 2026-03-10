@extends('layouts.admin')

@section('title', 'Danh sách Sản phẩm - Greenly Admin')

@section('page-title', 'Danh sách Sản phẩm')

@push('styles')
<style>
    /* Custom scrollbar cho filter dropdown nếu nội dung dài */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
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

    <!-- 1. TOP SECTION: STATS CARDS & TOOLBAR -->
    <div class="flex flex-col lg:flex-row justify-between items-end gap-6 mb-8">

        <!-- 4 Card Thống kê -->
        <div class="w-full lg:w-1/2 grid grid-cols-2 gap-4">
             <!-- Thẻ 1: Tổng sản phẩm -->
             <div class="bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
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
             <div class="bg-gradient-to-br from-red-700 via-red-600 to-red-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
                <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-triangle-exclamation text-8xl"></i>
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-[11px] font-bold uppercase tracking-wider mb-1">Đang hết hàng</p>
                        <div class="flex items-baseline gap-2">
                            <h3 class="text-3xl font-bold text-white drop-shadow-md">0</h3>
                            <span class="text-[9px] text-red-100 font-bold uppercase bg-red-900/40 px-1.5 py-0.5 rounded shadow-sm animate-pulse border border-red-400/50">Cần nhập!</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-yellow-300 text-xl shadow-inner">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
             </div>

             <!-- Thẻ 3: Đang bán -->
             <div class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
                <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-store text-8xl"></i>
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider mb-1">Đang mở bán</p>
                        <h3 class="text-3xl font-bold text-white drop-shadow-md">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-white text-xl shadow-inner">
                        <i class="fa-solid fa-store"></i>
                    </div>
                </div>
             </div>

             <!-- Thẻ 4: Ngừng kinh doanh -->
             <div class="bg-gradient-to-br from-slate-700 via-slate-600 to-slate-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all">
                <div class="absolute -right-4 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                    <i class="fa-solid fa-ban text-8xl"></i>
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-slate-200 text-[11px] font-bold uppercase tracking-wider mb-1">Ngừng kinh doanh</p>
                        <h3 class="text-3xl font-bold text-white drop-shadow-md">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-xl flex items-center justify-center text-slate-100 text-xl shadow-inner">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                </div>
             </div>
        </div>

        <!-- Thanh công cụ (Bộ lọc & Thêm mới) -->
        <div class="w-full lg:w-1/2 flex flex-col sm:flex-row justify-end items-center gap-4">

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

    <!-- 2. BẢNG DỮ LIỆU SẢN PHẨM -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-xs font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">ID</th>
                        <th class="px-6 py-4">Hình ảnh</th>
                        <th class="px-6 py-4">Thông tin <br/><span class="text-[10px] text-gray-400 font-normal normal-case">(name, unit, slug)</span></th>
                        <th class="px-6 py-4">Danh mục <br/><span class="text-[10px] text-gray-400 font-normal normal-case">(category_id)</span></th>
                        <th class="px-6 py-4">Giá <br/><span class="text-[10px] text-gray-400 font-normal normal-case">(price, discount_price)</span></th>
                        <th class="px-6 py-4">Trạng thái <br/><span class="text-[10px] text-gray-400 font-normal normal-case">(is_active)</span></th>
                        <th class="px-6 py-4 max-w-xs">Mô tả & Xuất xứ <br/><span class="text-[10px] text-gray-400 font-normal normal-case">(description, origin)</span></th>
                        <th class="px-6 py-4">Ngày tạo <br/><span class="text-[10px] text-gray-400 font-normal normal-case">(created_at)</span></th>
                        <th class="px-6 py-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">

                    @forelse ($products as $product)
                    <tr class="hover:bg-forest-50/30 transition-colors group {{ !$product->is_active ? 'bg-gray-50/50 opacity-70' : '' }}">
                        <td class="px-6 py-4 text-center font-mono text-gray-500">#{{ $product->id }}</td>
                        <td class="px-6 py-4">
                            <div class="w-12 h-12 rounded-xl border border-gray-200 overflow-hidden bg-white shadow-sm group-hover:shadow-md transition-shadow p-1 relative">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg {{ !$product->is_active ? 'grayscale' : '' }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&background=c8e6c9&color=2e7d32'">
                                @if($product->discount_price > 0 && $product->is_active)
                                    <div class="absolute -top-1 -right-1 bg-organic-500 text-white text-[8px] font-bold px-1 rounded-sm z-10">SALE</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 text-base group-hover:text-forest-700 transition-colors {{ !$product->is_active ? 'line-through text-gray-500' : '' }}">{{ $product->name }}</span>
                            <div class="text-[11px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full inline-block mt-1">ĐVT: {{ $product->unit }}</div>
                            <div class="text-[11px] text-gray-400 font-mono mt-0.5">/{{ $product->slug }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-gray-800">{{ $product->category->name ?? 'Không có' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($product->discount_price > 0)
                                <div class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                <div class="font-bold text-organic-600 text-base">{{ number_format($product->discount_price, 0, ',', '.') }}đ</div>
                            @else
                                <div class="font-bold text-forest-700 text-base">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Đang bán
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-gray-200 text-gray-600 border border-gray-300">
                                    <i class="fa-solid fa-ban mr-1.5 text-[10px]"></i> Ngừng bán
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 max-w-xs whitespace-normal">
                            <p class="text-gray-600 line-clamp-2 text-sm">{{ $product->description ?? 'Không có mô tả' }}</p>
                            @if($product->origin)
                                <div class="text-[11px] text-gray-400 mt-1"><i class="fa-solid fa-location-dot mr-1"></i>{{ $product->origin }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 font-medium">{{ $product->created_at->format('d/m/Y') }}</div>
                            <div class="text-[11px] text-gray-400">{{ $product->created_at->format('H:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @include('admin.products.partials.action-buttons', ['product' => $product])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-gray-500">
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

    // Placeholder cho các hàm modal sản phẩm (sẽ implement khi tạo modal)
    function openAddProductModal() {
        console.log('openAddProductModal() - chưa implement');
    }
</script>
@endpush