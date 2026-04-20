<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Greenly Admin')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Vite (Tailwind v4) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Hiệu ứng float cho các icon trang trí ở panel trái */
        @keyframes floaty { 0%,100% { transform: translateY(0) } 50% { transform: translateY(-14px) } }
        .floaty-1 { animation: floaty 6s ease-in-out infinite; }
        .floaty-2 { animation: floaty 8s ease-in-out infinite; animation-delay: -2s; }
        .floaty-3 { animation: floaty 7s ease-in-out infinite; animation-delay: -4s; }

        /* Gradient radial cho background panel trái */
        .brand-bg {
            background:
                radial-gradient(circle at 20% 20%, rgba(249, 168, 37, 0.18), transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(129, 199, 132, 0.2), transparent 40%),
                linear-gradient(135deg, #0f3d1f 0%, #1b5e20 45%, #2e7d32 100%);
        }

        /* Fade-in cho card form */
        @keyframes fadeUp { from { opacity:0; transform: translateY(12px) } to { opacity:1; transform: translateY(0) } }
        .fade-up { animation: fadeUp 0.5s ease-out both; }
    </style>

    @stack('styles')
</head>
<body class="font-sans bg-cream-50 text-gray-800 antialiased min-h-screen">

<div class="min-h-screen flex flex-col md:flex-row">

    <!-- =========================================================
         PANEL TRÁI: BRAND + Thông điệp (ẩn trên mobile)
         ========================================================= -->
    <aside class="hidden md:flex md:w-1/2 brand-bg text-white relative overflow-hidden p-12 flex-col justify-between">
        <!-- Icons trang trí nổi -->
        <i class="fa-solid fa-leaf text-organic-400/20 text-[220px] absolute -top-10 -left-10 floaty-1"></i>
        <i class="fa-solid fa-seedling text-white/10 text-[180px] absolute top-1/3 right-10 floaty-2"></i>
        <i class="fa-solid fa-apple-whole text-organic-400/15 text-[160px] absolute bottom-10 -left-6 floaty-3"></i>

        <!-- Logo + brand name -->
        <a href="{{ route('login') }}" class="relative z-10 flex items-center gap-3 hover:opacity-90 transition-opacity">
            <div class="w-14 h-14 rounded-full overflow-hidden bg-white ring-2 ring-organic-400 shadow-xl">
                <img src="{{ asset('images/logo.png') }}" alt="Greenly Logo" class="w-full h-full object-cover">
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-wide">GREENLY</h1>
                <p class="text-xs text-forest-100 uppercase tracking-[0.2em]">Admin Premium</p>
            </div>
        </a>

        <!-- Thông điệp chính -->
        <div class="relative z-10 max-w-md">
            <h2 class="text-4xl lg:text-5xl font-bold leading-tight mb-5 drop-shadow">
                Quản lý cửa hàng <span class="text-organic-400">thực phẩm xanh</span> đơn giản hơn.
            </h2>
            <p class="text-forest-100 text-base leading-relaxed mb-8">
                Hệ thống điều hành toàn diện cho cửa hàng thực phẩm hữu cơ: quản lý sản phẩm, đơn hàng, shipper và khách hàng — tất cả trong một.
            </p>

            <ul class="space-y-3">
                <li class="flex items-center gap-3 text-sm">
                    <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-organic-400">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    <span class="text-forest-50">Thống kê doanh thu & đơn hàng thời gian thực</span>
                </li>
                <li class="flex items-center gap-3 text-sm">
                    <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-organic-400">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </span>
                    <span class="text-forest-50">Quản lý kho hàng, lô hàng và nhà cung cấp</span>
                </li>
                <li class="flex items-center gap-3 text-sm">
                    <span class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-organic-400">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <span class="text-forest-50">Bảo mật nhiều lớp, xác thực OTP qua Email</span>
                </li>
            </ul>
        </div>

        <!-- Footer panel trái -->
        <div class="relative z-10 text-xs text-forest-100/80">
            © {{ date('Y') }} Greenly. Sản phẩm sạch — Cuộc sống xanh.
        </div>
    </aside>

    <!-- =========================================================
         PANEL PHẢI: NỘI DUNG THAY ĐỔI THEO TỪNG TRANG
         ========================================================= -->
    <main class="flex-1 flex items-center justify-center p-6 md:p-12 bg-cream-50">
        <div class="w-full max-w-md fade-up">

            <!-- Logo mini cho mobile (vì panel trái bị ẩn) -->
            <div class="md:hidden flex items-center gap-3 mb-8">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-white ring-2 ring-organic-400 shadow-md">
                    <img src="{{ asset('images/logo.png') }}" alt="Greenly Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-xl font-bold text-forest-800 tracking-wide">GREENLY</h1>
                    <p class="text-[10px] text-forest-600 uppercase tracking-[0.2em]">Admin Premium</p>
                </div>
            </div>

            @yield('card')

            <!-- Ghi chú bảo mật chung -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-400 text-center flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-forest-600"></i>
                    Dữ liệu của bạn được bảo vệ bằng mã hóa SSL và xác thực 2 lớp.
                </p>
            </div>
        </div>
    </main>
</div>

@stack('scripts')

</body>
</html>
