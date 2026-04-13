<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Danh sách banner, sắp xếp theo thứ tự hiển thị
     */
    public function index()
    {
        $banners = Banner::orderBy('sort_order', 'asc')->get();

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Lưu banner mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'image.mimes' => 'Chỉ chấp nhận định dạng: jpeg, png, jpg, webp.',
            'image.required' => 'Vui lòng chọn ảnh banner.',
        ]);

        // Upload ảnh vào folder banners
        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'image_url'  => $imagePath,
            'title'      => $request->title,
            'is_active'  => true,
            'sort_order'=> $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Thêm banner thành công!');
    }

    /**
     * Cập nhật banner
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            'image.mimes' => 'Chỉ chấp nhận định dạng: jpeg, png, jpg, webp.',
        ]);

        // Nếu có upload ảnh mới → xóa ảnh cũ → lưu ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ trong Storage
            if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
                Storage::disk('public')->delete($banner->image_url);
            }

            $banner->image_url = $request->file('image')->store('banners', 'public');
        }

        $banner->title = $request->title;
        $banner->sort_order = $request->sort_order ?? $banner->sort_order;
        $banner->save();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Cập nhật banner thành công!');
    }

    /**
     * Xóa banner
     */
    public function destroy(Banner $banner)
    {
        // Xóa ảnh trong Storage
        if ($banner->image_url && Storage::disk('public')->exists($banner->image_url)) {
            Storage::disk('public')->delete($banner->image_url);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Xóa banner thành công!');
    }

    /**
     * Toggle trạng thái hiển thị banner (cho nút Switch)
     */
    public function toggleActive(Banner $banner)
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'success'   => true,
            'message'   => $banner->is_active ? 'Đã bật hiển thị banner' : 'Đã tắt hiển thị banner',
            'is_active' => $banner->is_active,
        ]);
    }
}
