<!-- Modal Thêm Tài Khoản -->
<div id="addUserModal"
    class="fixed inset-0 bg-black/50 z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <div id="addUserContent"
        class="bg-gray-50 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-forest-600"></i> Thêm Tài Khoản
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Tạo tài khoản người dùng mới cho hệ thống.</p>
            </div>
            <button type="button" onclick="closeAddUserModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="form_type" value="add_user">

            <div class="p-8 md:p-10">
                <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                    <div class="space-y-6">

                        <!-- Họ và Tên -->
                        <div>
                            <label for="add_user_fullname" class="block text-sm font-bold text-gray-700 mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                            <input type="text" id="add_user_fullname" name="fullname" required
                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm"
                                placeholder="VD: Nguyễn Văn A..." value="{{ old('form_type') == 'add_user' ? old('fullname') : '' }}">
                            @if(old('form_type') == 'add_user' && $errors->has('fullname'))
                                <p class="text-red-500 text-xs mt-1.5">{{ $errors->first('fullname') }}</p>
                            @endif
                        </div>

                        <!-- Email + SĐT -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="add_user_email" class="block text-sm font-bold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="email" id="add_user_email" name="email" required
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="email@greenly.com" value="{{ old('form_type') == 'add_user' ? old('email') : '' }}">
                                </div>
                                @if(old('form_type') == 'add_user' && $errors->has('email'))
                                    <p class="text-red-500 text-xs mt-1.5">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                            <div>
                                <label for="add_user_phone" class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                                <div class="relative">
                                    <i class="fa-solid fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" id="add_user_phone" name="phone" maxlength="10"
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="0912345678" value="{{ old('form_type') == 'add_user' ? old('phone') : '' }}">
                                </div>
                                <p id="add_phone_error" class="text-red-500 text-xs mt-1.5 hidden">Số điện thoại phải gồm đúng 10 chữ số và bắt đầu bằng số 0.</p>
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div>
                            <label for="add_user_address" class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ</label>
                            <div class="relative">
                                <i class="fa-solid fa-location-dot absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" id="add_user_address" name="address"
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                    placeholder="Số nhà, Đường, Quận/Huyện, Tỉnh/TP..." value="{{ old('form_type') == 'add_user' ? old('address') : '' }}">
                            </div>
                        </div>

                        <!-- Vai trò + Mật khẩu -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="add_user_role" class="block text-sm font-bold text-gray-700 mb-2">Vai trò <span class="text-red-500">*</span></label>
                                <select id="add_user_role" name="role" required
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm">
                                    <option value="customer" {{ old('form_type') == 'add_user' && old('role') == 'customer' ? 'selected' : '' }}>Khách hàng (Customer)</option>
                                    <option value="shipper" {{ old('form_type') == 'add_user' && old('role') == 'shipper' ? 'selected' : '' }}>Shipper</option>
                                    <option value="admin" {{ old('form_type') == 'add_user' && old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="relative">
                                <label for="add_user_password" class="block text-sm font-bold text-gray-700 mb-2">Mật khẩu <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="password" id="add_user_password" name="password" required
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="Nhập mật khẩu...">
                                </div>
                                @if(old('form_type') == 'add_user' && $errors->has('password'))
                                    <p class="text-red-500 text-xs mt-1.5">{{ $errors->first('password') }}</p>
                                @endif
                                <!-- Popup kiểm tra điều kiện mật khẩu -->
                                <div id="add_pwd_popup" class="hidden absolute left-0 top-full mt-2 z-50 bg-gray-900 text-white border border-gray-700 rounded-xl shadow-2xl p-4 text-sm w-full">
                                    <p class="font-bold text-gray-100 mb-2">Mật khẩu phải đảm bảo:</p>
                                    <ul class="space-y-1.5">
                                        <li id="add_pwd_length" class="flex items-center gap-2 text-gray-300"><i class="fa-solid fa-circle-xmark text-red-400 text-xs"></i> Từ 8 đến 30 ký tự</li>
                                        <li id="add_pwd_upper_lower" class="flex items-center gap-2 text-gray-300"><i class="fa-solid fa-circle-xmark text-red-400 text-xs"></i> Chứa cả chữ hoa và chữ thường</li>
                                        <li id="add_pwd_number" class="flex items-center gap-2 text-gray-300"><i class="fa-solid fa-circle-xmark text-red-400 text-xs"></i> Chứa ít nhất 1 chữ số</li>
                                        <li id="add_pwd_special" class="flex items-center gap-2 text-gray-300"><i class="fa-solid fa-circle-xmark text-red-400 text-xs"></i> Chứa ít nhất 1 ký tự đặc biệt (!@#$%^&*)</li>
                                        <li id="add_pwd_no_seq" class="flex items-center gap-2 text-gray-300"><i class="fa-solid fa-circle-xmark text-red-400 text-xs"></i> Không chứa chuỗi liên tục (abc, 123, qwerty)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label for="add_user_avatar" class="block text-sm font-bold text-gray-700 mb-2">Ảnh đại diện</label>
                            <!-- Khung preview avatar -->
                            <div id="add_avatar_preview_wrap" class="hidden mb-3">
                                <div class="relative inline-block">
                                    <img id="add_avatar_preview" src="" alt="Preview" class="w-20 h-20 rounded-full object-cover border-4 border-forest-100 shadow-md">
                                    <button type="button" onclick="removeAddAvatar()" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors border-2 border-white shadow-sm">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="add_user_avatar" name="avatar" accept=".png,.jpg,.jpeg,.webp"
                                class="w-full bg-white border border-gray-300 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-2.5 outline-none transition-all shadow-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100">
                            <p class="text-xs text-gray-400 mt-1">Chấp nhận: PNG, JPG, WEBP — Tối đa 2MB.</p>
                            <p id="add_avatar_error" class="text-red-500 text-xs mt-1 hidden"></p>
                            @if(old('form_type') == 'add_user' && $errors->has('avatar'))
                                <p class="text-red-500 text-xs mt-1.5">{{ $errors->first('avatar') }}</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-8 py-4 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeAddUserModal()"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                    Hủy
                </button>
                <button type="submit" id="addUserSubmitBtn"
                    class="px-6 py-2.5 bg-forest-700 hover:bg-forest-800 text-white font-bold rounded-xl shadow-lg shadow-forest-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i> Tạo tài khoản
                </button>
            </div>
        </form>
    </div>
</div>
