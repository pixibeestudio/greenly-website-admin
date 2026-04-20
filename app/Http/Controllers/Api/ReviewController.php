<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * API quản lý đánh giá sản phẩm từ phía khách hàng (mobile app)
 */
class ReviewController extends Controller
{
    /**
     * GET /api/reviews/stats
     * Thống kê: tổng đánh giá + số chưa đánh giá của user hiện tại
     */
    public function stats()
    {
        $userId = auth()->id();

        $totalReviews = Review::where('user_id', $userId)->count();

        $pendingCount = OrderDetail::whereHas('order', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('order_status', 'delivered');
        })->whereDoesntHave('review')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_reviews' => $totalReviews,
                'pending_count' => $pendingCount,
            ],
        ]);
    }

    /**
     * GET /api/reviews/pending-count
     * Chỉ đếm số sản phẩm chưa đánh giá (dùng cho badge trong ProfileFragment)
     */
    public function pendingCount()
    {
        $userId = auth()->id();

        $count = OrderDetail::whereHas('order', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('order_status', 'delivered');
        })->whereDoesntHave('review')->count();

        return response()->json([
            'success' => true,
            'data' => ['pending_count' => $count],
        ]);
    }

    /**
     * GET /api/reviews/pending
     * Danh sách các sản phẩm chưa đánh giá (từ đơn đã giao)
     * Mỗi order_detail = 1 item cần đánh giá
     */
    public function pending()
    {
        $userId = auth()->id();

        $items = OrderDetail::with(['product', 'order'])
            ->whereHas('order', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('order_status', 'delivered');
            })
            ->whereDoesntHave('review')
            ->orderByDesc('id')
            ->get()
            ->map(function ($item) {
                return [
                    'order_detail_id' => $item->id,
                    'order_id' => $item->order_id,
                    'order_code' => $item->order->order_code ?? ('ORD-' . str_pad($item->order_id, 6, '0', STR_PAD_LEFT)),
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'Sản phẩm',
                    'product_image' => $item->product && $item->product->image
                        ? '/storage/' . $item->product->image
                        : null,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'delivered_at' => $item->order->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    /**
     * GET /api/reviews/my
     * Danh sách các review đã đánh giá của user hiện tại
     */
    public function myReviews()
    {
        $userId = auth()->id();

        $reviews = Review::with(['product', 'orderDetail'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'order_id' => $r->order_id,
                    'order_detail_id' => $r->order_detail_id,
                    'product_id' => $r->product_id,
                    'product_name' => $r->product->name ?? 'Sản phẩm',
                    'product_image' => $r->product && $r->product->image
                        ? '/storage/' . $r->product->image
                        : null,
                    'quantity' => $r->orderDetail->quantity ?? 1,
                    'price' => $r->orderDetail->price ?? 0,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'images' => $r->images ?? [],
                    'status' => $r->status,
                    'admin_reply' => $r->admin_reply,
                    'created_at' => $r->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /**
     * POST /api/reviews
     * Tạo review mới (multipart/form-data để upload ảnh)
     * Fields: order_detail_id, rating (1-5), comment (max 500), images[] (max 5 files)
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        $validator = Validator::make($request->all(), [
            'order_detail_id' => 'required|integer|exists:order_details,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB/ảnh
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá',
            'rating.min' => 'Số sao phải từ 1 đến 5',
            'rating.max' => 'Số sao phải từ 1 đến 5',
            'comment.max' => 'Nội dung đánh giá tối đa 500 ký tự',
            'images.max' => 'Chỉ được tải lên tối đa 5 ảnh',
            'images.*.image' => 'File tải lên phải là ảnh',
            'images.*.max' => 'Mỗi ảnh tối đa 5MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Kiểm tra order_detail có thuộc về user + đơn đã giao chưa
        $orderDetail = OrderDetail::with('order')->find($request->order_detail_id);
        if (!$orderDetail || $orderDetail->order->user_id !== $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong đơn hàng của bạn',
            ], 404);
        }
        if ($orderDetail->order->order_status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể đánh giá sản phẩm đã giao thành công',
            ], 422);
        }

        // Kiểm tra đã đánh giá chưa (UNIQUE order_detail_id)
        if (Review::where('order_detail_id', $orderDetail->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đánh giá sản phẩm này rồi',
            ], 422);
        }

        // Upload ảnh nếu có
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('reviews', 'public');
                $imagePaths[] = '/storage/' . $path;
            }
        }

        $review = Review::create([
            'user_id' => $userId,
            'order_id' => $orderDetail->order_id,
            'order_detail_id' => $orderDetail->id,
            'product_id' => $orderDetail->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => $imagePaths,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã đánh giá sản phẩm',
            'data' => $review,
        ], 201);
    }

    /**
     * GET /api/products/{id}/reviews
     * Lấy danh sách review của 1 sản phẩm (public, cho màn Product Detail)
     */
    public function productReviews($productId)
    {
        $reviews = Review::with('user:id,fullname,avatar')
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'user_name' => $r->user->fullname ?? 'Ẩn danh',
                    'user_avatar' => $r->user && $r->user->avatar
                        ? '/storage/' . $r->user->avatar
                        : null,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'images' => $r->images ?? [],
                    'admin_reply' => $r->admin_reply,
                    'created_at' => $r->created_at,
                ];
            });

        $stats = [
            'average' => Review::where('product_id', $productId)->where('status', 'approved')->avg('rating'),
            'total' => $reviews->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'stats' => $stats,
        ]);
    }
}
