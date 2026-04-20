<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Xử lý đăng nhập / đăng xuất cho trang quản trị Greenly.
 *
 * Luồng đăng nhập 2 bước (2FA qua Email):
 *   1. POST /login            -> Xác thực email/mật khẩu -> gửi OTP về email -> redirect OTP form
 *   2. GET  /login/otp        -> Form nhập OTP 6 số
 *   3. POST /login/otp        -> Verify OTP -> đăng nhập thật (Auth::login)
 *   4. POST /login/otp/resend -> Gửi lại OTP (có cooldown 60s)
 *
 * Chỉ cho phép tài khoản role = 'admin' và status = 'active' truy cập.
 */
class AuthController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /** Khóa session lưu trạng thái "đã qua bước 1, đang chờ nhập OTP" */
    private const PENDING_SESSION_KEY = 'auth.pending_login';

    /* ============================================================
     *  BƯỚC 1: FORM ĐĂNG NHẬP
     * ============================================================ */

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $credentials = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $remember = $request->boolean('remember');

        // 2. Kiểm tra tài khoản tồn tại + đúng mật khẩu + đúng quyền
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }

        if ($user->role !== 'admin') {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Tài khoản này không có quyền truy cập trang quản trị.']);
        }

        if (isset($user->status) && $user->status === 'locked') {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên cấp cao.']);
        }

        // 3. Gửi OTP tới email admin
        try {
            $this->otpService->sendOtp(
                email: $user->email,
                type: OtpCode::TYPE_LOGIN,
                recipientName: $user->fullname,
                ipAddress: $request->ip(),
            );
        } catch (\RuntimeException $e) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => $e->getMessage()]);
        }

        // 4. Lưu thông tin đăng nhập "đang chờ OTP" vào session (KHÔNG login ngay)
        $request->session()->put(self::PENDING_SESSION_KEY, [
            'user_id'   => $user->id,
            'remember'  => $remember,
            'issued_at' => now()->timestamp,
        ]);

        return redirect()->route('login.otp.show')
            ->with('success', 'Mã xác thực đã được gửi đến email của bạn.');
    }

    /* ============================================================
     *  BƯỚC 2: FORM OTP + VERIFY
     * ============================================================ */

    public function showOtpForm(Request $request)
    {
        $pending = $request->session()->get(self::PENDING_SESSION_KEY);
        if (!$pending || !isset($pending['user_id'])) {
            return redirect()->route('login')
                ->with('error', 'Phiên xác thực đã hết hạn. Vui lòng đăng nhập lại.');
        }

        $user = User::find($pending['user_id']);
        if (!$user) {
            $request->session()->forget(self::PENDING_SESSION_KEY);
            return redirect()->route('login')
                ->with('error', 'Tài khoản không tồn tại. Vui lòng đăng nhập lại.');
        }

        return view('auth.otp-verify', [
            'maskedEmail' => $user->masked_email,
            'context'     => 'login', // dùng chung template cho flow khác
            'ttlSeconds'  => OtpService::TTL_SECONDS,
            'resendUrl'   => route('login.otp.resend'),
            'submitUrl'   => route('login.otp.verify'),
            'backUrl'     => route('login'),
            'title'       => 'Xác thực đăng nhập',
            'description' => 'Vui lòng nhập mã 6 số chúng tôi đã gửi đến email của bạn để hoàn tất đăng nhập.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $pending = $request->session()->get(self::PENDING_SESSION_KEY);
        if (!$pending || !isset($pending['user_id'])) {
            return redirect()->route('login')
                ->with('error', 'Phiên xác thực đã hết hạn. Vui lòng đăng nhập lại.');
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ], [
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'otp.size'     => 'Mã OTP gồm đúng 6 chữ số.',
            'otp.regex'    => 'Mã OTP chỉ được chứa chữ số.',
        ]);

        $user = User::find($pending['user_id']);
        if (!$user) {
            $request->session()->forget(self::PENDING_SESSION_KEY);
            return redirect()->route('login')->with('error', 'Tài khoản không tồn tại.');
        }

        // Xác thực OTP
        $ok = $this->otpService->verify($user->email, OtpCode::TYPE_LOGIN, $request->input('otp'));
        if (!$ok) {
            return back()->withErrors(['otp' => 'Mã OTP không đúng hoặc đã hết hạn. Vui lòng thử lại.']);
        }

        // Đăng nhập thật sự
        Auth::login($user, (bool) ($pending['remember'] ?? false));
        $request->session()->regenerate();
        $request->session()->forget(self::PENDING_SESSION_KEY);

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('success', 'Đăng nhập thành công! Chào mừng ' . $user->fullname . '.');
    }

    public function resendOtp(Request $request)
    {
        $pending = $request->session()->get(self::PENDING_SESSION_KEY);
        if (!$pending || !isset($pending['user_id'])) {
            return redirect()->route('login')
                ->with('error', 'Phiên xác thực đã hết hạn. Vui lòng đăng nhập lại.');
        }

        $user = User::find($pending['user_id']);
        if (!$user) {
            $request->session()->forget(self::PENDING_SESSION_KEY);
            return redirect()->route('login')->with('error', 'Tài khoản không tồn tại.');
        }

        try {
            $this->otpService->sendOtp(
                email: $user->email,
                type: OtpCode::TYPE_LOGIN,
                recipientName: $user->fullname,
                ipAddress: $request->ip(),
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }

        return back()->with('success', 'Đã gửi lại mã OTP. Vui lòng kiểm tra email.');
    }

    /* ============================================================
     *  ĐĂNG XUẤT
     * ============================================================ */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Bạn đã đăng xuất thành công.');
    }
}
