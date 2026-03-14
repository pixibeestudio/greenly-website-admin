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

    Route::get('/reviews', function () {
        return view('admin.dashboard', ['pageTitle' => 'Đánh giá Khách hàng']);
    })->name('reviews.index');

    // Nhóm Kho hàng & Nguồn gốc
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class)->names('suppliers');

    Route::resource('batches', \App\Http\Controllers\Admin\BatchController::class)->names('batches');

    // Nhóm Kinh doanh & Vận chuyển
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/shippers', function () {
        return view('admin.dashboard', ['pageTitle' => 'Quản lý Shipper']);
    })->name('shippers.index');

    // Nhóm Hệ thống
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names('users');
    Route::patch('/users/{user}/toggle-lock', [\App\Http\Controllers\Admin\UserController::class, 'toggleLock'])->name('users.toggleLock');
});
