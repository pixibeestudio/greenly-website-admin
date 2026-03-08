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

        // 2. Phân trang cơ bản
        $products = $query->latest()->paginate(10);

        // 3. Đếm tổng số sản phẩm hiện có
        $totalProducts = Product::count();

        // 4. Lấy danh sách Category
        $categories = Category::all();

        // 5. Trả về view
        return view('admin.products.index', compact('products', 'totalProducts', 'categories'));
    }
}
