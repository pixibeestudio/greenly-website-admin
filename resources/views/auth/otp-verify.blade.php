@extends('layouts.auth')

@section('title', $title . ' - Greenly Admin')

@section('card')
    <!-- Icon nổi bật phía trên -->
    <div class="flex justify-center mb-6">
        <div class="relative">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-forest-100 to-organic-100 flex items-center justify-center shadow-inner">
                <i class="fa-solid fa-shield-halved text-forest-700 text-4xl"></i>
            </div>
            <!-- Hiệu ứng pulse -->
            <span class="absolute -top-1 -right-1 flex h-5 w-5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-organic-400 opacity-60"></span>
                <span class="relative inline-flex rounded-full h-5 w-5 bg-organic-500 items-center justify-center">
                    <i class="fa-solid fa-lock text-[9px] text-white"></i>
                </span>
            </span>
        </div>
    </div>

    <!-- Tiêu đề -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2 uppercase tracking-wide">{{ $title }}</h2>
        <p class="text-sm text-gray-500 leading-relaxed">
            {{ $description }}<br>
            <span class="font-mono font-bold text-gray-700">{{ $maskedEmail }}</span>
        </p>
        <p class="text-xs text-gray-400 mt-2">
            Mã xác thực có giá trị trong <span class="font-bold text-forest-600" id="otpCountdown">{{ $ttlSeconds }}s</span>
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

    @if($errors->has('otp'))
        <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <span>{{ $errors->first('otp') }}</span>
        </div>
    @endif

    <!-- Form OTP -->
    <form method="POST" action="{{ $submitUrl }}" id="otpForm">
        @csrf
        <!-- Hidden field gộp 6 số -->
        <input type="hidden" name="otp" id="otpValue" value="">

        <!-- 6 ô input -->
        <div class="flex justify-center gap-2 sm:gap-3 mb-6" id="otpInputs">
            @for($i = 0; $i < 6; $i++)
                <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code"
                       data-index="{{ $i }}"
                       class="otp-digit w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl sm:text-3xl font-bold text-gray-800 bg-white border-2 border-gray-200 rounded-xl outline-none focus:border-forest-500 focus:ring-2 focus:ring-forest-500/30 transition-all shadow-sm">
            @endfor
        </div>

        <!-- Nút Xác nhận -->
        <button type="submit" id="submitOtpBtn" disabled
                class="w-full bg-organic-500 hover:bg-organic-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold text-sm py-3.5 rounded-xl shadow-lg shadow-organic-500/30 hover:shadow-organic-500/50 transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ $context === 'login' ? 'Xác nhận đăng nhập' : 'Tiếp tục' }}</span>
        </button>
    </form>

    <!-- Gửi lại OTP + Quay lại -->
    <div class="mt-6 space-y-3">
        <div class="text-center text-sm text-gray-500">
            Chưa nhận được mã?
            <form method="POST" action="{{ $resendUrl }}" class="inline" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn"
                        class="text-forest-600 hover:text-forest-800 font-bold transition-colors disabled:text-gray-400 disabled:cursor-not-allowed">
                    Gửi lại mã <span id="resendCooldown" class="hidden"></span>
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ $backUrl }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const inputs       = document.querySelectorAll('.otp-digit');
    const hiddenInput  = document.getElementById('otpValue');
    const submitBtn    = document.getElementById('submitOtpBtn');
    const form         = document.getElementById('otpForm');
    const countdownEl  = document.getElementById('otpCountdown');
    const resendBtn    = document.getElementById('resendBtn');
    const resendCooldownEl = document.getElementById('resendCooldown');

    // ============================================
    // 1. XỬ LÝ NHẬP OTP 6 Ô
    // ============================================
    function updateHiddenValue() {
        const value = Array.from(inputs).map(i => i.value).join('');
        hiddenInput.value = value;
        submitBtn.disabled = value.length !== 6;
    }

    inputs.forEach((input, idx) => {
        // Nhập số: chỉ chấp nhận 1 số, tự nhảy ô kế tiếp
        input.addEventListener('input', function (e) {
            const v = this.value.replace(/[^0-9]/g, '');
            this.value = v.slice(-1); // chỉ giữ ký tự cuối

            if (this.value && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
            updateHiddenValue();

            // Auto-submit khi đủ 6 số
            if (hiddenInput.value.length === 6) {
                // Kiểm tra toàn số
                if (/^\d{6}$/.test(hiddenInput.value)) {
                    setTimeout(() => form.requestSubmit(), 150);
                }
            }
        });

        // Backspace về ô trước
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                inputs[idx - 1].focus();
                inputs[idx - 1].value = '';
                updateHiddenValue();
            } else if (e.key === 'ArrowLeft' && idx > 0) {
                inputs[idx - 1].focus();
            } else if (e.key === 'ArrowRight' && idx < inputs.length - 1) {
                inputs[idx + 1].focus();
            }
        });

        // Paste 6 số một lần
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
            if (!pasted) return;
            pasted.split('').forEach((ch, i) => {
                if (i < inputs.length) inputs[i].value = ch;
            });
            updateHiddenValue();
            if (pasted.length === 6) {
                inputs[5].focus();
                setTimeout(() => form.requestSubmit(), 150);
            } else {
                inputs[pasted.length].focus();
            }
        });
    });

    // Focus ô đầu khi trang load
    window.addEventListener('DOMContentLoaded', () => inputs[0].focus());

    // ============================================
    // 2. LOADING STATE KHI SUBMIT
    // ============================================
    form.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Đang xác thực...</span>';
    });

    // ============================================
    // 3. ĐẾM NGƯỢC HIỆU LỰC OTP
    // ============================================
    let remaining = {{ (int) $ttlSeconds }};
    const countdownTimer = setInterval(() => {
        remaining--;
        if (remaining <= 0) {
            clearInterval(countdownTimer);
            countdownEl.textContent = 'Đã hết hạn';
            countdownEl.classList.remove('text-forest-600');
            countdownEl.classList.add('text-red-600');
            // Disable tất cả input OTP
            inputs.forEach(i => i.disabled = true);
            submitBtn.disabled = true;
            return;
        }
        countdownEl.textContent = remaining + 's';
    }, 1000);

    // ============================================
    // 4. COOLDOWN NÚT "GỬI LẠI MÃ" (60s)
    // ============================================
    let resendCooldown = 60;

    function startResendCooldown() {
        resendBtn.disabled = true;
        resendCooldownEl.classList.remove('hidden');
        const timer = setInterval(() => {
            resendCooldown--;
            if (resendCooldown <= 0) {
                clearInterval(timer);
                resendBtn.disabled = false;
                resendCooldownEl.classList.add('hidden');
                resendCooldown = 60;
                return;
            }
            resendCooldownEl.textContent = `(${resendCooldown}s)`;
        }, 1000);
    }
    // Bắt đầu cooldown ngay khi vào trang (vì vừa nhận mã)
    startResendCooldown();
})();
</script>
@endpush
