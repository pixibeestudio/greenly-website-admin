<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    // Lấy danh sách tất cả sản phẩm, trả về JSON cho Mobile App
    public function index()
    {
        // Lấy sản phẩm kèm tổng tồn kho từ bảng batches
        $products = Product::withSum('batches', 'current_quantity')->get();

        $products->transform(function ($product) {
            // Gắn tổng tồn kho, mặc định 0 nếu chưa có lô hàng nào
            $product->stock_quantity = (int) ($product->batches_sum_current_quantity ?? 0);

            $imagePath = $product->image;

            if ($imagePath) {
                // Xóa bỏ trường hợp lặp storage/storage/ nếu có
                $imagePath = str_replace('storage/storage/', 'storage/', $imagePath);

                // Nếu trong DB chưa có chữ storage/ thì mới thêm vào
                if (!str_starts_with($imagePath, 'storage/') && !str_starts_with($imagePath, 'http')) {
                    $imagePath = 'storage/' . $imagePath;
                }

                $product->image = asset($imagePath);
            }

            return $product;
        });

        return response()->json([
            'success' => true,
            'data'    => $products,
        ], 200);
    }
}
