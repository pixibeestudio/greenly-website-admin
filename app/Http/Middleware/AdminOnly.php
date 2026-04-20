<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware chỉ cho phép tài khoản có role = 'admin' truy cập.
 * Nếu chưa đăng nhập -> redirect /login.
 * Nếu đã đăng nhập nhưng không phải admin -> logout + báo lỗi.
 */
class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Chưa đăng nhập -> chuyển hướng tới trang login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để truy cập trang quản trị.');
        }

        // Đã đăng nhập nhưng không phải Admin -> đăng xuất vì không đủ quyền
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Bạn không có quyền truy cập trang quản trị.']);
        }

        return $next($request);
    }
}
