<!-- Modal Xem Chi Tiết Tài Khoản -->
<div id="showUserModal"
    class="fixed inset-0 bg-black/50 z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <div id="showUserContent"
        class="bg-gray-50 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-eye text-green-600"></i> Chi Tiết Tài Khoản
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">Xem toàn bộ thông tin người dùng.</p>
            </div>
            <button type="button" onclick="closeShowUserModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-8 md:p-10">
            <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">

                <!-- Avatar + Tên + Badges -->
                <div class="flex flex-col items-center mb-8">
                    <img id="show_user_avatar" src="" alt="Avatar"
                        class="w-24 h-24 rounded-full object-cover border-4 border-forest-100 shadow-lg mb-4">
                    <h3 id="show_user_fullname" class="text-2xl font-bold text-gray-800 mb-2"></h3>
                    <div class="flex items-center gap-3">
                        <div id="show_user_role"></div>
                        <div id="show_user_status"></div>
                    </div>
                </div>

                <!-- Thông tin chi tiết -->
                <div class="space-y-4">
                    <!-- Email -->
                    <div class="bg-gray-50 rounded-xl px-5 py-4 border border-gray-100 flex items-center gap-4">
                        <div class="w-10 h-10 bg-forest-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-envelope text-forest-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Email</p>
                            <p id="show_user_email" class="text-sm font-bold text-gray-700"></p>
                        </div>
                    </div>

                    <!-- Số điện thoại -->
                    <div class="bg-gray-50 rounded-xl px-5 py-4 border border-gray-100 flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-phone text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Số điện thoại</p>
                            <p id="show_user_phone" class="text-sm font-bold text-gray-700"></p>
                        </div>
                    </div>

                    <!-- Địa chỉ -->
                    <div class="bg-gray-50 rounded-xl px-5 py-4 border border-gray-100 flex items-center gap-4">
                        <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-location-dot text-orange-500"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Địa chỉ</p>
                            <p id="show_user_address" class="text-sm text-gray-700 leading-relaxed"></p>
                        </div>
                    </div>

                    <!-- Ngày tạo -->
                    <div class="bg-gray-50 rounded-xl px-5 py-4 border border-gray-100 flex items-center gap-4">
                        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-calendar-plus text-purple-500"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Ngày tạo tài khoản</p>
                            <p id="show_user_created_at" class="text-sm font-bold text-gray-700"></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-white border-t border-gray-100 px-8 py-4 rounded-b-2xl flex justify-end">
            <button type="button" onclick="closeShowUserModal()"
                class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                Đóng
            </button>
        </div>
    </div>
</div>
