@extends('layouts.auth')

@section('title', 'Đổi mật khẩu thành công - Greenly Admin')

@push('styles')
<style>
    /* Animation checkmark SVG draw */
    .checkmark-circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #10b981;
        fill: none;
        animation: circle-draw 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .checkmark-check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: check-draw 0.3s 0.5s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    @keyframes circle-draw {
        100% { stroke-dashoffset: 0; }
    }
    @keyframes check-draw {
        100% { stroke-dashoffset: 0; }
    }

    /* Hiệu ứng nhảy (bounce) cho vòng tròn tick */
    .success-icon-wrap {
        animation: pop 0.45s 0.75s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        transform: scale(0.9);
    }
    @keyframes pop {
        0%   { transform: scale(0.9); }
        50%  { transform: scale(1.08); }
        100% { transform: scale(1); }
    }

    /* Confetti-like sparkles */
    .sparkle {
        position: absolute;
        width: 8px; height: 8px; border-radius: 50%;
        opacity: 0;
        animation: sparkle-anim 1.2s ease-out forwards;
    }
    @keyframes sparkle-anim {
        0%   { opacity: 0; transform: translate(0, 0) scale(0); }
        30%  { opacity: 1; }
        100% { opacity: 0; transform: translate(var(--tx), var(--ty)) scale(1.2); }
    }
</style>
@endpush

@section('card')
    <!-- Icon checkmark lớn với animation -->
    <div class="flex justify-center mb-6">
        <div class="relative success-icon-wrap">
            <!-- SVG checkmark -->
            <svg class="w-28 h-28" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" />
                <path class="checkmark-check" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M14 27 l8 8 l16 -16" />
            </svg>

            <!-- Sparkles trang trí -->
            <span class="sparkle bg-organic-400" style="top:10%;left:-10%;--tx:-30px;--ty:-20px;animation-delay:0.8s"></span>
            <span class="sparkle bg-forest-400" style="top:10%;right:-10%;--tx:30px;--ty:-20px;animation-delay:0.9s"></span>
            <span class="sparkle bg-emerald-400" style="bottom:10%;left:-5%;--tx:-25px;--ty:20px;animation-delay:1.0s"></span>
            <span class="sparkle bg-yellow-400" style="bottom:10%;right:-5%;--tx:25px;--ty:20px;animation-delay:1.1s"></span>
        </div>
    </div>

    <!-- Tiêu đề + mô tả -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-emerald-600 mb-2">Đổi mật khẩu thành công!</h2>
        <p class="text-sm text-gray-500 leading-relaxed">
            Mật khẩu của bạn đã được cập nhật an toàn.<br>
            Vui lòng đăng nhập lại bằng mật khẩu mới để tiếp tục sử dụng hệ thống.
        </p>
    </div>

    <!-- Thông tin nhỏ -->
    <div class="mb-6 px-4 py-3 rounded-xl bg-forest-50 border border-forest-100 flex items-start gap-3">
        <i class="fa-solid fa-shield-halved text-forest-600 mt-0.5"></i>
        <div class="text-xs text-forest-700 leading-relaxed">
            <strong>Mẹo bảo mật:</strong> Đừng chia sẻ mật khẩu với bất kỳ ai. Hệ thống Greenly sẽ không bao giờ hỏi mật khẩu của bạn qua email.
        </div>
    </div>

    <!-- Nút quay lại đăng nhập -->
    <a href="{{ route('login') }}"
       class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-forest-500/30 hover:shadow-forest-500/50 transition-all flex items-center justify-center gap-2">
        <i class="fa-solid fa-right-to-bracket"></i>
        <span>Quay lại đăng nhập</span>
    </a>
@endsection
