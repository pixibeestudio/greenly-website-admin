<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --- Route nhóm Admin ---
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Nhóm Cửa hàng & Sản phẩm
    Route::get('/categories', function () {
        return view('admin.dashboard');
    })->name('categories.index');

    Route::get('/products', function () {
        return view('admin.dashboard');
    })->name('products.index');

    Route::get('/reviews', function () {
        return view('admin.dashboard');
    })->name('reviews.index');

    // Nhóm Kho hàng & Nguồn gốc
    Route::get('/suppliers', function () {
        return view('admin.dashboard');
    })->name('suppliers.index');

    Route::get('/batches', function () {
        return view('admin.dashboard');
    })->name('batches.index');

    // Nhóm Kinh doanh & Vận chuyển
    Route::get('/orders', function () {
        return view('admin.dashboard');
    })->name('orders.index');

    Route::get('/shippers', function () {
        return view('admin.dashboard');
    })->name('shippers.index');

    // Nhóm Hệ thống
    Route::get('/users', function () {
        return view('admin.dashboard');
    })->name('users.index');
});
