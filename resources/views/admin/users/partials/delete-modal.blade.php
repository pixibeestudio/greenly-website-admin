<!-- Modal Xác nhận Xóa Tài Khoản -->
<div id="deleteUserModal" class="fixed inset-0 bg-black/50 z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center transform scale-95 transition-all duration-300 mx-4" id="deleteUserContent">

        <!-- Icon Cảnh báo -->
        <i class="fa-solid fa-triangle-exclamation text-5xl text-red-500 mb-4"></i>

        <!-- Nội dung -->
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Xác nhận xóa tài khoản</h2>
        <p class="text-gray-600 mb-6">
            Bạn có chắc chắn muốn xóa tài khoản <span id="delete_user_name" class="font-bold text-gray-800"></span> không? Hành động này không thể hoàn tác.
        </p>

        <!-- Form Xóa -->
        <form id="deleteUserForm" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="flex gap-3 justify-center mt-6">
                <button type="button" onclick="closeDeleteUserModal()" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                    Hủy
                </button>
                <button type="submit" class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl shadow-lg shadow-red-500/30 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> Xác nhận xóa
                </button>
            </div>
        </form>

    </div>
</div>
