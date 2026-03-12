<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

        // 5. Thống kê cho các Card
        $outOfStockProducts = 0; // Tạm thời để 0, chờ module Batches
        $discountedProducts = Product::where('discount_price', '>', 0)->count();
        $inactiveProducts = Product::where('is_active', 0)->count();

        // 6. Lấy danh sách Category
        $categories = Category::all();

        // 7. Trả về view
        return view('admin.products.index', compact(
            'products', 'totalProducts', 'categories',
            'outOfStockProducts', 'discountedProducts', 'inactiveProducts'
        ));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'unit_select' => 'required|string',
            'unit_custom' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
            'origin' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'name.max' => 'Tên sản phẩm không được quá 255 ký tự.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được âm.',
            'discount_price.numeric' => 'Giá khuyến mãi phải là số.',
            'discount_price.min' => 'Giá khuyến mãi không được âm.',
            'discount_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'image.image' => 'File ảnh đại diện không hợp lệ.',
            'image.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, webp.',
            'image.max' => 'Ảnh đại diện không được quá 2MB.',
            'gallery.*.image' => 'File ảnh chi tiết không hợp lệ.',
            'gallery.*.mimes' => 'Ảnh chi tiết phải có định dạng: jpeg, png, jpg, webp.',
            'gallery.*.max' => 'Mỗi ảnh chi tiết không được quá 2MB.',
            'unit_select.required' => 'Vui lòng chọn đơn vị tính.',
            'description.max' => 'Mô tả không được quá 2000 ký tự.',
        ]);

        // 2. Xử lý logic Đơn vị tính (Unit)
        $unit = $request->unit_select === 'custom' ? $request->unit_custom : $request->unit_select;

        // 3. Xử lý Upload Ảnh đại diện (Main Image)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 4. Lưu vào bảng Products
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
            'price' => $request->price,
            'discount_price' => $request->discount_price ?? 0,
            'unit' => $unit,
            'description' => $request->description,
            'origin' => $request->origin,
            'is_active' => $request->is_active,
            'image' => $imagePath,
        ]);

        // 5. Xử lý Upload Ảnh phụ (Gallery) vào bảng ProductImages
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        // 6. Redirect về trang danh sách với thông báo thành công
        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function update(Request $request, string $id)
    {
        // 1. Tìm sản phẩm theo ID
        $product = Product::findOrFail($id);

        // 2. Validate dữ liệu đầu vào
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'unit_select' => 'required|string',
            'unit_custom' => 'nullable|string|max:50',
        ], [
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'discount_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
        ]);

        // 3. Xử lý logic Đơn vị tính (Unit)
        $unit = $request->unit_select === 'custom' ? $request->unit_custom : $request->unit_select;

        // 4. Chuẩn bị dữ liệu cập nhật
        $data = $request->only(['name', 'category_id', 'price', 'discount_price', 'description', 'origin', 'is_active']);
        $data['slug'] = Str::slug($request->name);
        $data['unit'] = $unit;
        $data['discount_price'] = $request->discount_price ?? 0;

        // 5. Xử lý ảnh chính (Xóa ảnh cũ nếu upload ảnh mới)
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // 6. Cập nhật sản phẩm
        $product->update($data);

        // 7. Xử lý xóa ảnh gallery cũ (nếu user đã xóa trên form)
        if ($request->has('removed_gallery_ids')) {
            $removedImages = ProductImage::whereIn('id', $request->removed_gallery_ids)
                ->where('product_id', $product->id)
                ->get();
            foreach ($removedImages as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
        }

        // 8. Xử lý thêm ảnh phụ (Nối thêm vào gallery cũ)
        if ($request->hasFile('gallery')) {
            $maxOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $index => $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => $maxOrder + $index + 1,
                ]);
            }
        }

        // 8. Redirect về trang danh sách với thông báo thành công
        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }
}
