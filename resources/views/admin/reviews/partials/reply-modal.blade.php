<!-- Modal Phản hồi Đánh giá -->
<div id="replyReviewModal"
    class="fixed inset-0 bg-black/50 z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <div id="replyReviewContent"
        class="bg-gray-50 w-full max-w-xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-reply text-forest-600"></i> Phản hồi Đánh giá
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Nhập nội dung phản hồi cho khách hàng.</p>
            </div>
            <button type="button" onclick="closeReplyModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="replyReviewForm" action="" method="POST">
            @csrf

            <div class="p-8">
                <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8">
                    <div class="space-y-4">
                        <div>
                            <label for="admin_reply_text" class="block text-sm font-bold text-gray-700 mb-2">
                                Nội dung phản hồi <span class="text-red-500">*</span>
                            </label>
                            <textarea id="admin_reply_text" name="admin_reply" rows="5" required
                                class="w-full bg-white border border-gray-300 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm resize-none"
                                placeholder="Nhập nội dung phản hồi cho khách hàng..."></textarea>
                            <p class="text-xs text-gray-400 mt-1">Tối đa 1000 ký tự. Nội dung sẽ hiển thị dưới đánh giá của khách.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-8 py-4 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeReplyModal()"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                    Hủy
                </button>
                <button type="submit" id="replySubmitBtn"
                    class="px-6 py-2.5 bg-forest-700 hover:bg-forest-800 text-white font-bold rounded-xl shadow-lg shadow-forest-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Gửi phản hồi
                </button>
            </div>
        </form>
    </div>
</div>
