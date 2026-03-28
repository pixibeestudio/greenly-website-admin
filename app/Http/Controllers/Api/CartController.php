<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Lấy toàn bộ giỏ hàng của user đang đăng nhập
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->with(['product' => function ($query) {
                $query->withSum('batches', 'current_quantity');
            }, 'product.images' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->get();

        // Hàm helper xử lý đường dẫn ảnh tuyệt đối
        $resolveImageUrl = function ($path) {
            if (!$path) return null;
            $path = str_replace('storage/storage/', 'storage/', $path);
            if (!str_starts_with($path, 'storage/') && !str_starts_with($path, 'http')) {
                $path = 'storage/' . $path;
            }
            return asset($path);
        };

        $cartItems->transform(function ($item) use ($resolveImageUrl) {
            if ($item->product) {
                // Gắn tổng tồn kho
                $item->product->stock_quantity = (int) ($item->product->batches_sum_current_quantity ?? 0);

                // Xử lý ảnh chính của sản phẩm
                $item->product->image = $resolveImageUrl($item->product->image);

                // Xử lý ảnh phụ từ product_images
                if ($item->product->images) {
                    $item->product->images->transform(function ($img) use ($resolveImageUrl) {
                        $img->image_path = $resolveImageUrl($img->image_path);
                        return $img;
                    });
                }
            }
            return $item;
        });

        return response()->json([
            'success' => true,
            'data'    => $cartItems,
        ], 200);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'    => 'required|integer|min:1',
        ]);

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Đã có -> cộng dồn số lượng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Chưa có -> tạo mới
            $cartItem = Cart::create([
                'user_id'    => auth()->id(),
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
            'data'    => $cartItem,
        ], 200);
    }

    // Cập nhật số lượng sản phẩm trong giỏ
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!',
            ], 404);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng!',
            'data'    => $cartItem,
        ], 200);
    }

    // Xóa 1 sản phẩm khỏi giỏ hàng
    public function destroy($id)
    {
        $cartItem = Cart::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!',
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
        ], 200);
    }

    // Xóa toàn bộ giỏ hàng của user
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ giỏ hàng!',
        ], 200);
    }
}
