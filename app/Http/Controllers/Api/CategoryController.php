<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    // Lấy danh sách tất cả danh mục, trả về JSON cho Mobile App
    public function index()
    {
        $categories = Category::all()->map(function ($category) {
            return [
                'id'          => $category->id,
                'name'        => $category->name,
                'description' => $category->description,
                'image'       => $category->image ? asset('storage/' . $category->image) : null,
                'created_at'  => $category->created_at,
                'updated_at'  => $category->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ], 200);
    }
}
