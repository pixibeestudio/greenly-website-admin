<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;

class BannerApiController extends Controller
{
    /**
     * Lấy danh sách banner đang active, sắp xếp theo sort_order.
     * Trả về URL đầy đủ cho image_url.
     */
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get()
            ->map(function ($banner) {
                return [
                    'id'        => $banner->id,
                    'title'     => $banner->title,
                    'image_url' => $banner->image_url
                        ? asset('storage/' . $banner->image_url)
                        : null,
                    'sort_order' => $banner->sort_order,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $banners,
        ]);
    }
}
