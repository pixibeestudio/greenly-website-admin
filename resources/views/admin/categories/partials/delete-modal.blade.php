<!-- Modal Xác nhận Xóa -->
<div id="deleteCategoryModal" class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center transform scale-95 transition-all duration-300 mx-4" id="deleteModalContent">
        
        <!-- Icon Cảnh báo -->
        <i class="fa-solid fa-triangle-exclamation text-5xl text-red-500 mb-4"></i>
        
        <!-- Nội dung -->
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Xác nhận xóa danh mục</h2>
        <p class="text-gray-600 mb-6">
            Bạn có chắc chắn muốn xóa danh mục <span id="delete_category_name" class="font-bold text-gray-800"></span> không? Hành động này không thể hoàn tác.
        </p>

        <!-- Form Xóa -->
        <form id="deleteCategoryForm" method="POST" action="">
            @csrf
            @method('DELETE')
            
            <!-- Nút Hành động -->
            <div class="flex gap-3 justify-center mt-6">
                <button type="button" onclick="closeDeleteModal()" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                    Hủy
                </button>
                <button type="submit" id="confirmDeleteBtn" class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-500/30 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> Xác nhận xóa
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    // Xử lý sự kiện submit form (Loading state)
    document.getElementById('deleteCategoryForm').addEventListener('submit', function (e) {
        const confirmBtn = document.getElementById('confirmDeleteBtn');

        // Bước 1: Thay đổi giao diện ngay lập tức thành vòng quay
        confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        confirmBtn.classList.add('opacity-70', 'cursor-not-allowed');

        // Bước 2: Dùng setTimeout (Nghỉ 10ms) để trình duyệt kịp vẽ (Render) UI mới ra màn hình
        setTimeout(() => {
            confirmBtn.disabled = true;
        }, 10);
    });
</script>
