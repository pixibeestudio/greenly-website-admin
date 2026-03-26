<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Danh mục sản phẩm (public, không cần auth)
Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);

// API Sản phẩm (public, không cần auth)
Route::get('/products/discounted', [\App\Http\Controllers\Api\ProductController::class, 'getDiscountedProducts']);
Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'show']);

// API Xác thực (public, không cần auth)
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
