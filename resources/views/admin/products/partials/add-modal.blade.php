<!-- Modal Background Overlay -->
<div id="addProductModal"
    class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <!-- Modal Content -->
    <div id="addProductContent"
        class="bg-gray-50 w-full max-w-5xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-forest-600"></i> Thêm Sản Phẩm Mới
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Cập nhật thông tin chi tiết, hình ảnh và phân loại để đăng bán sản phẩm.</p>
            </div>
            <button type="button" onclick="closeAddProductModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="addProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-8 md:p-10">
                <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                    <div class="flex flex-col lg:flex-row gap-10">

                        <!-- ================= CỘT TRÁI: HÌNH ẢNH ================= -->
                        <div class="w-full lg:w-1/3 flex flex-col">

                            <!-- 1. Ảnh đại diện chính (Main Thumbnail) -->
                            <div class="mb-6">
                                <div class="w-full flex justify-between items-end mb-2">
                                    <label class="block text-sm font-bold text-gray-700">Ảnh đại diện <span class="text-red-500">*</span></label>
                                </div>
                                <label for="add_product_image"
                                    class="w-full aspect-square bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center relative overflow-hidden group cursor-pointer hover:border-forest-500 hover:bg-forest-50 transition-colors">

                                    <!-- Icon mặc định khi chưa có ảnh -->
                                    <div id="add_product_image_placeholder"
                                        class="flex flex-col items-center justify-center text-gray-400 group-hover:text-forest-500 transition-colors">
                                        <i class="fa-solid fa-cloud-arrow-up text-4xl mb-3"></i>
                                        <span class="text-sm font-medium">Chọn hình ảnh</span>
                                        <span class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP. Tối đa 2MB</span>
                                    </div>

                                    <!-- Ảnh Preview (Ẩn mặc định) -->
                                    <img id="add_product_image_preview" src="#" alt="Preview"
                                        class="absolute inset-0 w-full h-full object-cover z-0 hidden">

                                    <!-- Lớp phủ khi hover -->
                                    <div id="add_product_image_overlay"
                                        class="absolute inset-0 bg-black/40 flex-col items-center justify-center hidden group-hover:flex opacity-0 group-hover:opacity-100 transition-opacity z-10">
                                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-white mb-2"></i>
                                        <span class="text-white text-sm font-medium">Tải ảnh khác</span>
                                    </div>

                                    <!-- Input File ẩn -->
                                    <input type="file" name="image" id="add_product_image" class="hidden"
                                        accept="image/png, image/jpeg, image/jpg, image/webp"
                                        onchange="previewAddProductImage(this)">
                                </label>
                                <p id="add_product_image_error" class="text-red-500 text-xs mt-1.5 hidden"></p>
                            </div>

                            <hr class="border-gray-100 mb-6">

                            <!-- 2. Thư viện ảnh (Gallery - Max 10) -->
                            <div>
                                <div class="w-full flex justify-between items-end mb-2">
                                    <label class="block text-sm font-bold text-gray-700">Hình ảnh chi tiết</label>
                                    <span id="add_product_gallery_count" class="text-xs text-gray-400 font-medium">0/10</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mb-3">PNG, JPG, WEBP. Tối đa 2MB/ảnh.</p>

                                <!-- Grid Gallery -->
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3" id="add_product_gallery_grid">
                                    <!-- Nút Thêm Ảnh (+) -->
                                    <label for="add_product_gallery"
                                        class="aspect-square bg-gray-50 rounded-xl border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400 cursor-pointer hover:border-forest-500 hover:text-forest-600 hover:bg-forest-50 transition-colors">
                                        <i class="fa-solid fa-plus text-xl"></i>
                                    </label>
                                </div>

                                <!-- Input File Gallery ẩn -->
                                <input type="file" name="gallery[]" id="add_product_gallery" multiple class="hidden"
                                    accept="image/png, image/jpeg, image/jpg, image/webp"
                                    onchange="previewAddProductGallery(this)">
                            </div>

                        </div>

                        <!-- ================= CỘT PHẢI: FORM NHẬP LIỆU ================= -->
                        <div class="w-full lg:w-2/3 flex flex-col justify-between">

                            <div class="space-y-6">

                                <!-- Dòng 1: Tên Sản Phẩm -->
                                <div>
                                    <label for="add_product_name" class="block text-sm font-bold text-gray-700 mb-2">Tên sản phẩm <span class="text-red-500">*</span></label>
                                    <input type="text" id="add_product_name" name="name" required
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="VD: Cà chua Organic Đà Lạt...">
                                    <p id="add_product_name_error" class="text-red-500 text-xs mt-1.5 hidden"></p>
                                </div>

                                <!-- Dòng 2: Danh mục -->
                                <div>
                                    <label for="add_product_category" class="block text-sm font-bold text-gray-700 mb-2">Danh mục <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select id="add_product_category" name="category_id" required
                                            class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm appearance-none cursor-pointer">
                                            <option value="" disabled selected>-- Chọn danh mục --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none text-sm"></i>
                                    </div>
                                </div>

                                <!-- Dòng 3: Giá & Giá Khuyến mãi -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Giá gốc -->
                                    <div>
                                        <label for="add_product_price" class="block text-sm font-bold text-gray-700 mb-2">Giá gốc (Price) <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <input type="number" id="add_product_price" name="price" required min="0" step="1000"
                                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-4 pr-12 py-3 outline-none transition-all shadow-sm font-mono text-right"
                                                placeholder="0">
                                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold text-sm pointer-events-none">₫</span>
                                        </div>
                                    </div>

                                    <!-- Giá Khuyến mãi -->
                                    <div>
                                        <label for="add_product_discount_price" class="block text-sm font-bold text-gray-700 mb-2">Giá Khuyến mãi (Tùy chọn)</label>
                                        <div class="relative">
                                            <input type="number" id="add_product_discount_price" name="discount_price" min="0" step="1000"
                                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-organic-500 focus:border-organic-500 block pl-4 pr-12 py-3 outline-none transition-all shadow-sm font-mono text-right text-organic-600"
                                                placeholder="0">
                                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold text-sm pointer-events-none">₫</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dòng 4: Đơn vị & Nguồn gốc -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Unit -->
                                    <div>
                                        <label for="add_product_unit_select" class="block text-sm font-bold text-gray-700 mb-2">Đơn vị tính (Unit) <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <select id="add_product_unit_select" name="unit_select" required
                                                onchange="toggleAddProductCustomUnit()"
                                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm appearance-none cursor-pointer">
                                                <option value="Kg" selected>Kilogram (Kg)</option>
                                                <option value="Gram">Gram (g)</option>
                                                <option value="Hộp">Hộp / Khay</option>
                                                <option value="Túi">Túi / Bịch</option>
                                                <option value="Bó">Bó / Lốc</option>
                                                <option value="custom">Khác (Nhập tay)...</option>
                                            </select>
                                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none text-sm"></i>
                                        </div>
                                        <!-- Input nhập tay đơn vị khác (ẩn mặc định) -->
                                        <input type="text" name="unit_custom" id="add_product_unit_custom"
                                            class="hidden mt-2 w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-forest-500 focus:ring-1 focus:ring-forest-500 outline-none transition-all text-gray-700 bg-white"
                                            placeholder="Nhập đơn vị tính (VD: Lít, Lốc, Chai...)">
                                    </div>

                                    <!-- Origin -->
                                    <div>
                                        <label for="add_product_origin" class="block text-sm font-bold text-gray-700 mb-2">Xuất xứ (Origin)</label>
                                        <div class="relative">
                                            <i class="fa-solid fa-location-dot absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                            <input type="text" id="add_product_origin" name="origin"
                                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                                placeholder="VD: Đà Lạt, Nhật Bản...">
                                        </div>
                                    </div>
                                </div>

                                <!-- Dòng 5: Mô tả -->
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <label for="add_product_description" class="block text-sm font-bold text-gray-700">Mô tả sản phẩm</label>
                                    </div>
                                    <div class="relative">
                                        <textarea id="add_product_description" name="description" rows="4"
                                            class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 pb-8 outline-none transition-all shadow-sm resize-y"
                                            placeholder="Nhập mô tả chi tiết về sản phẩm, công dụng, cách bảo quản..."
                                            oninput="updateAddProductCharCount(this)"></textarea>
                                        <!-- Character Count -->
                                        <div class="absolute bottom-2 right-4 text-xs font-medium text-gray-400 bg-white px-1">
                                            <span id="add_product_char_count">0</span>/2000
                                        </div>
                                    </div>
                                </div>

                                <!-- Dòng 6: Trạng thái -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-3">Trạng thái sản phẩm</label>
                                    <div class="flex items-center gap-6">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="is_active" value="1" checked
                                                class="w-4 h-4 text-forest-600 focus:ring-forest-500 border-gray-300">
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-forest-700">
                                                <i class="fa-solid fa-circle-check text-green-500 mr-1"></i> Đang bán
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="is_active" value="0"
                                                class="w-4 h-4 text-gray-600 focus:ring-gray-500 border-gray-300">
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800">
                                                <i class="fa-solid fa-ban text-gray-400 mr-1"></i> Ngừng bán
                                            </span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                            <!-- ================= BUTTONS ================= -->
                            <div class="flex justify-end items-center gap-4 mt-8 pt-6 border-t border-gray-100">
                                <button type="button" onclick="closeAddProductModal()"
                                    class="px-6 py-3 bg-white text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200">
                                    Hủy
                                </button>
                                <button type="submit" id="add_product_submit_btn"
                                    class="px-8 py-3 bg-forest-700 text-white font-bold rounded-xl hover:bg-forest-800 shadow-lg shadow-forest-500/30 transition-all flex items-center gap-2">
                                    <i class="fa-solid fa-save"></i> Thêm sản phẩm
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
