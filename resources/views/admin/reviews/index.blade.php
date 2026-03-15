@extends('layouts.admin')

@section('title', 'Quản lý Đánh giá - Greenly Admin')

@section('page-title', 'Quản lý Đánh giá')

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endpush

@section('content')
<div class="fade-in bg-cream-50 rounded-2xl relative min-h-[80vh]">

<!-- 1. STATS OVERVIEW -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8 mt-2">
    <!-- Card 1: Đánh giá trung bình -->
    <div class="bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-forest-800">
        <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
            <i class="fa-solid fa-star text-[100px]"></i>
        </div>
        <div class="relative z-10 flex flex-col justify-between h-full">
            <div class="flex items-center justify-between mb-2">
                <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider">Đánh giá trung bình</p>
                <div class="w-8 h-8 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/20 text-organic-400"><i class="fa-solid fa-chart-simple text-sm"></i></div>
            </div>
            <div class="flex items-end gap-2">
                <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $avgRating }}</h3>
                <div class="flex text-organic-400 text-sm mb-1.5">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($avgRating))
                            <i class="fa-solid fa-star"></i>
                        @elseif ($i - $avgRating < 1 && $i - $avgRating > 0)
                            <i class="fa-solid fa-star-half-stroke"></i>
                        @else
                            <i class="fa-solid fa-star text-white/30"></i>
                        @endif
                    @endfor
                </div>
            </div>
            <div class="text-[10px] text-white/90 bg-black/10 inline-block px-2 py-0.5 rounded backdrop-blur-md mt-1 w-fit">
                Tổng {{ number_format($totalReviews) }} đánh giá
            </div>
        </div>
    </div>

    <!-- Card 2: Đánh giá tích cực (4-5 sao) -->
    <div class="bg-gradient-to-br from-emerald-700 to-emerald-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-emerald-600">
        <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
            <i class="fa-solid fa-face-smile text-[100px]"></i>
        </div>
        <div class="relative z-10 flex flex-col justify-between h-full">
            <div class="flex items-center justify-between mb-2">
                <p class="text-emerald-100 text-[11px] font-bold uppercase tracking-wider">Tích cực (4-5 <i class="fa-solid fa-star text-[9px]"></i>)</p>
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-face-smile text-sm"></i></div>
            </div>
            <div class="flex items-baseline gap-2 mb-2">
                <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ number_format($positiveCount) }}</h3>
                <span class="text-xs text-emerald-100 font-medium">lượt</span>
            </div>
            <div class="w-full bg-white/20 rounded-full h-1.5">
                <div class="bg-white h-1.5 rounded-full" style="width: {{ $positivePercent }}%"></div>
            </div>
        </div>
    </div>

    <!-- Card 3: Đánh giá trung lập (3 sao) -->
    <div class="bg-gradient-to-br from-orange-500 to-yellow-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-orange-500">
        <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
            <i class="fa-solid fa-face-meh text-[100px]"></i>
        </div>
        <div class="relative z-10 flex flex-col justify-between h-full">
            <div class="flex items-center justify-between mb-2">
                <p class="text-orange-50 text-[11px] font-bold uppercase tracking-wider">Trung lập (3 <i class="fa-solid fa-star text-[9px]"></i>)</p>
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-face-meh text-sm"></i></div>
            </div>
            <div class="flex items-baseline gap-2 mb-2">
                <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ number_format($neutralCount) }}</h3>
                <span class="text-xs text-yellow-100 font-medium">lượt</span>
            </div>
            <div class="w-full bg-white/20 rounded-full h-1.5">
                <div class="bg-white h-1.5 rounded-full" style="width: {{ $neutralPercent }}%"></div>
            </div>
        </div>
    </div>

    <!-- Card 4: Đánh giá tiêu cực (1-2 sao) -->
    <div class="bg-gradient-to-br from-red-600 to-rose-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-red-600">
        <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
            <i class="fa-solid fa-face-frown text-[100px]"></i>
        </div>
        <div class="relative z-10 flex flex-col justify-between h-full">
            <div class="flex items-center justify-between mb-2">
                <p class="text-red-100 text-[11px] font-bold uppercase tracking-wider">Tiêu cực (1-2 <i class="fa-solid fa-star text-[9px]"></i>)</p>
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-face-frown text-sm"></i></div>
            </div>
            <div class="flex items-baseline gap-2 mb-2">
                <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ number_format($negativeCount) }}</h3>
                <span class="text-xs text-red-100 font-medium">lượt</span>
            </div>
            <div class="w-full bg-white/20 rounded-full h-1.5">
                <div class="bg-white h-1.5 rounded-full" style="width: {{ $negativePercent }}%"></div>
            </div>
        </div>
    </div>
