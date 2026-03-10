<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Truy vấn danh sách sản phẩm, sử dụng Eager Loading
        $query = Product::with(['category', 'images']);

        // 2. Tìm kiếm theo tên sản phẩm
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 3. Phân trang với số lượng tùy chỉnh
        $perPage = $request->input('per_page', 10);
        $products = $query->latest()->paginate($perPage)->appends($request->query());

        // 4. Đếm tổng số sản phẩm hiện có
        $totalProducts = Product::count();

        // 5. Lấy danh sách Category
        $categories = Category::all();

        // 6. Trả về view
        return view('admin.products.index', compact('products', 'totalProducts', 'categories'));
    }
}
