<!-- Cụm nút hành động cho mỗi review -->
<div class="flex items-center gap-1">
    {{-- Nút Duyệt hiển thị (nếu đang pending hoặc hidden) --}}
    @if ($review->status !== 'approved')
        <form action="{{ route('admin.reviews.toggleStatus', $review->id) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="approved">
            <button type="submit" class="text-gray-400 hover:text-green-600 p-2 rounded-full hover:bg-green-50 transition-colors" title="Duyệt hiển thị">
                <i class="fa-regular fa-circle-check"></i>
            </button>
        </form>
    @endif

    {{-- Nút Ẩn đánh giá (nếu đang approved hoặc pending) --}}
    @if ($review->status !== 'hidden')
        <form action="{{ route('admin.reviews.toggleStatus', $review->id) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="hidden">
            <button type="submit" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 transition-colors" title="Ẩn đánh giá">
                <i class="fa-regular fa-eye-slash"></i>
            </button>
        </form>
    @endif

    {{-- Nút Xóa --}}
    <button onclick="openDeleteReviewModal({{ $review->id }})" class="text-gray-400 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors" title="Xóa đánh giá">
        <i class="fa-regular fa-trash-can"></i>
    </button>
</div>
