<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * API tìm kiếm sản phẩm theo từ khóa (keyword)
     */
    public function searchProducts(Request $request)
    {
        $keyword = $request->query('keyword');

        // Nếu keyword rỗng, trả về mảng rỗng
        if (!$keyword || trim($keyword) === '') {
            return response()->json([
                'success' => true,
                'data'   => [],
            ], 200);
        }

        // Tìm sản phẩm có tên chứa từ khóa, chỉ lấy SP đang hoạt động
        $products = Product::where('name', 'like', '%' . $keyword . '%')
            ->where('is_active', 1)
            ->with('images')
            ->withSum('batches', 'current_quantity')
            ->get();

        // Format ảnh + tồn kho cho từng sản phẩm
        $products->transform(function ($product) {
            $product->stock_quantity = (int) ($product->batches_sum_current_quantity ?? 0);

            $imagePath = $product->image;
            if ($imagePath) {
                $imagePath = str_replace('storage/storage/', 'storage/', $imagePath);
                if (!str_starts_with($imagePath, 'storage/') && !str_starts_with($imagePath, 'http')) {
                    $imagePath = 'storage/' . $imagePath;
                }
                $product->image = asset($imagePath);
            }

            return $product;
        });

        return response()->json([
            'success' => true,
            'data'   => $products,
        ], 200);
    }

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

        // Lấy nhà cung cấp từ lô hàng mới nhất của sản phẩm
        $latestBatch = $product->batches()->with('supplier')->latest('id')->first();
        $product->supplier = $latestBatch && $latestBatch->supplier ? [
            'name'        => $latestBatch->supplier->name,
            'address'     => $latestBatch->supplier->address,
            'certificate' => $latestBatch->supplier->certificate,
        ] : null;

        return response()->json([
            'success' => true,
            'data'    => $product,
        ], 200);
    }
}
