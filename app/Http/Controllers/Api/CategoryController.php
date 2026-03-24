<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    // Lấy danh sách tất cả danh mục, trả về JSON cho Mobile App
    public function index()
    {
        $categories = Category::all();

        $categories->transform(function ($category) {
            $imagePath = $category->image;

            if ($imagePath) {
                // Nếu trong DB chưa có chữ storage/ thì mới thêm vào
                if (!str_starts_with($imagePath, 'storage/') && !str_starts_with($imagePath, 'http')) {
                    $imagePath = 'storage/' . $imagePath;
                }
                // Xóa bỏ trường hợp lặp storage/storage/ nếu có
                $imagePath = str_replace('storage/storage/', 'storage/', $imagePath);

                $category->image = asset($imagePath);
            }

            return $category;
        });

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ], 200);
    }
}
