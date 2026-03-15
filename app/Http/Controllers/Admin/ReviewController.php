<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        // 1. Truy vấn danh sách đánh giá kèm user + product
        $query = Review::with(['user', 'product'])->latest();

        // 2. Lọc theo trạng thái (status)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. Lọc theo số sao (rating)
        if ($request->filled('rating')) {
            $rating = $request->rating;
            if ($rating === 'positive') {
                $query->where('rating', '>=', 4);
            } elseif ($rating === 'neutral') {
                $query->where('rating', 3);
            } elseif ($rating === 'negative') {
                $query->where('rating', '<=', 2);
            } else {
                $query->where('rating', (int)$rating);
            }
        }

        // 4. Tìm kiếm theo tên sản phẩm hoặc tên khách hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('fullname', 'like', '%' . $search . '%');
                });
            });
        }

        // 5. Phân trang
        $reviews = $query->paginate(10)->appends($request->query());

        // 6. Thống kê cho 3 Card
        $totalReviews = Review::count();
        $positiveCount = Review::where('rating', '>=', 4)->count();
        $neutralCount = Review::where('rating', 3)->count();
        $negativeCount = Review::where('rating', '<=', 2)->count();

        // Tính phần trăm cho thanh progress
        $positivePercent = $totalReviews > 0 ? round(($positiveCount / $totalReviews) * 100) : 0;
        $neutralPercent = $totalReviews > 0 ? round(($neutralCount / $totalReviews) * 100) : 0;
        $negativePercent = $totalReviews > 0 ? round(($negativeCount / $totalReviews) * 100) : 0;

        // Tính điểm trung bình
        $avgRating = Review::count() > 0 ? round(Review::avg('rating'), 1) : 0;

        return view('admin.reviews.index', compact(
            'reviews', 'totalReviews',
            'positiveCount', 'neutralCount', 'negativeCount',
            'positivePercent', 'neutralPercent', 'negativePercent',
            'avgRating'
        ));
    }

    // Duyệt / Ẩn đánh giá
    public function toggleStatus(Review $review, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,hidden',
        ]);

        $review->update(['status' => $request->status]);

        $statusText = $request->status === 'approved' ? 'Đã duyệt hiển thị' : 'Đã ẩn';
        return redirect()->back()->with('success', $statusText . ' đánh giá thành công!');
    }

    // Phản hồi đánh giá
    public function reply(Review $review, Request $request)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ], [
            'admin_reply.required' => 'Vui lòng nhập nội dung phản hồi.',
            'admin_reply.max' => 'Nội dung phản hồi không được quá 1000 ký tự.',
        ]);

        $review->update(['admin_reply' => $request->admin_reply]);

        return redirect()->back()->with('success', 'Đã phản hồi đánh giá thành công!');
    }

    // Xóa đánh giá (spam)
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Đã xóa đánh giá thành công!');
    }
}
