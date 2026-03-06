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

    <!-- TOAST NOTIFICATION -->
    <div id="toastNotification" class="fixed top-6 right-6 z-50 transform transition-all duration-300 translate-x-[120%] opacity-0 flex items-start p-4 mb-4 text-gray-200 bg-[#1e232d] rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-gray-700/50 w-[380px]" role="alert">
        <!-- Icon Success (Xanh lá) -->
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full border-2 border-emerald-500 text-emerald-500 mr-3 mt-0.5">
            <i class="fa-solid fa-check text-sm"></i>
        </div>
        
        <!-- Nội dung -->
        <div class="ml-1 flex-1">
            <h4 class="text-base font-bold text-white mb-0.5">Thành công!</h4>
            <div class="text-sm font-normal text-gray-400" id="toastMessage">Thao tác thành công.</div>
        </div>
        
        <!-- Nút đóng (X) -->
        <button type="button" onclick="hideNotification()" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-gray-400 hover:text-white rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8 transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- TOAST ERROR NOTIFICATION -->
    <div id="errorToastNotification" class="fixed bottom-5 right-5 transform translate-x-[120%] opacity-0 transition-all duration-500 z-50 bg-white border-l-4 border-red-500 p-4 rounded-xl shadow-2xl flex items-center gap-4 min-w-[300px] max-w-sm" role="alert">
        <!-- Icon Error (Đỏ) -->
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500">
            <i class="fa-solid fa-circle-xmark text-2xl"></i>
        </div>
        <!-- Nội dung -->
        <p id="errorToastMessage" class="text-sm font-bold text-gray-800">Đã xảy ra lỗi.</p>
    </div>

    <!-- Script xử lý Toast Notification -->
    <script>
        let notificationTimeout;

        function showNotification(message = 'Thao tác thành công.') {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            
            if (message) {
                toastMessage.innerText = message;
            }
            
            // Xóa class ẩn, thêm class hiện
            toast.classList.remove('translate-x-[120%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');

            // Xóa timeout cũ nếu có
            clearTimeout(notificationTimeout);

            // Tự động ẩn sau 3 giây
            notificationTimeout = setTimeout(() => {
                hideNotification();
            }, 3000);
        }

        function hideNotification() {
            const toast = document.getElementById('toastNotification');
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-[120%]', 'opacity-0');
        }

        let errorNotificationTimeout;

        function showErrorNotification(message = 'Đã xảy ra lỗi.') {
            const toast = document.getElementById('errorToastNotification');
            const toastMessage = document.getElementById('errorToastMessage');

            if (message) {
                toastMessage.innerText = message;
            }

            toast.classList.remove('translate-x-[120%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');

            clearTimeout(errorNotificationTimeout);

            errorNotificationTimeout = setTimeout(() => {
                hideErrorNotification();
            }, 3000);
        }

        function hideErrorNotification() {
            const toast = document.getElementById('errorToastNotification');
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-[120%]', 'opacity-0');
        }

        // Tự động gọi thông báo nếu có session flash success từ Laravel
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showNotification("{{ session('success') }}");
            });
        @endif
    </script>

    @stack('scripts')
</body>
</html>
