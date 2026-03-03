<!-- Header -->
<header class="h-20 bg-white/80 backdrop-blur-md shadow-sm flex items-center justify-between px-6 sticky top-0 z-10">
    <div class="flex items-center gap-4">
        <!-- Nút Hamburger (Mobile) -->
        <button onclick="toggleSidebar()" class="p-2 rounded-lg hover:bg-gray-100 text-forest-800 md:hidden focus:outline-none">
            <i class="fa-solid fa-bars text-2xl"></i>
        </button>
        <!-- Nút Toggle Sidebar (Desktop) -->
        <button onclick="toggleSidebarDesktop()" class="hidden md:block p-2 rounded-lg hover:bg-gray-100 text-forest-800 focus:outline-none">
            <i class="fa-solid fa-outdent text-xl"></i>
        </button>

        <h2 id="page-title" class="text-2xl font-bold text-forest-800">@yield('page-title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center gap-4">
        <!-- Ô tìm kiếm -->
        <div class="hidden sm:flex items-center bg-cream-100 rounded-full px-4 py-2 border border-transparent focus-within:border-organic-400 focus-within:bg-white transition-all">
            <i class="fa-solid fa-search text-gray-400"></i>
            <input type="text" placeholder="Tìm kiếm..." class="bg-transparent border-none focus:ring-0 ml-2 text-sm w-48 outline-none text-gray-600">
        </div>

        <!-- Chuông thông báo -->
        <button class="relative p-2 rounded-full hover:bg-gray-100 text-gray-500 transition-colors">
            <i class="fa-regular fa-bell text-xl"></i>
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
        </button>
    </div>
</header>
