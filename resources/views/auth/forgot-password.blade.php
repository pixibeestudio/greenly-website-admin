@extends('layouts.auth')

@section('title', 'Quên mật khẩu - Greenly Admin')

@section('card')
    <!-- Icon -->
    <div class="flex justify-center mb-6">
        <div class="w-20 h-20 rounded-full bg-organic-100 flex items-center justify-center shadow-inner">
            <i class="fa-solid fa-key text-organic-500 text-3xl"></i>
        </div>
    </div>

    <!-- Tiêu đề -->
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Quên mật khẩu?</h2>
        <p class="text-sm text-gray-500 leading-relaxed">
            Vui lòng nhập email quản trị của bạn. Chúng tôi sẽ gửi mã xác thực 6 số để bạn đặt lại mật khẩu.
        </p>
    </div>

    <!-- Flash notifications -->
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-start gap-2">
            <i class="fa-solid fa-circle-check mt-0.5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('password.forgot.send') }}" class="space-y-5" id="forgotForm">
        @csrf

        <div>
            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fa-solid fa-envelope text-forest-600 mr-1.5"></i> Email quản trị
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   autocomplete="email" required autofocus
                   placeholder="admin@greenly.com"
                   class="w-full bg-white border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-forest-500 focus:ring-forest-500' }} rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:ring-1 transition-all shadow-sm">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        <button type="submit" id="submitBtn"
                class="w-full bg-organic-500 hover:bg-organic-600 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-organic-500/30 hover:shadow-organic-500/50 transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-paper-plane"></i>
            <span>Nhận mã xác thực</span>
        </button>
    </form>

    <!-- Quay lại đăng nhập -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-forest-600 hover:text-forest-800 font-semibold transition-colors inline-flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập
        </a>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('forgotForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Đang gửi mã...</span>';
    });
</script>
@endpush
