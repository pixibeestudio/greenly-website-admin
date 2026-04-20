@extends('layouts.auth')

@section('title', 'Đăng nhập - Greenly Admin')

@section('card')
    <!-- Tiêu đề -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Đăng nhập quản trị</h2>
        <p class="text-sm text-gray-500">Vui lòng đăng nhập bằng tài khoản admin để tiếp tục.</p>
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

    <!-- Form đăng nhập -->
    <form method="POST" action="{{ route('login.submit') }}" class="space-y-5" id="loginForm">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fa-solid fa-envelope text-forest-600 mr-1.5"></i> Email
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   autocomplete="email" required autofocus
                   placeholder="admin@greenly.com"
                   class="w-full bg-white border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-forest-500 focus:ring-forest-500' }} rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:ring-1 transition-all shadow-sm">
            @error('email')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        <!-- Mật khẩu -->
        <div>
            <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fa-solid fa-lock text-forest-600 mr-1.5"></i> Mật khẩu
            </label>
            <div class="relative">
                <input type="password" id="password" name="password"
                       autocomplete="current-password" required
                       placeholder="••••••••"
                       class="w-full bg-white border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-forest-500 focus:ring-forest-500' }} rounded-xl px-4 py-3 pr-11 text-sm text-gray-700 outline-none focus:ring-1 transition-all shadow-sm">
                <button type="button" onclick="togglePassword()" id="togglePasswordBtn"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest-600 transition-colors p-1"
                        title="Hiện/ẩn mật khẩu">
                    <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror
        </div>

        <!-- Ghi nhớ + quên mật khẩu -->
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-gray-300 text-forest-600 focus:ring-forest-500">
                <span class="text-gray-600">Ghi nhớ đăng nhập</span>
            </label>
            <a href="{{ route('password.forgot') }}" class="text-forest-600 hover:text-forest-800 font-semibold transition-colors">
                Quên mật khẩu?
            </a>
        </div>

        <!-- Nút Submit -->
        <button type="submit" id="submitBtn"
                class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-forest-500/30 hover:shadow-forest-500/50 transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>Đăng nhập</span>
        </button>
    </form>

    <!-- Thông tin bảo mật phụ -->
    <div class="mt-6 px-4 py-3 rounded-xl bg-forest-50 border border-forest-100 flex items-start gap-3">
        <i class="fa-solid fa-shield-halved text-forest-600 mt-0.5"></i>
        <div class="text-xs text-forest-700 leading-relaxed">
            <strong>Bảo mật 2 lớp:</strong> Sau khi đăng nhập, chúng tôi sẽ gửi mã OTP 6 số về email của bạn để xác thực.
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Toggle hiện/ẩn mật khẩu
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('togglePasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Loading state cho nút submit
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.classList.add('opacity-80', 'cursor-not-allowed');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Đang xử lý...</span>';
    });
</script>
@endpush
