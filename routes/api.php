<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Danh mục sản phẩm (public, không cần auth)
Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
