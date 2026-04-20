<!-- SIDEBAR -->
<!-- Logic: Hidden on mobile by default, toggled via JS. Fixed width on desktop. -->
<aside id="sidebar" class="absolute z-20 top-0 left-0 h-full w-64 bg-forest-800 text-white transform -translate-x-full md:translate-x-0 flex flex-col shadow-2xl">
    <!-- Logo Area -->
    <div class="h-20 flex items-center justify-center border-b border-forest-700 bg-forest-900">
        <div class="flex items-center gap-3">
            <!-- Logo bo tròn, viền nhẹ để nổi bật trên nền tối -->
            <div class="w-11 h-11 rounded-full overflow-hidden bg-white ring-2 ring-organic-400/60 shadow-md flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Greenly Logo" class="w-full h-full object-cover">
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-wide">GREENLY</h1>
                <p class="text-xs text-forest-100 uppercase tracking-wider">Admin Premium</p>
            </div>
        </div>
    </div>

    <!-- Menu điều hướng -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-3">
            <!-- 1. Dashboard -->
            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-chart-pie w-6 text-center"></i>
                    <span class="ml-3 font-medium">Dashboard</span>
                </a>
            </li>

            <!-- 2. Nhóm Cửa hàng & Sản phẩm -->
            <li class="pt-4 pb-1 px-4 text-xs font-bold text-forest-100 uppercase opacity-70">Cửa hàng & Sản phẩm</li>

            <!-- Danh mục -->
            <li>
                <a href="{{ route('admin.categories.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-tags w-6 text-center"></i>
                    <span class="ml-3 font-medium">Danh mục</span>
                </a>
            </li>

            <!-- Quản lý Banner -->
            <li>
                <a href="{{ route('admin.banners.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.banners.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-images w-6 text-center"></i>
                    <span class="ml-3 font-medium">Quản lý Banner</span>
                </a>
            </li>

            <!-- Sản phẩm -->
            <li>
                <a href="{{ route('admin.products.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-carrot w-6 text-center"></i>
                    <span class="ml-3 font-medium">Sản phẩm</span>
                </a>
            </li>

            <!-- Đánh giá -->
            <li>
                <a href="{{ route('admin.reviews.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.reviews.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-star w-6 text-center"></i>
                    <span class="ml-3 font-medium">Đánh giá</span>
                </a>
            </li>

            <!-- 3. Nhóm Kho hàng & Nguồn gốc -->
            <li class="pt-4 pb-1 px-4 text-xs font-bold text-forest-100 uppercase opacity-70">Kho hàng & Nguồn gốc</li>

            <!-- Nhà cung cấp -->
            <li>
                <a href="{{ route('admin.suppliers.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.suppliers.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-truck-field w-6 text-center"></i>
                    <span class="ml-3 font-medium">Nhà cung cấp</span>
                </a>
            </li>

            <!-- Lô hàng nhập -->
            <li>
                <a href="{{ route('admin.batches.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.batches.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-boxes-stacked w-6 text-center"></i>
                    <span class="ml-3 font-medium">Lô hàng nhập</span>
                </a>
            </li>

            <!-- 4. Nhóm Kinh doanh & Vận chuyển -->
            <li class="pt-4 pb-1 px-4 text-xs font-bold text-forest-100 uppercase opacity-70">Kinh doanh & Vận chuyển</li>

            <!-- Đơn hàng -->
            <li>
                <a href="{{ route('admin.orders.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.orders.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-cart-shopping w-6 text-center"></i>
                    <span class="ml-3 font-medium">Đơn hàng</span>
                </a>
            </li>

            <!-- Quản lý Shipper -->
            <li>
                <a href="{{ route('admin.shippers.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.shippers.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-motorcycle w-6 text-center"></i>
                    <span class="ml-3 font-medium">Quản lý Shipper</span>
                </a>
            </li>

            <!-- 5. Nhóm Hệ thống -->
            <li class="pt-4 pb-1 px-4 text-xs font-bold text-forest-100 uppercase opacity-70">Hệ thống</li>

            <!-- Người dùng (Khách hàng) -->
            <li>
                <a href="{{ route('admin.users.index') }}" class="nav-item flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-forest-700 text-white shadow-md' : 'hover:bg-forest-700 text-forest-100 hover:text-white' }} transition-colors">
                    <i class="fa-solid fa-users w-6 text-center"></i>
                    <span class="ml-3 font-medium">Người dùng</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Thông tin người dùng (Cuối Sidebar) -->
    @php
        $currentUser = auth()->user();
        // Nếu chưa login (ví dụ lỡ vào trang public), hiển thị placeholder an toàn
        $displayName   = $currentUser->fullname ?? 'Quản trị viên';
        $displayEmail  = $currentUser->masked_email ?? 'admin@greenly.com';
        $avatarFallback= 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=f9a825&color=fff&bold=true&rounded=true';
        $avatarUrl     = $currentUser && $currentUser->avatar
            ? asset('storage/' . $currentUser->avatar)
            : $avatarFallback;
    @endphp
    <div class="p-4 border-t border-forest-700 bg-forest-900">
        <div class="flex items-center gap-3">
            <!-- Avatar bo tròn hoàn toàn -->
            <img src="{{ $avatarUrl }}" alt="Avatar quản trị viên"
                 class="w-10 h-10 rounded-full object-cover border-2 border-organic-400 shadow-md flex-shrink-0"
                 onerror="this.src='{{ $avatarFallback }}'">
            <div class="overflow-hidden flex-1 min-w-0">
                <h4 class="text-sm font-bold truncate">{{ $displayName }}</h4>
                <p class="text-xs text-forest-100 truncate" title="Đã ẩn để bảo mật">{{ $displayEmail }}</p>
            </div>
        </div>

        <!-- Nút Đăng xuất -->
        @auth
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-forest-700/60 hover:bg-red-600/90 text-forest-100 hover:text-white text-xs font-bold transition-all border border-forest-600 hover:border-red-500">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
        @endauth
    </div>
</aside>
