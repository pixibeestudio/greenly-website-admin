<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    // Lấy toàn bộ giỏ hàng của user đang đăng nhập
    public function index()
    {
        // Eager load product với batches (để tính stock) và product_images
        $carts = Cart::where('user_id', auth()->id())
            ->with(['product' => function ($query) {
                $query->withSum('batches', 'current_quantity')
                    ->with('images');
            }])
            ->get();

        // Xử lý dữ liệu trả về: tính stock, xử lý ảnh
        $carts->transform(function ($cart) {
            $product = $cart->product;

            if ($product) {
                // Tính tổng tồn kho
                $product->stock_quantity = (int) ($product->batches_sum_current_quantity ?? 0);

                // Hàm helper xử lý đường dẫn ảnh
                $resolveImageUrl = function ($path) {
                    if (!$path) return null;

                    // Xóa bỏ trường hợp lặp storage/storage/
                    $path = str_replace('storage/storage/', 'storage/', $path);

                    // Nếu chưa có prefix storage/ thì thêm vào
                    if (!str_starts_with($path, 'storage/') && !str_starts_with($path, 'http')) {
                        $path = 'storage/' . $path;
                    }

                    return asset($path);
                };

                // Transform ảnh chính
                $product->image = $resolveImageUrl($product->image);

                // Tạo mảng all_images: ảnh chính đứng đầu + các ảnh phụ
                $allImages = [];
                if ($product->image) {
                    $allImages[] = $product->image;
                }
                foreach ($product->images as $img) {
                    $url = $resolveImageUrl($img->image_path);
                    if ($url) {
                        $allImages[] = $url;
                    }
                }
                $product->all_images = $allImages;

                // Xóa mảng images gốc để gọn JSON
                unset($product->images);
                unset($product->batches_sum_current_quantity);
            }

            return $cart;
        });

        return response()->json([
            'success' => true,
            'data'    => $carts,
        ], 200);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function store(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_id.exists'   => 'Sản phẩm không tồn tại.',
            'quantity.required'   => 'Vui lòng nhập số lượng.',
            'quantity.integer'    => 'Số lượng phải là số nguyên.',
            'quantity.min'        => 'Số lượng tối thiểu là 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Đã có -> Cộng dồn số lượng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Chưa có -> Tạo mới
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
        ], 201);
    }

    // Cập nhật số lượng trong giỏ hàng
    public function update(Request $request, $id)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer'  => 'Số lượng phải là số nguyên.',
            'quantity.min'      => 'Số lượng tối thiểu là 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Tìm cart item theo ID và UserID
        $cartItem = Cart::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
            ], 404);
        }

        // Cập nhật số lượng
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng!',
            'data'    => $cartItem,
        ], 200);
    }

    // Xóa 1 item khỏi giỏ hàng
    public function destroy($id)
    {
        // Tìm cart item theo ID và UserID
        $cartItem = Cart::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
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
