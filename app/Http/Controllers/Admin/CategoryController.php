<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')],
            'description' => 'nullable|string|max:500',
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục này đã tồn tại trong bảng.',
            'description.max' => 'Mô tả không được vượt quá 500 ký tự.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Chỉ hỗ trợ ảnh PNG, JPG, WEBP.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
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

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($id)],
            'description' => 'nullable|string|max:500',
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục này đã tồn tại trong bảng.',
            'description.max' => 'Mô tả không được vượt quá 500 ký tự.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Chỉ hỗ trợ ảnh PNG, JPG, WEBP.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có (bỏ đi tiền tố storage/ để xóa trong disk public)
            if ($category->image && str_starts_with($category->image, 'storage/')) {
                $oldImagePath = str_replace('storage/', '', $category->image);
                Storage::disk('public')->delete($oldImagePath);
            }

            // Lưu file mới
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Xóa ảnh cũ nếu có
        if ($category->image && str_starts_with($category->image, 'storage/')) {
            $oldImagePath = str_replace('storage/', '', $category->image);
            Storage::disk('public')->delete($oldImagePath);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Đã xóa danh mục thành công!');
    }
}
