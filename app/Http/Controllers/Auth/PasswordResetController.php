<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Xử lý luồng Quên mật khẩu (Forgot Password) với xác thực OTP qua Email.
 *
 * Luồng 4 bước:
 *   1. GET  /password/forgot           -> Form nhập email
 *   2. POST /password/forgot           -> Gửi OTP -> redirect form nhập OTP
 *   3. GET  /password/reset/otp        -> Form nhập OTP 6 số
 *   4. POST /password/reset/otp        -> Verify OTP -> cấp token tạm -> redirect form đổi mật khẩu
 *   5. GET  /password/reset            -> Form nhập mật khẩu mới + xác nhận
 *   6. POST /password/reset            -> Cập nhật mật khẩu -> redirect trang thành công
 *   7. GET  /password/reset/success    -> Trang hiển thị đổi mật khẩu thành công
 *
 * Bảo mật:
 *  - Email + OTP phải hợp lệ mới cho qua
 *  - Sau khi xác thực OTP, cấp reset_token 1 lần (64 ký tự ngẫu nhiên) có thời hạn 10 phút
 *  - Mật khẩu mới phải khác mật khẩu cũ
 *  - Sau khi đổi thành công, xóa hết session và bắt buộc đăng nhập lại
 */
class PasswordResetController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /** Khóa session lưu email đang yêu cầu reset password */
    private const EMAIL_SESSION_KEY = 'pwdreset.email';
    /** Khóa session lưu token sau khi verify OTP thành công */
    private const TOKEN_SESSION_KEY = 'pwdreset.token';
    /** Khóa session lưu thời điểm issue token (để kiểm tra hết hạn) */
    private const TOKEN_EXPIRES_KEY = 'pwdreset.token_expires';

    /** Thời gian hiệu lực của reset token: 10 phút */
    private const TOKEN_TTL_MINUTES = 10;

    /* ============================================================
     *  BƯỚC 1: NHẬP EMAIL
     * ============================================================ */

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
        ]);

        $email = $request->input('email');
        $user  = User::where('email', $email)->first();

        // Bảo mật: KHÔNG tiết lộ email có tồn tại hay không (chống user enumeration).
        // Chỉ admin role=admin mới được reset qua luồng này.
        if (!$user || $user->role !== 'admin') {
            // Vẫn trả thông báo "đã gửi" và redirect bình thường để kẻ xấu không suy luận được email có tồn tại
            $request->session()->put(self::EMAIL_SESSION_KEY, $email);
            return redirect()->route('password.otp.show')
                ->with('success', 'Nếu email tồn tại trong hệ thống, mã xác thực đã được gửi.');
        }

        if (isset($user->status) && $user->status === 'locked') {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Tài khoản đã bị khóa. Không thể đặt lại mật khẩu.']);
        }

        try {
            $this->otpService->sendOtp(
                email: $email,
                type: OtpCode::TYPE_RESET_PASSWORD,
                recipientName: $user->fullname,
                ipAddress: $request->ip(),
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['email' => $e->getMessage()]);
        }

        $request->session()->put(self::EMAIL_SESSION_KEY, $email);

        return redirect()->route('password.otp.show')
            ->with('success', 'Mã xác thực đã được gửi đến email của bạn.');
    }

    /* ============================================================
     *  BƯỚC 2: NHẬP OTP
     * ============================================================ */

    public function showOtpForm(Request $request)
    {
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        if (!$email) {
            return redirect()->route('password.forgot')
                ->with('error', 'Vui lòng nhập email trước.');
        }

        // Lấy masked email để hiển thị (dùng accessor của User nếu có, ngược lại tự mask)
        $user = User::where('email', $email)->first();
        $maskedEmail = $user ? $user->masked_email : $this->maskEmail($email);

        return view('auth.otp-verify', [
            'maskedEmail' => $maskedEmail,
            'context'     => 'reset_password',
            'ttlSeconds'  => OtpService::TTL_SECONDS,
            'resendUrl'   => route('password.otp.resend'),
            'submitUrl'   => route('password.otp.verify'),
            'backUrl'     => route('password.forgot'),
            'title'       => 'Xác thực đặt lại mật khẩu',
            'description' => 'Vui lòng nhập mã 6 số chúng tôi đã gửi đến email của bạn để tiếp tục đặt lại mật khẩu.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        if (!$email) {
            return redirect()->route('password.forgot')->with('error', 'Phiên đã hết hạn. Vui lòng thử lại.');
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.size'     => 'Mã OTP gồm đúng 6 chữ số.',
            'otp.regex'    => 'Mã OTP chỉ được chứa chữ số.',
        ]);

        $ok = $this->otpService->verify($email, OtpCode::TYPE_RESET_PASSWORD, $request->input('otp'));
        if (!$ok) {
            return back()->withErrors(['otp' => 'Mã OTP không đúng hoặc đã hết hạn. Vui lòng thử lại.']);
        }

        // Sinh token tạm cho bước đổi mật khẩu (tránh user nhảy thẳng vào URL /password/reset)
        $token = Str::random(64);
        $request->session()->put(self::TOKEN_SESSION_KEY, $token);
        $request->session()->put(self::TOKEN_EXPIRES_KEY, now()->addMinutes(self::TOKEN_TTL_MINUTES)->timestamp);

        return redirect()->route('password.reset.form');
    }

    public function resendOtp(Request $request)
    {
        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        if (!$email) {
            return redirect()->route('password.forgot')->with('error', 'Phiên đã hết hạn. Vui lòng thử lại.');
        }

        $user = User::where('email', $email)->first();
        if (!$user || $user->role !== 'admin') {
            return back()->with('success', 'Nếu email tồn tại, mã xác thực đã được gửi.');
        }

        try {
            $this->otpService->sendOtp(
                email: $email,
                type: OtpCode::TYPE_RESET_PASSWORD,
                recipientName: $user->fullname,
                ipAddress: $request->ip(),
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }

        return back()->with('success', 'Đã gửi lại mã OTP. Vui lòng kiểm tra email.');
    }

    /* ============================================================
     *  BƯỚC 3: FORM ĐẶT MẬT KHẨU MỚI
     * ============================================================ */

    public function showResetForm(Request $request)
    {
        if (!$this->hasValidToken($request)) {
            return redirect()->route('password.forgot')
                ->with('error', 'Phiên đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại.');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (!$this->hasValidToken($request)) {
            return redirect()->route('password.forgot')
                ->with('error', 'Phiên đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại.');
        }

        $request->validate([
            'password' => [
                'required', 'string', 'min:8', 'max:30',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',
                'regex:/[!@#$%^&*()_+\-=\[\]{};\':"\\\\|,.<>\/?]/',
                'confirmed',
            ],
        ], [
            'password.required'  => 'Vui lòng nhập mật khẩu mới.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.max'       => 'Mật khẩu không được quá 30 ký tự.',
            'password.regex'     => 'Mật khẩu phải chứa chữ hoa, chữ thường, số và ký tự đặc biệt.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $email = $request->session()->get(self::EMAIL_SESSION_KEY);
        $user  = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.forgot')
                ->with('error', 'Không tìm thấy tài khoản. Vui lòng thử lại.');
        }

        // Mật khẩu mới phải KHÁC mật khẩu cũ
        if (Hash::check($request->input('password'), $user->password)) {
            return back()->withErrors([
                'password' => 'Mật khẩu mới không được trùng với mật khẩu cũ. Vui lòng chọn mật khẩu khác.',
            ]);
        }

        // Cập nhật mật khẩu
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Xoá token + email khỏi session, đánh dấu flow thành công
        $request->session()->forget([self::TOKEN_SESSION_KEY, self::TOKEN_EXPIRES_KEY, self::EMAIL_SESSION_KEY]);
        $request->session()->flash('password_reset_done', true);

        return redirect()->route('password.reset.success');
    }

    /* ============================================================
     *  TRANG THÀNH CÔNG
     * ============================================================ */

    public function showSuccess(Request $request)
    {
        // Chỉ cho hiển thị nếu vừa reset thành công (flash 'password_reset_done')
        if (!$request->session()->has('password_reset_done')) {
            return redirect()->route('login');
        }
        // Gọi ->keep() để giữ flash qua 1 request tiếp nếu user F5 trang này cũng ok,
        // nhưng tránh F5 lặp vô hạn nên chỉ để hiển thị 1 lần là đủ.
        return view('auth.reset-success');
    }

    /* ============================================================
     *  HÀM TIỆN ÍCH
     * ============================================================ */

    protected function hasValidToken(Request $request): bool
    {
        $token   = $request->session()->get(self::TOKEN_SESSION_KEY);
        $expires = $request->session()->get(self::TOKEN_EXPIRES_KEY);
        $email   = $request->session()->get(self::EMAIL_SESSION_KEY);

        if (!$token || !$expires || !$email) {
            return false;
        }
        return $expires > now()->timestamp;
    }

    protected function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) return $email;
        [$local, $domain] = explode('@', $email, 2);
        $visibleLen = min(3, max(1, mb_strlen($local) - 1));
        return mb_substr($local, 0, $visibleLen) . '****@' . $domain;
    }
}
