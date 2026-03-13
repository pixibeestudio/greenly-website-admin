<!-- Modal Xem Chi Tiết Lô Hàng -->
<div id="showBatchModal"
    class="fixed inset-0 bg-black/50 z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <!-- Modal Content -->
    <div id="showBatchContent"
        class="bg-gray-50 w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-eye text-green-600"></i> Chi Tiết Lô Hàng
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">Xem toàn bộ thông tin chi tiết của lô hàng nhập.</p>
            </div>
            <button type="button" onclick="closeShowBatchModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-8 md:p-10">
            <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                <div class="flex flex-col gap-6">

                    <!-- Nhóm 1: Thông tin lô hàng -->
                    <div>
                        <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-forest-500"></i> Thông tin lô hàng
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- ID -->
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Mã lô (ID)</p>
                                <p id="show_batch_id" class="text-sm font-bold text-gray-800 font-mono"></p>
                            </div>
                            <!-- Batch Code -->
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Mã lô hàng</p>
                                <p id="show_batch_code" class="text-sm font-bold text-gray-800 font-mono"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Nhóm 2: Sản phẩm & Nhà cung cấp -->
                    <div>
                        <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-link text-blue-500"></i> Liên kết
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Sản phẩm -->
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Sản phẩm</p>
                                <p id="show_batch_product" class="text-sm text-gray-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-box text-forest-500 text-xs"></i>
                                    <span></span>
                                </p>
                            </div>
                            <!-- Nhà cung cấp -->
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Nhà cung cấp</p>
                                <p id="show_batch_supplier" class="text-sm text-gray-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-truck-field text-forest-500 text-xs"></i>
                                    <span></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Nhóm 3: Số liệu kho -->
                    <div>
                        <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-warehouse text-amber-500"></i> Số liệu kho
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Giá nhập -->
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Giá nhập</p>
                                <p id="show_batch_import_price" class="text-sm font-bold text-forest-700"></p>
                            </div>
                            <!-- SL ban đầu -->
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">SL ban đầu</p>
                                <p id="show_batch_quantity" class="text-sm font-bold text-gray-800"></p>
                            </div>
                            <!-- Tồn kho -->
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tồn kho</p>
                                <p id="show_batch_current_quantity" class="text-sm font-bold text-gray-800"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Nhóm 4: Trạng thái -->
                    <div>
                        <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-toggle-on text-blue-500"></i> Trạng thái
                        </h3>
                        <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                            <span id="show_batch_status_badge"></span>
                        </div>
                    </div>

                    <!-- Nhóm 5: Thời gian -->
                    <div>
                        <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-regular fa-clock text-purple-500"></i> Thời gian
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày nhập</p>
                                <p id="show_batch_created_at" class="text-sm text-gray-700"></p>
                            </div>
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Cập nhật lần cuối</p>
                                <p id="show_batch_updated_at" class="text-sm text-gray-700"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-white border-t border-gray-100 px-8 py-4 rounded-b-2xl flex justify-end">
            <button type="button" onclick="closeShowBatchModal()"
                class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                Đóng
            </button>
        </div>

    </div>
</div>
