<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Banner quảng cáo (public, không cần auth)
Route::get('/banners', [\App\Http\Controllers\Api\BannerApiController::class, 'index']);

// API Danh mục sản phẩm (public, không cần auth)
Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);

// API Sản phẩm (public, không cần auth)
Route::get('/products/discounted', [\App\Http\Controllers\Api\ProductController::class, 'getDiscountedProducts']);
Route::get('/products/search', [\App\Http\Controllers\Api\ProductController::class, 'searchProducts']);
Route::get('/products/home-data', [\App\Http\Controllers\Api\ProductController::class, 'getHomeData']);
Route::get('/products/filter', [\App\Http\Controllers\Api\ProductController::class, 'filterProducts']);
Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/products/category/{categoryId}', [\App\Http\Controllers\Api\ProductController::class, 'getProductsByCategory']);
Route::get('/products/{id}', [\App\Http\Controllers\Api\ProductController::class, 'show']);

// API Xác thực (public, không cần auth)
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// API yêu cầu đăng nhập (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Giỏ hàng
    Route::get('/carts', [\App\Http\Controllers\Api\CartController::class, 'index']);
    Route::post('/carts', [\App\Http\Controllers\Api\CartController::class, 'store']);
    Route::put('/carts/{id}', [\App\Http\Controllers\Api\CartController::class, 'update']);
    Route::delete('/carts/clear', [\App\Http\Controllers\Api\CartController::class, 'clear']);
    Route::delete('/carts/{id}', [\App\Http\Controllers\Api\CartController::class, 'destroy']);

    // Đặt hàng (Checkout)
    Route::post('/checkout', [\App\Http\Controllers\Api\CheckoutController::class, 'checkout']);

    // Đơn hàng của khách hàng
    Route::get('/my-orders', [\App\Http\Controllers\Api\OrderController::class, 'getUserOrders']);
    Route::post('/orders/{id}/confirm-payment', [\App\Http\Controllers\Api\CheckoutController::class, 'confirmPayment']);

    // Kiểm tra trạng thái thanh toán (app polling)
    Route::get('/payment/status/{id}', [\App\Http\Controllers\Api\CheckoutController::class, 'checkPaymentStatus']);

    // Sổ địa chỉ (Addresses)
    Route::get('/addresses', [\App\Http\Controllers\Api\AddressController::class, 'index']);
    Route::post('/addresses', [\App\Http\Controllers\Api\AddressController::class, 'store']);
    Route::put('/addresses/{id}', [\App\Http\Controllers\Api\AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [\App\Http\Controllers\Api\AddressController::class, 'destroy']);
    Route::post('/addresses/{id}/set-default', [\App\Http\Controllers\Api\AddressController::class, 'setDefault']);

    // Yêu thích sản phẩm (Wishlist)
    Route::get('/wishlist', [\App\Http\Controllers\Api\WishlistController::class, 'getWishlists']);
    Route::post('/wishlist/toggle', [\App\Http\Controllers\Api\WishlistController::class, 'toggleFavorite']);
    Route::delete('/wishlist/clear', [\App\Http\Controllers\Api\WishlistController::class, 'clearWishlists']);

    // --- SHIPPER API ---
    Route::get('/shipper/stats', [\App\Http\Controllers\Api\ShipperApiController::class, 'getStats']);
    Route::post('/shipper/work-status', [\App\Http\Controllers\Api\ShipperApiController::class, 'updateWorkStatus']);
    Route::get('/shipper/orders/new', [\App\Http\Controllers\Api\ShipperApiController::class, 'getNewOrders']);
    Route::get('/shipper/orders/pickup', [\App\Http\Controllers\Api\ShipperApiController::class, 'getPickupOrders']);
    Route::get('/shipper/orders/shipping', [\App\Http\Controllers\Api\ShipperApiController::class, 'getShippingOrders']);
    Route::post('/shipper/orders/{id}/accept', [\App\Http\Controllers\Api\ShipperApiController::class, 'acceptOrder']);
    Route::post('/shipper/orders/{id}/reject', [\App\Http\Controllers\Api\ShipperApiController::class, 'rejectOrder']);
    Route::post('/shipper/orders/{id}/pickup', [\App\Http\Controllers\Api\ShipperApiController::class, 'pickupOrder']);
    Route::post('/shipper/orders/{id}/complete', [\App\Http\Controllers\Api\ShipperApiController::class, 'completeOrder']);
    Route::post('/shipper/orders/{id}/fail', [\App\Http\Controllers\Api\ShipperApiController::class, 'failOrder']);
    Route::get('/shipper/wallet-profile', [\App\Http\Controllers\Api\ShipperApiController::class, 'getWalletAndProfile']);
});
