<!-- SIDEBAR -->
<!-- Logic: Hidden on mobile by default, toggled via JS. Fixed width on desktop. -->
<aside id="sidebar" class="absolute z-20 top-0 left-0 h-full w-64 bg-forest-800 text-white transform -translate-x-full md:translate-x-0 flex flex-col shadow-2xl">
    <!-- Logo Area -->
    <div class="h-20 flex items-center justify-center border-b border-forest-700 bg-forest-900">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Greenly Logo" class="h-10 w-auto">
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
    <div class="p-4 border-t border-forest-700 bg-forest-900">
        <div class="flex items-center gap-3">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=f9a825&color=fff" alt="Admin" class="w-10 h-10 rounded-full border-2 border-white">
            <div class="overflow-hidden">
                <h4 class="text-sm font-semibold truncate">Quản trị viên</h4>
                <p class="text-xs text-forest-100 truncate">admin@greenly.com</p>
            </div>
        </div>
    </div>
</aside>
