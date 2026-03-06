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

    <!-- TOAST NOTIFICATION THÀNH CÔNG -->
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

    <!-- TOAST NOTIFICATION THẤT BẠI -->
    <div id="errorToastNotification" class="fixed top-6 right-6 z-50 transform transition-all duration-300 translate-x-[120%] opacity-0 flex items-start p-4 mb-4 text-red-800 bg-red-50 rounded-xl shadow-[0_8px_30px_rgb(220,38,38,0.2)] border border-red-200 w-[380px]" role="alert">
        <!-- Icon Error (Đỏ) -->
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-full border-2 border-red-500 text-red-500 mr-3 mt-0.5">
            <i class="fa-solid fa-xmark text-sm"></i>
        </div>
        
        <!-- Nội dung -->
        <div class="ml-1 flex-1">
            <h4 class="text-base font-bold text-red-800 mb-0.5">Thất bại!</h4>
            <div class="text-sm font-normal text-red-600" id="errorToastMessage">Có lỗi xảy ra.</div>
        </div>
        
        <!-- Nút đóng (X) -->
        <button type="button" onclick="hideErrorNotification()" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-red-400 hover:text-red-600 rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8 transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Script xử lý Toast Notification -->
    <script>
        let notificationTimeout;
        let errorNotificationTimeout;

        function showNotification(message = 'Thao tác thành công.') {
            const toast = document.getElementById('toastNotification');
            const toastMessage = document.getElementById('toastMessage');
            
            if (message) {
                toastMessage.innerText = message;
            }
            
            // Xóa class ẩn, thêm class hiện
            toast.classList.remove('translate-x-[120%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');

            // Tự động ẩn sau 4 giây
            clearTimeout(notificationTimeout);
            notificationTimeout = setTimeout(hideNotification, 4000);
        }

        function hideNotification() {
            const toast = document.getElementById('toastNotification');
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-[120%]', 'opacity-0');
        }

        function showErrorNotification(message = 'Có lỗi xảy ra.') {
            const toast = document.getElementById('errorToastNotification');
            const toastMessage = document.getElementById('errorToastMessage');
            
            if (message) {
                toastMessage.innerText = message;
            }
            
            // Xóa class ẩn, thêm class hiện
            toast.classList.remove('translate-x-[120%]', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');

            // Tự động ẩn sau 4 giây
            clearTimeout(errorNotificationTimeout);
            errorNotificationTimeout = setTimeout(hideErrorNotification, 4000);
        }

        function hideErrorNotification() {
            const toast = document.getElementById('errorToastNotification');
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-[120%]', 'opacity-0');
        }
    </script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showNotification('{{ session("success") }}');
        });
    </script>
    @endif

    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showErrorNotification('{{ $errors->first() }}');
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>
