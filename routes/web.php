<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Trang xác nhận thanh toán giả lập (public, quét QR từ app mở trang này)
Route::get('/payment/{orderId}/{token}', [\App\Http\Controllers\PaymentController::class, 'showPaymentPage'])->name('payment.show');
Route::post('/payment/{orderId}/{token}/confirm', [\App\Http\Controllers\PaymentController::class, 'processPayment'])->name('payment.confirm');

// --- Route nhóm Admin ---
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        $today = \Carbon\Carbon::today();

        // 4 KPI Cards
        $todayOrders = \App\Models\Order::whereDate('created_at', $today)->count();
        $todayRevenue = \App\Models\Order::where('order_status', 'delivered')
            ->whereDate('updated_at', $today)
            ->sum('total_money');
        $totalCustomers = \App\Models\User::where('role', 'customer')->count();
        $totalProducts = \App\Models\Product::count();

        // 5 đơn hàng mới nhất
        $recentOrders = \App\Models\Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Thống kê trạng thái đơn hàng (cho biểu đồ Donut)
        $orderStatusCounts = [
            'delivered'  => \App\Models\Order::where('order_status', 'delivered')->count(),
            'pending'    => \App\Models\Order::where('order_status', 'pending')->count(),
            'processing' => \App\Models\Order::where('order_status', 'processing')->count(),
            'shipping'   => \App\Models\Order::where('order_status', 'shipping')->count(),
            'cancelled'  => \App\Models\Order::where('order_status', 'cancelled')->count(),
        ];

        // Top 5 sản phẩm bán chạy
        $topProducts = \App\Models\Product::withSum('batches', 'current_quantity')
            ->withCount('orderDetails')
            ->orderByDesc('order_details_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'todayOrders', 'todayRevenue', 'totalCustomers', 'totalProducts',
            'recentOrders', 'orderStatusCounts', 'topProducts'
        ));
    })->name('dashboard');

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
