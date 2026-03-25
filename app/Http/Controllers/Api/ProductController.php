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

    // Lấy danh sách sản phẩm đang giảm giá, trả về JSON cho Mobile App
    public function getDiscountedProducts()
    {
        // Chỉ lấy sản phẩm có giá giảm > 0, kèm tổng tồn kho từ bảng batches
        $products = Product::where('discount_price', '>', 0)
            ->withSum('batches', 'current_quantity')
            ->get();

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

    // Lấy chi tiết sản phẩm theo ID, kèm ảnh phụ và tồn kho
    public function show($id)
    {
        // Tìm sản phẩm kèm eager load ảnh phụ + tổng tồn kho
        $product = Product::with(['images' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }])
            ->withSum('batches', 'current_quantity')
            ->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }

        // Gắn tổng tồn kho
        $product->stock_quantity = (int) ($product->batches_sum_current_quantity ?? 0);

        // Hàm helper xử lý đường dẫn ảnh thành URL tuyệt đối
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

        // Tạo mảng all_images: ảnh chính đứng đầu + các ảnh phụ từ product_images
        $allImages = [];

        // Ảnh chính đầu tiên
        if ($product->image) {
            $allImages[] = $product->image;
        }

        // Các ảnh phụ từ bảng product_images
        foreach ($product->images as $img) {
            $url = $resolveImageUrl($img->image_path);
            if ($url) {
                $allImages[] = $url;
            }
        }

        $product->all_images = $allImages;

        return response()->json([
            'success' => true,
            'data'    => $product,
        ], 200);
    }
}
