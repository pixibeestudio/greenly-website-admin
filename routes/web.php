<?php

use Illuminate\Support\Facades\Route;

// Trang gốc: nếu đã login thì vào dashboard, chưa login thì ra trang login
Route::get('/', function () {
    return auth()->check() && auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

// --- Route Xác thực (Auth) ---
// Nhóm guest: chỉ cho phép truy cập khi CHƯA đăng nhập
Route::middleware('guest')->group(function () {
    // Bước 1: Đăng nhập bằng email + mật khẩu
    Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.submit');

    // Bước 2: Xác thực OTP sau khi đăng nhập thành công email+mk
    Route::get('/login/otp',         [\App\Http\Controllers\Auth\AuthController::class, 'showOtpForm'])->name('login.otp.show');
    Route::post('/login/otp',        [\App\Http\Controllers\Auth\AuthController::class, 'verifyOtp'])->name('login.otp.verify');
    Route::post('/login/otp/resend', [\App\Http\Controllers\Auth\AuthController::class, 'resendOtp'])->name('login.otp.resend');

    // Quên mật khẩu - 4 bước với OTP Email
    Route::get('/password/forgot',           [\App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
    Route::post('/password/forgot',          [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendOtp'])->name('password.forgot.send');
    Route::get('/password/reset/otp',        [\App\Http\Controllers\Auth\PasswordResetController::class, 'showOtpForm'])->name('password.otp.show');
    Route::post('/password/reset/otp',       [\App\Http\Controllers\Auth\PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('/password/reset/otp/resend',[\App\Http\Controllers\Auth\PasswordResetController::class, 'resendOtp'])->name('password.otp.resend');
    Route::get('/password/reset',            [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/password/reset',           [\App\Http\Controllers\Auth\PasswordResetController::class, 'resetPassword'])->name('password.reset.submit');
    Route::get('/password/reset/success',    [\App\Http\Controllers\Auth\PasswordResetController::class, 'showSuccess'])->name('password.reset.success');
});

// Đăng xuất: bắt buộc phải đăng nhập
Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Trang xác nhận thanh toán giả lập (public, quét QR từ app mở trang này)
Route::get('/payment/{orderId}/{token}', [\App\Http\Controllers\PaymentController::class, 'showPaymentPage'])->name('payment.show');
Route::post('/payment/{orderId}/{token}/confirm', [\App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payment.confirm');

// --- Route nhóm Admin (yêu cầu đăng nhập + role admin) ---
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    // Dashboard - trang chính
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    // API AJAX lấy số liệu Dashboard theo khoảng thời gian (today/week/month/year)
    Route::get('/dashboard/stats', [\App\Http\Controllers\Admin\DashboardController::class, 'getStats'])->name('dashboard.stats');

    // Nhóm Cửa hàng & Sản phẩm
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('categories');

    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->names('banners');
    Route::post('banners/{banner}/toggle-active', [\App\Http\Controllers\Admin\BannerController::class, 'toggleActive'])->name('banners.toggle-active');

    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->names('products');

    Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'destroy'])->names('reviews');
    Route::patch('/reviews/{review}/toggle-status', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleStatus'])->name('reviews.toggleStatus');
    Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Admin\ReviewController::class, 'reply'])->name('reviews.reply');

    // Nhóm Kho hàng & Nguồn gốc
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class)->names('suppliers');

    Route::resource('batches', \App\Http\Controllers\Admin\BatchController::class)->names('batches');

    // Nhóm Kinh doanh & Vận chuyển
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::post('/orders/{order}/assign-shipper', [\App\Http\Controllers\Admin\OrderController::class, 'assignShipper'])->name('orders.assign-shipper');

    Route::resource('shippers', \App\Http\Controllers\Admin\ShipperController::class)->only(['index', 'show'])->names('shippers');
    Route::patch('/shippers/{shipper}/work-status', [\App\Http\Controllers\Admin\ShipperController::class, 'updateWorkStatus'])->name('shippers.updateWorkStatus');

    // Nhóm Hệ thống
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names('users');
    Route::patch('/users/{user}/toggle-lock', [\App\Http\Controllers\Admin\UserController::class, 'toggleLock'])->name('users.toggleLock');
});
