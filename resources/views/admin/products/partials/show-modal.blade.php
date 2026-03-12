<!-- Modal Xem Chi Tiết Sản Phẩm -->
<div id="showProductModal"
    class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <!-- Modal Content -->
    <div id="showProductContent"
        class="bg-gray-50 w-full max-w-5xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div
            class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-eye text-green-600"></i> Chi Tiết Sản Phẩm
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">Xem toàn bộ thông tin chi tiết của sản phẩm.</p>
            </div>
            <button type="button" onclick="closeShowProductModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-8 md:p-10">
            <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                <div class="flex flex-col lg:flex-row gap-10">

                    <!-- ================= CỘT TRÁI: HÌNH ẢNH ================= -->
                    <div class="w-full lg:w-1/3 flex flex-col">

                        <!-- 1. Ảnh đại diện chính -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh đại diện</label>
                            <div class="aspect-[4/3] bg-gray-100 rounded-2xl overflow-hidden border border-gray-200">
                                <img id="show_product_image" src="" alt="Ảnh sản phẩm"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>

                        <!-- 2. Gallery ảnh chi tiết -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <label class="block text-sm font-bold text-gray-700">Hình ảnh chi tiết</label>
                                <span id="show_product_gallery_count" class="text-xs text-gray-400 font-medium">0 ảnh</span>
                            </div>
                            <div id="show_product_gallery_grid" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                                <!-- JS sẽ render ảnh vào đây -->
                            </div>
                            <p id="show_product_no_gallery" class="text-sm text-gray-400 italic mt-2 hidden">Không có hình ảnh chi tiết.</p>
                        </div>

                    </div>

                    <!-- ================= CỘT PHẢI: THÔNG TIN ================= -->
                    <div class="w-full lg:w-2/3 flex flex-col gap-6">

                        <!-- Nhóm 1: Thông tin cơ bản -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-forest-500"></i> Thông tin cơ bản
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- ID -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Mã sản phẩm</p>
                                    <p id="show_product_id" class="text-sm font-bold text-gray-800 font-mono"></p>
                                </div>
                                <!-- Tên -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tên sản phẩm</p>
                                    <p id="show_product_name" class="text-sm font-bold text-gray-800"></p>
                                </div>
                                <!-- Slug -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Slug</p>
                                    <p id="show_product_slug" class="text-sm text-gray-600 font-mono break-all"></p>
                                </div>
                                <!-- Danh mục -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Danh mục</p>
                                    <p id="show_product_category" class="text-sm font-bold text-gray-800"></p>
                                </div>
                                <!-- Xuất xứ -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 sm:col-span-2">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Xuất xứ</p>
                                    <p id="show_product_origin" class="text-sm text-gray-700 flex items-center gap-1.5">
                                        <i class="fa-solid fa-location-dot text-forest-500"></i>
                                        <span></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Nhóm 2: Giá cả & Tồn kho -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-coins text-amber-500"></i> Giá cả & Tồn kho
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- Giá gốc -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Giá gốc</p>
                                    <p id="show_product_price" class="text-sm font-bold text-gray-800"></p>
                                </div>
                                <!-- Giá khuyến mãi -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Giá khuyến mãi</p>
                                    <p id="show_product_discount_price" class="text-sm font-bold text-gray-800"></p>
                                </div>
                                <!-- Đơn vị tính -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Đơn vị tính</p>
                                    <p id="show_product_unit" class="text-sm font-bold text-gray-800"></p>
                                </div>
                                <!-- Tồn kho -->
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 sm:col-span-3">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Tồn kho</p>
                                    <p id="show_product_stock" class="text-sm font-bold"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Nhóm 3: Trạng thái -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-toggle-on text-blue-500"></i> Trạng thái
                            </h3>
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                <span id="show_product_status_badge"></span>
                            </div>
                        </div>

                        <!-- Nhóm 4: Thời gian -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-regular fa-clock text-purple-500"></i> Thời gian
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Ngày tạo</p>
                                    <p id="show_product_created_at" class="text-sm text-gray-700"></p>
                                </div>
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Cập nhật lần cuối</p>
                                    <p id="show_product_updated_at" class="text-sm text-gray-700"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Nhóm 5: Mô tả -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-align-left text-gray-500"></i> Mô tả sản phẩm
                            </h3>
                            <div class="bg-gray-50 rounded-xl px-4 py-4 border border-gray-100 max-h-48 overflow-y-auto">
                                <p id="show_product_description" class="text-sm text-gray-700 leading-relaxed whitespace-pre-line"></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-white border-t border-gray-100 px-8 py-4 rounded-b-2xl flex justify-end">
            <button type="button" onclick="closeShowProductModal()"
                class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                Đóng
            </button>
        </div>

    </div>
</div>
