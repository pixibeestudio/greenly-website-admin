<!-- Modal Sửa Tài Khoản -->
<div id="editUserModal"
    class="fixed inset-0 bg-black/50 z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <div id="editUserContent"
        class="bg-gray-50 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-blue-600"></i> Sửa Tài Khoản
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Cập nhật thông tin người dùng trong hệ thống.</p>
            </div>
            <button type="button" onclick="closeEditUserModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editUserForm" action="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_type" value="edit_user">

            <div class="p-8 md:p-10">
                <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                    <div class="space-y-6">

                        <!-- Họ và Tên -->
                        <div>
                            <label for="edit_user_fullname" class="block text-sm font-bold text-gray-700 mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                            <input type="text" id="edit_user_fullname" name="fullname" required
                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm"
                                placeholder="VD: Nguyễn Văn A...">
                            @if(old('form_type') == 'edit_user' && $errors->has('fullname'))
                                <p class="text-red-500 text-xs mt-1.5">{{ $errors->first('fullname') }}</p>
                            @endif
                        </div>

                        <!-- Email + SĐT -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_user_email" class="block text-sm font-bold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="email" id="edit_user_email" name="email" required
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="email@greenly.com">
                                </div>
                                @if(old('form_type') == 'edit_user' && $errors->has('email'))
                                    <p class="text-red-500 text-xs mt-1.5">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                            <div>
                                <label for="edit_user_phone" class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                                <div class="relative">
                                    <i class="fa-solid fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" id="edit_user_phone" name="phone"
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="0912.345.678">
                                </div>
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div>
                            <label for="edit_user_address" class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ</label>
                            <div class="relative">
                                <i class="fa-solid fa-location-dot absolute left-4 top-3.5 text-gray-400 text-sm"></i>
                                <input type="text" id="edit_user_address" name="address"
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                    placeholder="Số nhà, Đường, Quận/Huyện, Tỉnh/TP...">
                            </div>
                        </div>

                        <!-- Vai trò + Mật khẩu mới -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_user_role" class="block text-sm font-bold text-gray-700 mb-2">Vai trò <span class="text-red-500">*</span></label>
                                <select id="edit_user_role" name="role" required
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm">
                                    <option value="customer">Khách hàng (Customer)</option>
                                    <option value="shipper">Shipper</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_user_password" class="block text-sm font-bold text-gray-700 mb-2">Mật khẩu mới</label>
                                <div class="relative">
                                    <i class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="password" id="edit_user_password" name="password"
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="Để trống nếu giữ nguyên">
                                </div>
                                @if(old('form_type') == 'edit_user' && $errors->has('password'))
                                    <p class="text-red-500 text-xs mt-1.5">{{ $errors->first('password') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label for="edit_user_avatar" class="block text-sm font-bold text-gray-700 mb-2">Ảnh đại diện mới</label>
                            <input type="file" id="edit_user_avatar" name="avatar" accept="image/*"
                                class="w-full bg-white border border-gray-300 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-2.5 outline-none transition-all shadow-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100">
                            <p class="text-xs text-gray-400 mt-1">Để trống nếu không muốn thay đổi avatar.</p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-8 py-4 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeEditUserModal()"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                    Hủy
                </button>
                <button type="submit" id="editUserSubmitBtn"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
