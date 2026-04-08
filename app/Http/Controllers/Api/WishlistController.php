<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * API lấy danh sách sản phẩm yêu thích của User hiện tại
     */
    public function getWishlists(Request $request)
    {
        $wishlists = Wishlist::with(['product.images'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $wishlists,
        ], 200);
    }

    /**
     * API toggle yêu thích sản phẩm (thêm/xóa)
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = $request->user()->id;
        $productId = $request->product_id;

        // Kiểm tra sản phẩm đã có trong danh sách yêu thích chưa
        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            // Đã có → Xóa khỏi danh sách yêu thích
            $existing->delete();

            return response()->json([
                'success'     => true,
                'message'     => 'Đã xóa khỏi danh sách yêu thích',
                'is_favorite' => false,
            ], 200);
        }

        // Chưa có → Thêm vào danh sách yêu thích
        Wishlist::create([
            'user_id'    => $userId,
            'product_id' => $productId,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Đã thêm vào danh sách yêu thích',
            'is_favorite' => true,
        ], 200);
    }

    /**
     * API xóa toàn bộ danh sách yêu thích của User hiện tại
     */
    public function clearWishlists(Request $request)
    {
        Wishlist::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ danh sách yêu thích',
        ], 200);
    }
}
