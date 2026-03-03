<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Greenly Admin')</title>

    <!-- Google Fonts (Quicksand) -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite Assets (Tailwind CSS v4) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-cream-50 text-gray-800 font-sans antialiased overflow-hidden">

    <div class="flex h-screen relative">

        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- MAIN CONTENT -->
        <div id="main-wrapper" class="flex-1 flex flex-col h-screen w-full md:ml-64 transition-all duration-300 relative">

            <!-- Header -->
            @include('partials.header')

            <!-- Khu vực nội dung chính có thể cuộn -->
            <main id="main-content" class="flex-1 overflow-y-auto p-4 md:p-6 bg-cream-50 scroll-smooth">
                @yield('content')
            </main>
        </div>

        <!-- Overlay cho Sidebar trên Mobile -->
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-10 hidden md:hidden"></div>
    </div>

    <!-- JavaScript xử lý Sidebar -->
    <script>
        // Toggle Sidebar Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Toggle Desktop Sidebar (dùng custom CSS class, không dùng Tailwind dynamic)
        function toggleSidebarDesktop() {
            document.getElementById('sidebar').classList.toggle('sidebar-desktop-hidden');
            document.getElementById('main-wrapper').classList.toggle('sidebar-desktop-hidden');
        }
    </script>

    @stack('scripts')
</body>
</html>
