<!-- Modal Xem Chi Tiết -->
<div id="showCategoryModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <!-- Modal Content -->
    <div class="bg-white w-full max-w-2xl mx-4 md:mx-auto rounded-3xl shadow-2xl border border-gray-100 p-8 transform scale-95 transition-transform duration-300 relative max-h-[90vh] overflow-y-auto" id="showModalContent">
        
        <!-- Header -->
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-4">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-forest-600"></i> Chi tiết danh mục
                </h2>
            </div>
            <!-- Nút đóng (X) -->
            <button type="button" onclick="closeShowModal()" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex flex-col md:flex-row gap-8">
            <!-- Cột trái: Hình ảnh -->
            <div class="w-full md:w-1/2 flex flex-col items-center justify-start">
                <div class="w-full aspect-square rounded-2xl border border-gray-200 overflow-hidden bg-gray-50 shadow-sm p-1">
                    <img id="show_image" src="" alt="Category Image" class="w-full h-full object-cover rounded-xl" onerror="this.src='https://ui-avatars.com/api/?name=Error&background=c8e6c9&color=2e7d32'">
                </div>
            </div>

            <!-- Cột phải: Thông tin -->
            <div class="w-full md:w-1/2 flex flex-col space-y-4">
                
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">ID Danh Mục</p>
                    <p id="show_id" class="text-sm font-mono text-gray-700 bg-gray-50 px-2 py-1 rounded inline-block">#</p>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tên Danh Mục</p>
                    <p id="show_name" class="text-lg font-bold text-forest-700"></p>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Mô tả</p>
                    <p id="show_description" class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-100 min-h-[80px]"></p>
                </div>

                <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Ngày tạo</p>
                        <p id="show_created_at" class="text-sm font-medium text-gray-700"></p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Cập nhật lần cuối</p>
                        <p id="show_updated_at" class="text-sm font-medium text-organic-600"></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Button Đóng (Dưới cùng) -->
        <div class="mt-8 pt-4 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeShowModal()" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                Đóng
            </button>
        </div>

    </div>
</div>