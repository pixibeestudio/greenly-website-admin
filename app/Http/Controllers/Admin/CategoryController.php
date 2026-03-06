<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $date = $request->input('date');
        
        $totalCategories = Category::count();

        $query = Category::query();
        
        if ($search) { 
            $query->where('name', 'like', "%{$search}%"); 
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $categories = $query->latest()->paginate($perPage)->appends($request->query());

        return view('admin.categories.index', compact('categories', 'totalCategories'));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 500 ký tự.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, hoặc webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.',
        ]);

        // 2. Xử lý upload ảnh
        if ($request->hasFile('image')) {
            // Lưu file vào thư mục public/categories trên disk public
            $imagePath = $request->file('image')->store('categories', 'public');
            // Cập nhật đường dẫn để dùng với hàm asset()
            $validated['image'] = 'storage/' . $imagePath;
        }

        // 3. Lưu vào database
        Category::create($validated);

        // 4. Redirect về danh sách kèm thông báo thành công
        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục mới thành công!');
    }
}
