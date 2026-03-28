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
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// API Giỏ hàng (cần xác thực Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/carts', [\App\Http\Controllers\Api\CartController::class, 'index']);
    Route::post('/carts', [\App\Http\Controllers\Api\CartController::class, 'store']);
    Route::put('/carts/{id}', [\App\Http\Controllers\Api\CartController::class, 'update']);
    Route::delete('/carts/{id}', [\App\Http\Controllers\Api\CartController::class, 'destroy']);
    Route::delete('/carts/clear', [\App\Http\Controllers\Api\CartController::class, 'clear']);
});