</div>

<!-- 2. FILTER & ACTION BAR -->
<div class="flex flex-col xl:flex-row justify-between items-center gap-4 mb-6">
    <div class="flex gap-3 flex-wrap">
        <!-- Dropdown lọc theo Trạng thái (click-based) -->
        <div class="relative" id="statusDropdown">
            <button type="button" onclick="toggleDropdown('statusDropdown')" class="bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-2 text-sm font-bold hover:bg-gray-50 transition-all">
                <i class="fa-solid fa-filter text-forest-600"></i> Trạng thái:
                <span class="text-forest-700">
                    @if(request('status') === 'pending') Chờ xử lý
                    @elseif(request('status') === 'approved') Đã hiển thị
                    @elseif(request('status') === 'hidden') Đã ẩn
                    @else Tất cả
                    @endif
                </span>
                <i class="fa-solid fa-chevron-down text-xs text-gray-400 ml-1 transition-transform" id="statusDropdownIcon"></i>
            </button>
            <div class="dropdown-panel hidden absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 z-20 py-1">
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('status', 'page'))) }}"
                   class="block px-4 py-2.5 text-sm {{ !request('status') ? 'text-forest-700 bg-forest-50 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Tất cả
                </a>
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
                   class="flex justify-between items-center px-4 py-2.5 text-sm {{ request('status') === 'pending' ? 'text-forest-700 bg-forest-50 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Chờ xử lý <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                </a>
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}"
                   class="flex justify-between items-center px-4 py-2.5 text-sm {{ request('status') === 'approved' ? 'text-forest-700 bg-forest-50 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Đã hiển thị <span class="w-2 h-2 rounded-full bg-green-500"></span>
                </a>
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('page'), ['status' => 'hidden'])) }}"
                   class="flex justify-between items-center px-4 py-2.5 text-sm {{ request('status') === 'hidden' ? 'text-forest-700 bg-forest-50 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Đã ẩn <span class="w-2 h-2 rounded-full bg-red-500"></span>
                </a>
            </div>
        </div>

        <!-- Dropdown lọc theo Số sao (click-based) -->
        <div class="relative" id="ratingDropdown">
            <button type="button" onclick="toggleDropdown('ratingDropdown')" class="bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-2 text-sm font-bold hover:bg-gray-50 transition-all">
                <i class="fa-solid fa-star text-organic-500"></i> Số sao:
                <span class="text-organic-500">
                    @if(request('rating') === 'positive') 4-5 Sao
                    @elseif(request('rating') === 'neutral') 3 Sao
                    @elseif(request('rating') === 'negative') 1-2 Sao
                    @else Tất cả
                    @endif
                </span>
                <i class="fa-solid fa-chevron-down text-xs text-gray-400 ml-1 transition-transform" id="ratingDropdownIcon"></i>
            </button>
            <div class="dropdown-panel hidden absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 z-20 py-1">
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('rating', 'page'))) }}"
                   class="block px-4 py-2.5 text-sm {{ !request('rating') ? 'text-organic-500 bg-organic-100 font-bold' : 'text-gray-600 hover:bg-gray-50' }}">
                    Tất cả
                </a>
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('page'), ['rating' => 'positive'])) }}"
                   class="flex justify-between items-center px-4 py-2.5 text-sm {{ request('rating') === 'positive' ? 'text-organic-500 bg-organic-100 font-bold' : 'text-gray-600 hover:bg-green-50 hover:text-green-600' }}">
                    Tích cực (4-5 Sao) <span class="w-2 h-2 rounded-full bg-green-500"></span>
                </a>
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('page'), ['rating' => 'neutral'])) }}"
                   class="flex justify-between items-center px-4 py-2.5 text-sm {{ request('rating') === 'neutral' ? 'text-organic-500 bg-organic-100 font-bold' : 'text-gray-600 hover:bg-organic-100 hover:text-organic-500' }}">
                    Trung lập (3 Sao) <span class="w-2 h-2 rounded-full bg-organic-500"></span>
                </a>
                <a href="{{ route('admin.reviews.index', array_merge(request()->except('page'), ['rating' => 'negative'])) }}"
                   class="flex justify-between items-center px-4 py-2.5 text-sm {{ request('rating') === 'negative' ? 'text-organic-500 bg-organic-100 font-bold' : 'text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    Tiêu cực (1-2 Sao) <span class="w-2 h-2 rounded-full bg-red-500"></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tìm kiếm (giống module orders) -->
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="relative group w-full xl:w-72">
        @foreach(request()->except(['search', 'page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500 transition-all">
            <i class="fa-solid fa-search text-gray-400 mr-3 group-focus-within:text-forest-500 transition-colors"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên SP, khách hàng..."
                class="bg-transparent text-sm text-gray-700 outline-none w-full placeholder-gray-400">
        </div>
    </form>
</div>

<!-- 3. REVIEWS LIST -->
<div class="space-y-5">
    @forelse ($reviews as $review)
        @php
            $isNegative = $review->rating <= 2;
            $hasReply = !empty($review->admin_reply);
            $productImage = $review->product && $review->product->image ? asset('storage/' . $review->product->image) : 'https://via.placeholder.com/200x200?text=No+Image';
            $userAvatar = $review->user && $review->user->avatar ? asset('storage/' . $review->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($review->user->fullname ?? 'User') . '&background=random&size=40';
        @endphp

        <div class="bg-white p-6 rounded-xl border {{ $isNegative && !$hasReply ? 'border-red-100' : 'border-gray-100' }} shadow-sm hover:shadow-md transition-shadow relative">
            {{-- Thanh indicator cho review tiêu cực chưa phản hồi --}}
            @if ($isNegative && !$hasReply)
                <div class="absolute -left-[1px] top-6 w-1 h-12 bg-red-500 rounded-r-full"></div>
            @endif

            {{-- Badge trạng thái --}}
            @if ($review->status === 'pending')
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                        <i class="fa-solid fa-clock"></i> Chờ duyệt
                    </span>
                </div>
            @elseif ($review->status === 'hidden')
                <div class="absolute top-3 right-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                        <i class="fa-solid fa-eye-slash"></i> Đã ẩn
                    </span>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Thông tin Sản phẩm -->
                <div class="md:w-56 flex-shrink-0 flex flex-col gap-2 border-b md:border-b-0 md:border-r border-gray-100 pb-4 md:pb-0 md:pr-4">
                    <img src="{{ $productImage }}" class="w-full h-32 rounded-lg object-cover border border-gray-100" alt="{{ $review->product->name ?? 'Sản phẩm' }}">
                    <div>
                        <h4 class="text-sm font-bold text-gray-800 line-clamp-2 hover:text-forest-700 cursor-pointer transition-colors">{{ $review->product->name ?? 'Sản phẩm không tồn tại' }}</h4>
                        <div class="text-xs text-gray-500 mt-1">Mã SP: #{{ $review->product->id ?? '---' }}</div>
                    </div>
                </div>

                <!-- Nội dung Đánh giá -->
                <div class="flex-1">
                    <!-- Header: User + Rating + Time -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $userAvatar }}" class="w-10 h-10 rounded-full border border-white shadow-sm" alt="{{ $review->user->fullname ?? 'User' }}">
                            <div>
                                <div class="text-sm font-bold text-gray-800">{{ $review->user->fullname ?? 'Người dùng' }}</div>
                                <div class="flex items-center gap-2">
                                    <div class="flex text-xs">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star {{ $i <= $review->rating ? 'text-organic-500' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-medium">| {{ $review->created_at->format('d/m/Y - H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        @include('admin.reviews.partials.action-buttons', ['review' => $review])
                    </div>

                    <!-- Nội dung bình luận -->
                    @if ($review->comment)
                        <div class="text-gray-700 text-sm leading-relaxed mb-4">
                            "{{ $review->comment }}"
                        </div>
                    @else
                        <div class="text-gray-400 text-sm italic mb-4">
                            (Không có bình luận — chỉ đánh giá sao)
                        </div>
                    @endif

                    <!-- Phản hồi của Admin -->
                    @if ($hasReply)
                        <div class="bg-forest-50/50 rounded-lg p-3 border border-forest-100 ml-4 relative">
                            <div class="flex justify-between items-start mb-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 bg-forest-700 rounded-full flex items-center justify-center text-white text-[10px]"><i class="fa-solid fa-leaf"></i></div>
                                    <span class="text-xs font-bold text-forest-800">Greenly Support</span>
                                    <span class="text-[10px] text-gray-400">{{ $review->updated_at->format('d/m/Y - H:i') }}</span>
                                </div>
                                <button onclick="openReplyModal({{ $review->id }}, '{{ addslashes($review->admin_reply) }}')" class="text-xs text-blue-500 hover:text-blue-700 font-medium flex items-center gap-1">
                                    <i class="fa-solid fa-pen"></i> Sửa
                                </button>
                            </div>
                            <p class="text-sm text-gray-600">
                                "{{ $review->admin_reply }}"
                            </p>
                        </div>
                    @else
                        <!-- Nút Trả lời (nếu chưa phản hồi) -->
                        <div class="ml-4">
                            <button onclick="openReplyModal({{ $review->id }}, '')" class="bg-forest-600 hover:bg-forest-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md shadow-forest-500/20 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-reply"></i> Trả lời khách hàng
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <!-- Không có đánh giá -->
        <div class="bg-white p-12 rounded-xl border border-gray-100 shadow-sm text-center">
            <i class="fa-regular fa-star text-6xl text-gray-200 mb-4"></i>
            <p class="text-gray-500 text-lg font-bold">Chưa có đánh giá nào</p>
            <p class="text-gray-400 text-sm mt-1">Các đánh giá từ khách hàng sẽ xuất hiện ở đây.</p>
        </div>
    @endforelse
</div>

<!-- Phân trang -->
@if ($reviews->hasPages())
    <div class="mt-6 p-4 border-t border-gray-100 flex justify-between items-center bg-white rounded-b-xl">
        <span class="text-xs text-gray-500">
            Hiển thị {{ $reviews->firstItem() }}-{{ $reviews->lastItem() }} trên {{ number_format($reviews->total()) }} đánh giá
        </span>
        {{ $reviews->links('vendor.pagination.greenly') }}
    </div>
@endif

{{-- Include Modals --}}
@include('admin.reviews.partials.reply-modal')
@include('admin.reviews.partials.delete-modal')

</div>
@endsection

@push('scripts')
<script>
    // ============================================
    // MODAL PHẢN HỒI ĐÁNH GIÁ
    // ============================================
    function openReplyModal(reviewId, existingReply) {
        document.getElementById('replyReviewForm').action = '/admin/reviews/' + reviewId + '/reply';
        document.getElementById('admin_reply_text').value = existingReply || '';

        const modal = document.getElementById('replyReviewModal');
        const content = document.getElementById('replyReviewContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }
    function closeReplyModal() {
        const modal = document.getElementById('replyReviewModal');
        const content = document.getElementById('replyReviewContent');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // ============================================
    // MODAL XÓA ĐÁNH GIÁ
    // ============================================
    function openDeleteReviewModal(reviewId) {
        document.getElementById('deleteReviewForm').action = '/admin/reviews/' + reviewId;

        const modal = document.getElementById('deleteReviewModal');
        const content = document.getElementById('deleteReviewContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }
    function closeDeleteReviewModal() {
        const modal = document.getElementById('deleteReviewModal');
        const content = document.getElementById('deleteReviewContent');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // Submit loading cho Reply form
    document.getElementById('replyReviewForm').addEventListener('submit', function() {
        const btn = document.getElementById('replySubmitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // ============================================
    // DROPDOWN CLICK-BASED (thay thế hover)
    // ============================================
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const panel = dropdown.querySelector('.dropdown-panel');
        const icon = dropdown.querySelector('button i:last-child');
        const isOpen = !panel.classList.contains('hidden');

        // Đóng tất cả dropdown khác trước
        document.querySelectorAll('.dropdown-panel').forEach(p => {
            p.classList.add('hidden');
            const parentIcon = p.closest('[id]').querySelector('button i:last-child');
            if (parentIcon) parentIcon.classList.remove('rotate-180');
        });

        // Toggle dropdown hiện tại
        if (!isOpen) {
            panel.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
        }
    }

    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(e) {
        document.querySelectorAll('[id$="Dropdown"]').forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                const panel = dropdown.querySelector('.dropdown-panel');
                const icon = dropdown.querySelector('button i:last-child');
                if (panel) panel.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            }
        });
    });
</script>
@endpush
