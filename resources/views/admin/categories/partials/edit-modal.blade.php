<!-- Modal Background Overlay -->
<div id="editCategoryModal"
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <!-- Modal Content -->
    <div class="bg-white w-full max-w-4xl mx-4 md:mx-auto rounded-3xl shadow-2xl border border-gray-100 p-8 md:p-10 transform scale-95 transition-transform duration-300 relative max-h-[90vh] overflow-y-auto"
        id="editModalContent">

        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-forest-800 flex items-center gap-2">
                    Cập nhật Danh mục
                </h2>
                <p class="text-gray-500 text-sm mt-1">Nhập thông tin chi tiết để cập nhật danh mục sản phẩm.
                </p>
            </div>

            <!-- Nút đóng (X) -->
            <button type="button" onclick="closeEditModal()"
                class="w-8 h-8 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editCategoryForm" action="" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="flex flex-col md:flex-row gap-10">

                <!-- Cột trái: Khu vực hình ảnh -->
                <div class="w-full md:w-1/3 flex flex-col items-center">
                    <div class="w-full text-left mb-2">
                        <label class="block text-sm font-bold text-gray-700">Hình ảnh danh mục</label>
                    </div>

                    <!-- Khung Upload/Preview Image -->
                    <label for="editImageUpload"
                        class="w-full aspect-square bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center relative overflow-hidden group cursor-pointer hover:border-forest-500 hover:bg-forest-50 transition-colors">

                        <!-- Icon mặc định khi chưa có ảnh -->
                        <div id="editUploadPlaceholder"
                            class="flex flex-col items-center justify-center text-gray-400 group-hover:text-forest-500 transition-colors hidden">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl mb-3"></i>
                            <span class="text-sm font-medium">Chọn hình ảnh</span>
                        </div>

                        <!-- Ảnh Preview -->
                        <img id="editImagePreview" src="#" alt="Preview"
                            class="absolute inset-0 w-full h-full object-cover z-0">

                        <!-- Lớp phủ khi hover (nếu muốn đổi ảnh) -->
                        <div id="editChangeImageOverlay"
                            class="absolute inset-0 bg-black/40 flex-col items-center justify-center hidden group-hover:flex opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-white mb-2"></i>
                            <span class="text-white text-sm font-medium">Tải ảnh khác</span>
                        </div>

                        <!-- Input File ẩn -->
                        <input type="file" id="editImageUpload" name="image" class="hidden"
                            accept="image/png, image/jpeg, image/jpg, image/webp" onchange="previewEditImage(this)">
                    </label>

                    <p id="edit_image_error" class="text-red-500 text-xs mt-2 font-medium hidden"></p>

                    @error('image')
                        <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror

                    <!-- Text hướng dẫn -->
                    <p class="text-xs text-gray-400 mt-4 font-medium">PNG, JPG, WEBP. Tối đa 2MB</p>
                </div>

                <!-- Cột phải: Khung nhập liệu (Inputs) -->
                <div class="w-full md:w-2/3 flex flex-col justify-between">

                    <div class="space-y-6">
                        <!-- Input: ID và Tên danh mục -->
                        <div class="flex gap-4">
                            <!-- Input: ID -->
                            <div class="w-24">
                                <label for="edit_id" class="block text-sm font-bold text-gray-700 mb-2">ID</label>
                                <input type="text" id="edit_id" readonly
                                    class="w-full bg-gray-100 border border-gray-300 text-gray-500 text-base font-mono rounded-xl block px-4 py-3 outline-none cursor-not-allowed">
                            </div>

                            <!-- Input: Tên danh mục -->
                            <div class="flex-1">
                                <label for="edit_name" class="block text-sm font-bold text-gray-700 mb-2">Tên danh mục
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_name" name="name" required
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="Nhập tên danh mục...">
                                
                                <p id="edit_name_error" class="text-red-500 text-xs mt-1.5 font-medium hidden"></p>

                                @error('name')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Textarea: Mô tả -->
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <label for="edit_description" class="block text-sm font-bold text-gray-700">Mô tả</label>
                            </div>
                            <div class="relative">
                                <textarea id="edit_description" name="description" rows="5"
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm resize-none @error('description') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="Nhập mô tả cho danh mục này... (Tùy chọn)"
                                    oninput="updateEditCharCount(this)"></textarea>

                                <!-- Character Count (Nằm góc dưới bên phải) -->
                                <div class="absolute bottom-3 right-4 text-xs font-medium text-gray-400 bg-white px-1">
                                    <span id="editCharCount">0</span>/500
                                </div>
                            </div>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Buttons: Hủy & Thêm -->
                    <div class="flex justify-end items-center gap-4 mt-8 pt-6 border-t border-gray-100">
                        <button type="button" onclick="closeEditModal()"
                            class="px-6 py-3 bg-white text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200">
                            Hủy
                        </button>
                        <button type="submit" id="updateBtn"
                            class="px-8 py-3 bg-forest-700 text-white font-bold rounded-xl hover:bg-forest-800 shadow-lg shadow-forest-500/30 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> Cập nhật danh mục
                        </button>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

