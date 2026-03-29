<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- Route nhóm Admin ---
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return view('admin.dashboard', ['pageTitle' => 'Dashboard']);
    })->name('dashboard');

    // Nhóm Cửa hàng & Sản phẩm
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('categories');

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
