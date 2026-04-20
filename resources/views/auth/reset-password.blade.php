@extends('layouts.auth')

@section('title', 'Đặt lại mật khẩu - Greenly Admin')

@section('card')
    <!-- Icon -->
    <div class="flex justify-center mb-6">
        <div class="w-20 h-20 rounded-full bg-forest-100 flex items-center justify-center shadow-inner">
            <i class="fa-solid fa-lock text-forest-700 text-3xl"></i>
        </div>
    </div>

    <!-- Tiêu đề -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Đặt lại mật khẩu</h2>
        <p class="text-sm text-gray-500 leading-relaxed">
            Vui lòng nhập mật khẩu mới. Mật khẩu mới phải <strong>khác</strong> với mật khẩu cũ.
        </p>
    </div>

    <!-- Flash notifications -->
    @if(session('error'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form đặt lại mật khẩu -->
    <form method="POST" action="{{ route('password.reset.submit') }}" class="space-y-5" id="resetForm">
        @csrf

        <!-- Mật khẩu mới -->
        <div>
            <label for="password" class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fa-solid fa-key text-forest-600 mr-1.5"></i> Mật khẩu mới
            </label>
            <div class="relative">
                <input type="password" id="password" name="password" required
                       autocomplete="new-password"
                       placeholder="Nhập mật khẩu mới"
                       class="w-full bg-white border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-forest-500 focus:ring-forest-500' }} rounded-xl px-4 py-3 pr-11 text-sm text-gray-700 outline-none focus:ring-1 transition-all shadow-sm">
                <button type="button" onclick="togglePwd('password','icon1')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest-600 transition-colors p-1">
                    <i class="fa-solid fa-eye" id="icon1"></i>
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
            @enderror

            <!-- Checklist điều kiện mật khẩu (realtime) -->
            <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1 text-xs" id="pwdChecklist">
                <span class="flex items-center gap-1.5 text-gray-400" data-rule="len"><i class="fa-regular fa-circle w-3"></i> Từ 8-30 ký tự</span>
                <span class="flex items-center gap-1.5 text-gray-400" data-rule="case"><i class="fa-regular fa-circle w-3"></i> Chữ hoa + thường</span>
                <span class="flex items-center gap-1.5 text-gray-400" data-rule="num"><i class="fa-regular fa-circle w-3"></i> Có chữ số</span>
                <span class="flex items-center gap-1.5 text-gray-400" data-rule="special"><i class="fa-regular fa-circle w-3"></i> Ký tự đặc biệt</span>
            </div>
        </div>

        <!-- Xác nhận mật khẩu -->
        <div>
            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">
                <i class="fa-solid fa-check-double text-forest-600 mr-1.5"></i> Xác nhận mật khẩu
            </label>
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       autocomplete="new-password"
                       placeholder="Nhập lại mật khẩu mới"
                       class="w-full bg-white border border-gray-200 focus:border-forest-500 focus:ring-forest-500 rounded-xl px-4 py-3 pr-11 text-sm text-gray-700 outline-none focus:ring-1 transition-all shadow-sm">
                <button type="button" onclick="togglePwd('password_confirmation','icon2')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest-600 transition-colors p-1">
                    <i class="fa-solid fa-eye" id="icon2"></i>
                </button>
            </div>
            <p class="text-red-500 text-xs mt-1.5 hidden items-center gap-1" id="mismatchError">
                <i class="fa-solid fa-circle-exclamation"></i> Mật khẩu xác nhận không khớp.
            </p>
        </div>

        <button type="submit" id="submitBtn"
                class="w-full bg-forest-700 hover:bg-forest-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-forest-500/30 hover:shadow-forest-500/50 transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-rotate"></i>
            <span>Đổi mật khẩu</span>
        </button>
    </form>

    <!-- Quay lại -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors inline-flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Hủy và quay lại đăng nhập
        </a>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const pwd     = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const form    = document.getElementById('resetForm');
    const submitBtn = document.getElementById('submitBtn');
    const mismatch  = document.getElementById('mismatchError');

    // Toggle hiện/ẩn mật khẩu
    window.togglePwd = function (inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };

    // Kiểm tra điều kiện mật khẩu realtime
    function checkRules(value) {
        const rules = {
            len:     value.length >= 8 && value.length <= 30,
            case:    /[a-z]/.test(value) && /[A-Z]/.test(value),
            num:     /[0-9]/.test(value),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value),
        };

        Object.keys(rules).forEach(key => {
            const el = document.querySelector(`[data-rule="${key}"]`);
            if (!el) return;
            const icon = el.querySelector('i');
            if (rules[key]) {
                el.classList.remove('text-gray-400');
                el.classList.add('text-emerald-600');
                icon.classList.replace('fa-regular', 'fa-solid');
                icon.classList.replace('fa-circle', 'fa-circle-check');
            } else {
                el.classList.remove('text-emerald-600');
                el.classList.add('text-gray-400');
                icon.classList.replace('fa-solid', 'fa-regular');
                icon.classList.replace('fa-circle-check', 'fa-circle');
            }
        });

        return Object.values(rules).every(Boolean);
    }

    function validate() {
        const ok = checkRules(pwd.value);
        const match = pwd.value && pwd.value === confirm.value;

        if (confirm.value && !match) {
            mismatch.classList.remove('hidden');
            mismatch.classList.add('flex');
        } else {
            mismatch.classList.add('hidden');
            mismatch.classList.remove('flex');
        }

        submitBtn.disabled = !(ok && match);
    }

    pwd.addEventListener('input', validate);
    confirm.addEventListener('input', validate);
    validate();

    // Loading state
    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Đang xử lý...</span>';
    });
})();
</script>
@endpush