<script>
    // Xử lý sự kiện submit form (Loading state & Validate Name)
    document.getElementById('editCategoryForm').addEventListener('submit', function (e) {
        const nameInput = document.getElementById('edit_name');
        const nameValue = nameInput.value;
        const nameError = document.getElementById('edit_name_error');

        // JS Validate: Tránh nhập toàn số
        if (/^\d+$/.test(nameValue.trim())) {
            e.preventDefault(); // Chặn submit
            nameError.innerText = 'Không cho phép đặt tên bằng số.';
            nameError.classList.remove('hidden');
            nameInput.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
            
            // Gọi hàm showErrorNotification nếu có (từ layout)
            if (typeof showErrorNotification === 'function') {
                showErrorNotification('Tên danh mục không hợp lệ.');
            }
            return;
        } else {
            nameError.classList.add('hidden');
            nameInput.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        }

        const updateBtn = document.getElementById('updateBtn');

        // Bước 1: Thay đổi giao diện ngay lập tức thành vòng quay
        updateBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        updateBtn.classList.add('opacity-70', 'cursor-not-allowed');

        // Bước 2: Dùng setTimeout (Nghỉ 10ms) để trình duyệt kịp vẽ (Render) UI mới ra màn hình
        // Rồi mới khóa nút để tránh double-click chặn việc gửi form
        setTimeout(() => {
            updateBtn.disabled = true;
        }, 10);
    });

    // Hàm cập nhật số lượng ký tự cho Edit Modal
    function updateEditCharCount(element) {
        const maxLength = 500;
        const currentLength = element.value ? element.value.length : 0;
        const countElement = document.getElementById('editCharCount');

        if (currentLength > maxLength) {
            element.value = element.value.substring(0, maxLength); // Chặn nhập lố
            countElement.innerText = maxLength;
        } else {
            countElement.innerText = currentLength;
        }

        // Đổi màu đỏ nếu sắp hết
        if (currentLength >= 450) {
            countElement.classList.add('text-organic-500');
        } else {
            countElement.classList.remove('text-organic-500');
        }
    }

    // Hàm Preview ảnh & Validate
    function previewEditImage(input) {
        const preview = document.getElementById('editImagePreview');
        const placeholder = document.getElementById('editUploadPlaceholder');
        const changeOverlay = document.getElementById('editChangeImageOverlay');
        const imageError = document.getElementById('edit_image_error');
        const labelUpload = document.querySelector('label[for="editImageUpload"]');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate loại file
            if (!['image/png', 'image/jpeg', 'image/webp', 'image/jpg'].includes(file.type)) {
                imageError.innerText = 'Chỉ cho phép tải hình ảnh PNG, JPG, WEBP.';
                imageError.classList.remove('hidden');
                labelUpload.classList.add('border-red-500', 'bg-red-50');
                input.value = ''; // Xóa file không hợp lệ
                return;
            }

            // Validate dung lượng (2MB)
            if (file.size > 2 * 1024 * 1024) {
                imageError.innerText = 'Chỉ cho phép hình ảnh có kích thước dưới 2MB.';
                imageError.classList.remove('hidden');
                labelUpload.classList.add('border-red-500', 'bg-red-50');
                input.value = ''; // Xóa file không hợp lệ
                return;
            }

            // Xóa lỗi nếu ảnh hợp lệ
            imageError.classList.add('hidden');
            labelUpload.classList.remove('border-red-500', 'bg-red-50');

            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                changeOverlay.classList.remove('hidden'); // Kích hoạt hover thay ảnh
            }

            reader.readAsDataURL(file);
        }
    }
</script>