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

    /**
     * API lấy danh sách sản phẩm theo danh mục
     */
    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->where('is_active', 1)
            ->with('images')
            ->withSum('batches', 'current_quantity')
            ->get();

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
            'data'    => $products,
        ], 200);
    }

    /**
     * API lấy dữ liệu cho trang Home: SP mới nhất + Top 10 bán chạy
     */
    public function getHomeData()
    {
        // Hàm helper format ảnh thành URL tuyệt đối
        $resolveImage = function ($path) {
            if (!$path) return null;
            $path = str_replace('storage/storage/', 'storage/', $path);
            if (!str_starts_with($path, 'storage/') && !str_starts_with($path, 'http')) {
                $path = 'storage/' . $path;
            }
            return asset($path);
        };

        // Hàm transform chung cho sản phẩm
        $transformProduct = function ($product) use ($resolveImage) {
            $product->stock_quantity = (int) ($product->batches_sum_current_quantity ?? 0);
            $product->image = $resolveImage($product->image);
            return $product;
        };

        // 1. Sản phẩm mới nhất (4 SP mới nhất đang hoạt động)
        $featuredProducts = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->withSum('batches', 'current_quantity')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get()
            ->transform($transformProduct);

        // 2. Top 10 bán chạy (sắp xếp theo sold_count giảm dần)
        $topSellingProducts = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->withSum('batches', 'current_quantity')
            ->orderBy('sold_count', 'desc')
            ->take(10)
            ->get()
            ->transform($transformProduct);

        // 3. Banner đang hoạt động, sắp xếp theo thứ tự hiển thị
        $banners = \App\Models\Banner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get(['id', 'image_url', 'title']);

        $banners->transform(function ($banner) {
            $banner->image_url = asset('storage/' . $banner->image_url);
            return $banner;
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'banners'     => $banners,
                'featured'    => $featuredProducts,
                'top_selling' => $topSellingProducts,
            ],
        ], 200);
    }

    /**
     * API lọc sản phẩm tổng hợp: category_id, is_discount, sort_by
     */
    public function filterProducts(Request $request)
    {
        // Khởi tạo query: chỉ lấy SP đang hoạt động, kèm images + category
        $query = Product::with(['images', 'category'])
            ->where('is_active', true)
            ->withSum('batches', 'current_quantity');

        // Lọc theo danh mục (bỏ qua nếu category_id = 0 hoặc không gửi)
        if ($request->filled('category_id') && $request->category_id != 0) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc sản phẩm đang giảm giá (loại bỏ SP có discount_price = 0 hoặc NULL)
        if ($request->filled('is_discount') && $request->is_discount === 'true') {
            $query->whereNotNull('discount_price')
                  ->where('discount_price', '>', 0)
                  ->whereColumn('discount_price', '<', 'price');
        }

        // Sắp xếp theo tham số sort_by
        switch ($request->query('sort_by')) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'top_sales':
                $query->orderBy('sold_count', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->get();

        // Format ảnh + tồn kho
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
            'data'    => $products,
        ], 200);
    }
}
