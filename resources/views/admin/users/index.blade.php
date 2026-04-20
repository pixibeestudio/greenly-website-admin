@extends('layouts.admin')

@section('title', 'Quản lý Người dùng - Greenly Admin')

@section('page-title', 'Quản lý Người dùng')

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="fade-in bg-cream-50 rounded-2xl relative min-h-[80vh]">

    <!-- 1. 4 CARD THỐNG KÊ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8 mt-2">
        <!-- Card 1: Tổng số người dùng -->
        <div class="bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-forest-800">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
                <i class="fa-solid fa-users text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-forest-100 text-[11px] font-bold uppercase tracking-wider">Tổng người dùng</p>
                    <div class="w-8 h-8 bg-white/10 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/20 text-organic-400"><i class="fa-solid fa-users text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $totalUsers }}</h3>
                    <span class="text-xs text-forest-100 font-medium">Tài khoản</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Khách hàng mới -->
        <div class="bg-gradient-to-br from-teal-600 to-teal-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-teal-600">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
                <i class="fa-solid fa-user-plus text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-teal-100 text-[11px] font-bold uppercase tracking-wider">Khách hàng mới</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-seedling text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $newCustomersThisMonth }}</h3>
                    <span class="text-[10px] text-teal-50 font-medium bg-black/10 px-2 py-0.5 rounded">Trong tháng này</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Tổng số Shipper -->
        <div class="bg-gradient-to-br from-blue-700 to-blue-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-blue-600">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
                <i class="fa-solid fa-motorcycle text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-100 text-[11px] font-bold uppercase tracking-wider">Đội ngũ Shipper</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-truck-fast text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ $totalShippers }}</h3>
                    <span class="text-xs text-blue-100 font-medium">Nhân sự</span>
                </div>
            </div>
        </div>

        <!-- Card 4: Tài khoản bị khóa -->
        <div class="bg-gradient-to-br from-red-600 to-rose-500 p-5 rounded-2xl shadow-lg text-white relative overflow-hidden group hover:shadow-xl transition-all border border-red-600">
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500 -rotate-12">
                <i class="fa-solid fa-user-lock text-[100px]"></i>
            </div>
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-red-100 text-[11px] font-bold uppercase tracking-wider">Khóa / Tạm dừng</p>
                    <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center"><i class="fa-solid fa-ban text-sm"></i></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-4xl font-bold text-white drop-shadow-md">{{ str_pad($lockedAccounts, 2, '0', STR_PAD_LEFT) }}</h3>
                    <span class="text-xs text-red-100 font-medium">Cần kiểm tra</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TOOLBAR: LỌC THEO TABS & SEARCH -->
    <div class="flex flex-col xl:flex-row justify-between items-center gap-4 mb-6">
        <!-- Tabs Navigation (Role Filter) -->
        <div class="w-full xl:w-auto overflow-x-auto pb-2 xl:pb-0 hide-scrollbar">
            <div class="inline-flex bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm items-center min-w-max gap-1">
                @php
                    $currentRole = request('role', '');
                    $roleTabs = [
                        '' => 'Tất cả',
                        'customer' => 'Khách hàng',
                        'shipper' => 'Shipper',
                        'admin' => 'Admin',
                    ];
                @endphp
                @foreach($roleTabs as $roleKey => $roleLabel)
                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), $roleKey ? ['role' => $roleKey] : [])) }}"
                       class="px-5 py-2 rounded-lg text-sm transition-all focus:outline-none
                              {{ $currentRole === $roleKey ? 'font-bold bg-forest-100 text-forest-700' : 'font-medium text-gray-600 hover:bg-gray-50 hover:text-forest-700' }}">
                        {{ $roleLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Nút Thêm mới & Ô Search -->
        <div class="w-full xl:w-auto flex gap-3 shrink-0">
            <!-- Search Box -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="relative group w-full xl:w-72">
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500 transition-all">
                    <i class="fa-solid fa-search text-gray-400 mr-3 group-focus-within:text-forest-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên, email, SĐT..."
                        class="bg-transparent text-sm text-gray-700 outline-none w-full placeholder-gray-400">
                </div>
            </form>

            <!-- Thêm User -->
            <button type="button" onclick="openAddUserModal()"
                class="bg-forest-700 hover:bg-forest-800 text-white px-5 py-2.5 rounded-xl shadow-md shadow-forest-500/30 flex items-center justify-center gap-2 transition-all font-bold text-sm whitespace-nowrap">
                <i class="fa-solid fa-user-plus"></i> <span class="hidden sm:inline">Thêm tài khoản</span>
            </button>
        </div>
    </div>

    <!-- 3. BẢNG DỮ LIỆU NGƯỜI DÙNG -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-[11px] font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-center w-12">ID</th>
                        <th class="px-6 py-4 w-16 text-center">Avatar</th>
                        <th class="px-6 py-4">Họ và Tên</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Số điện thoại</th>
                        <th class="px-6 py-4 text-center">Vai trò (Role)</th>
                        <th class="px-6 py-4">Ngày tạo</th>
                        <th class="px-6 py-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="{{ $user->status === 'locked' ? 'bg-red-50/20 hover:bg-red-50/40' : 'hover:bg-forest-50/30' }} transition-colors group">
                            <!-- ID -->
                            <td class="px-6 py-4 text-center font-mono text-gray-500 text-xs">#U{{ str_pad($user->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <!-- Avatar -->
                            <td class="px-6 py-4">
                                @if($user->status === 'locked')
                                    <div class="relative w-9 h-9 mx-auto">
                                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->fullname) . '&background=e8f5e9&color=2e7d32&rounded=true&bold=true' }}"
                                            alt="Avatar" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm grayscale opacity-60">
                                        <div class="absolute -bottom-1 -right-1 bg-red-500 text-white w-4 h-4 rounded-full flex items-center justify-center text-[8px] border-2 border-white"><i class="fa-solid fa-ban"></i></div>
                                    </div>
                                @else
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->fullname) . '&background=e8f5e9&color=2e7d32&rounded=true&bold=true' }}"
                                        alt="Avatar" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm mx-auto">
                                @endif
                            </td>
                            <!-- Họ và Tên -->
                            <td class="px-6 py-4">
                                <span class="font-bold text-sm {{ $user->status === 'locked' ? 'text-gray-500 line-through' : 'text-gray-800 group-hover:text-forest-700' }} transition-colors">{{ $user->fullname }}</span>
                            </td>
                            <!-- Email (đã mask để bảo mật) -->
                            <td class="px-6 py-4 {{ $user->status === 'locked' ? 'text-gray-500 line-through' : 'text-gray-600' }}" title="Bấm icon mắt để xem đầy đủ">{{ $user->masked_email }}</td>
                            <!-- Số điện thoại (đã mask) -->
                            <td class="px-6 py-4 font-mono {{ $user->status === 'locked' ? 'text-gray-500' : 'text-gray-700' }}" title="Bấm icon mắt để xem đầy đủ">{{ $user->masked_phone ?? '—' }}</td>
                            <!-- Vai trò -->
                            <td class="px-6 py-4 text-center">
                                @switch($user->role)
                                    @case('admin')
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">
                                            <i class="fa-solid fa-shield-halved mr-1.5"></i> Admin
                                        </span>
                                        @break
                                    @case('shipper')
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-200">
                                            <i class="fa-solid fa-motorcycle mr-1.5"></i> Shipper
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md text-[10px] font-bold {{ $user->status === 'locked' ? 'bg-gray-100 text-gray-500 border border-gray-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                            <i class="fa-solid fa-user mr-1.5"></i> Customer
                                        </span>
                                @endswitch
                            </td>
                            <!-- Ngày tạo -->
                            <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('d/m/Y') }}</td>
                            <!-- Hành động -->
                            <td class="px-6 py-4 text-center">
                                @include('admin.users.partials.action-buttons', ['user' => $user])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-16">
                                <div class="flex flex-col items-center gap-3">
                                    <i class="fa-solid fa-users-slash text-4xl text-gray-300"></i>
                                    <p class="text-gray-400 font-medium">Không tìm thấy người dùng nào.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-white rounded-b-2xl">
                {{ $users->links('vendor.pagination.greenly') }}
            </div>
        @endif
    </div>
</div>

<!-- Modals -->
@include('admin.users.partials.add-modal')
@include('admin.users.partials.edit-modal')
@include('admin.users.partials.show-modal')
@include('admin.users.partials.delete-modal')

@endsection

@push('scripts')
<script>
    // ============================================
    // HÀM TIỆN ÍCH CHUNG
    // ============================================
    // Kiểm tra số điện thoại hợp lệ (10 số, bắt đầu bằng 0)
    function isValidPhone(phone) {
        if (!phone || phone.trim() === '') return true; // Cho phép trống
        return /^0\d{9}$/.test(phone.trim());
    }

    // Kiểm tra điều kiện mật khẩu
    function checkPasswordRules(password) {
        const sequences = ['abc','bcd','cde','def','efg','fgh','ghi','hij','ijk','jkl','klm','lmn','mno','nop','opq','pqr','qrs','rst','stu','tuv','uvw','vwx','wxy','xyz','012','123','234','345','456','567','678','789','qwerty','asdf','zxcv','1111','2222','3333','4444','5555','6666','7777','8888','9999','0000'];
        const lower = password.toLowerCase();
        const hasSeq = sequences.some(s => lower.includes(s));
        return {
            length: password.length >= 8 && password.length <= 30,
            upperLower: /[a-z]/.test(password) && /[A-Z]/.test(password),
            number: /\d/.test(password),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
            noSeq: !hasSeq
        };
    }

    // Cập nhật giao diện popup mật khẩu theo kết quả kiểm tra
    function updatePwdUI(prefix, rules) {
        const items = {
            [`${prefix}_pwd_length`]: rules.length,
            [`${prefix}_pwd_upper_lower`]: rules.upperLower,
            [`${prefix}_pwd_number`]: rules.number,
            [`${prefix}_pwd_special`]: rules.special,
            [`${prefix}_pwd_no_seq`]: rules.noSeq,
        };
        Object.entries(items).forEach(([id, passed]) => {
            const li = document.getElementById(id);
            if (!li) return;
            const icon = li.querySelector('i');
            if (passed) {
                icon.className = 'fa-solid fa-circle-check text-green-400 text-xs';
                li.classList.remove('text-gray-300'); li.classList.add('text-green-400');
            } else {
                icon.className = 'fa-solid fa-circle-xmark text-red-400 text-xs';
                li.classList.remove('text-green-400'); li.classList.add('text-gray-300');
            }
        });
    }

    // Kiểm tra mật khẩu có hợp lệ hoàn toàn không
    function isPasswordValid(password) {
        if (!password || password === '') return false;
        const r = checkPasswordRules(password);
        return r.length && r.upperLower && r.number && r.special && r.noSeq;
    }

    // Validate avatar: kiểu file + kích thước
    function validateAvatarFile(file, errorElId) {
        const errorEl = document.getElementById(errorElId);
        const allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];
        const allowedExts = ['.png', '.jpg', '.jpeg', '.webp'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!file) { errorEl.classList.add('hidden'); return true; }

        const ext = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(file.type) && !allowedExts.includes(ext)) {
            errorEl.textContent = 'Chỉ chấp nhận ảnh định dạng PNG, JPG hoặc WEBP.';
            errorEl.classList.remove('hidden');
            return false;
        }
        if (file.size > maxSize) {
            errorEl.textContent = 'Kích thước ảnh không được vượt quá 2MB. (Hiện tại: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB)';
            errorEl.classList.remove('hidden');
            return false;
        }

        errorEl.classList.add('hidden');
        return true;
    }

    // ============================================
    // AVATAR PREVIEW + VALIDATION
    // ============================================
    function setupAvatarPreview(inputId, previewWrapId, previewImgId, errorElId) {
        const input = document.getElementById(inputId);
        input.addEventListener('change', function() {
            const file = this.files[0];
            const wrap = document.getElementById(previewWrapId);
            const img = document.getElementById(previewImgId);

            if (!file) { wrap.classList.add('hidden'); return; }

            if (!validateAvatarFile(file, errorElId)) {
                this.value = '';
                wrap.classList.add('hidden');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                wrap.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
    }

    function removeAddAvatar() {
        document.getElementById('add_user_avatar').value = '';
        document.getElementById('add_avatar_preview_wrap').classList.add('hidden');
        document.getElementById('add_avatar_error').classList.add('hidden');
    }
    function removeEditAvatar() {
        document.getElementById('edit_user_avatar').value = '';
        document.getElementById('edit_avatar_preview_wrap').classList.add('hidden');
        document.getElementById('edit_avatar_error').classList.add('hidden');
    }

    // Khởi tạo preview cho cả 2 form
    setupAvatarPreview('add_user_avatar', 'add_avatar_preview_wrap', 'add_avatar_preview', 'add_avatar_error');
    setupAvatarPreview('edit_user_avatar', 'edit_avatar_preview_wrap', 'edit_avatar_preview', 'edit_avatar_error');

    // ============================================
    // PHONE VALIDATION REAL-TIME
    // ============================================
    function setupPhoneValidation(inputId, errorId) {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        input.addEventListener('input', function() {
            // Chỉ cho phép nhập số
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.trim() === '') { error.classList.add('hidden'); return; }
            if (!isValidPhone(this.value)) {
                error.classList.remove('hidden');
            } else {
                error.classList.add('hidden');
            }
        });
    }
    setupPhoneValidation('add_user_phone', 'add_phone_error');
    setupPhoneValidation('edit_user_phone', 'edit_phone_error');

    // ============================================
    // PASSWORD POPUP REAL-TIME
    // ============================================
    function setupPasswordPopup(inputId, popupId, prefix) {
        const input = document.getElementById(inputId);
        const popup = document.getElementById(popupId);

        input.addEventListener('focus', function() { popup.classList.remove('hidden'); });
        input.addEventListener('blur', function() {
            // Ẩn popup khi blur nếu ô trống
            setTimeout(() => {
                if (!this.value) popup.classList.add('hidden');
            }, 200);
        });
        input.addEventListener('input', function() {
            popup.classList.remove('hidden');
            const rules = checkPasswordRules(this.value);
            updatePwdUI(prefix, rules);
            // Tự động ẩn popup khi tất cả điều kiện đạt
            if (rules.length && rules.upperLower && rules.number && rules.special && rules.noSeq) {
                setTimeout(() => { popup.classList.add('hidden'); }, 600);
            }
        });
    }
    setupPasswordPopup('add_user_password', 'add_pwd_popup', 'add');
    setupPasswordPopup('edit_user_password', 'edit_pwd_popup', 'edit');

    // ============================================
    // CLIENT-SIDE FORM VALIDATION (không cho submit nếu sai)
    // ============================================
    function validateForm(prefix, isEdit) {
        let valid = true;

        // Kiểm tra Họ tên (required)
        const fullname = document.getElementById(prefix + '_user_fullname').value.trim();
        if (!fullname) { valid = false; }

        // Kiểm tra Email (required)
        const email = document.getElementById(prefix + '_user_email').value.trim();
        if (!email) { valid = false; }

        // Kiểm tra SĐT (nếu có nhập)
        const phone = document.getElementById(prefix + '_user_phone').value.trim();
        if (phone && !isValidPhone(phone)) {
            document.getElementById(prefix + '_phone_error').classList.remove('hidden');
            valid = false;
        }

        // Kiểm tra mật khẩu
        const password = document.getElementById(prefix + '_user_password').value;
        if (!isEdit && !password) {
            // Form Thêm: mật khẩu bắt buộc
            valid = false;
        }
        if (password && !isPasswordValid(password)) {
            // Nếu có nhập mật khẩu thì phải đúng điều kiện
            document.getElementById(prefix + '_pwd_popup').classList.remove('hidden');
            valid = false;
        }

        // Kiểm tra Avatar (nếu có chọn file)
        const avatarInput = document.getElementById(prefix + '_user_avatar');
        if (avatarInput.files.length > 0) {
            if (!validateAvatarFile(avatarInput.files[0], prefix + '_avatar_error')) {
                valid = false;
            }
        }

        return valid;
    }

    // ============================================
    // MODAL THÊM TÀI KHOẢN
    // ============================================
    function openAddUserModal() {
        const modal = document.getElementById('addUserModal');
        const content = document.getElementById('addUserContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }
    function closeAddUserModal() {
        const modal = document.getElementById('addUserModal');
        const content = document.getElementById('addUserContent');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('addUserForm').reset();
            document.getElementById('add_avatar_preview_wrap').classList.add('hidden');
            document.getElementById('add_pwd_popup').classList.add('hidden');
            document.getElementById('add_phone_error').classList.add('hidden');
            document.getElementById('add_avatar_error').classList.add('hidden');
            // Reset icon mật khẩu về mặc định
            ['add_pwd_length','add_pwd_upper_lower','add_pwd_number','add_pwd_special','add_pwd_no_seq'].forEach(id => {
                const li = document.getElementById(id);
                if (li) { li.querySelector('i').className = 'fa-solid fa-circle-xmark text-red-400 text-xs'; li.classList.remove('text-green-400'); li.classList.add('text-gray-300'); }
            });
        }, 300);
    }

    // ============================================
    // MODAL SỬA TÀI KHOẢN
    // ============================================
    function openEditUserModal(button) {
        const user = JSON.parse(button.getAttribute('data-user'));
        document.getElementById('editUserForm').action = '/admin/users/' + user.id;
        document.getElementById('edit_user_fullname').value = user.fullname;
        document.getElementById('edit_user_email').value = user.email;
        document.getElementById('edit_user_phone').value = user.phone || '';
        document.getElementById('edit_user_address').value = user.address || '';
        document.getElementById('edit_user_role').value = user.role;
        document.getElementById('edit_user_password').value = '';

        // Reset trạng thái edit form
        document.getElementById('edit_avatar_preview_wrap').classList.add('hidden');
        document.getElementById('edit_pwd_popup').classList.add('hidden');
        document.getElementById('edit_phone_error').classList.add('hidden');
        document.getElementById('edit_avatar_error').classList.add('hidden');
        document.getElementById('edit_user_avatar').value = '';
        ['edit_pwd_length','edit_pwd_upper_lower','edit_pwd_number','edit_pwd_special','edit_pwd_no_seq'].forEach(id => {
            const li = document.getElementById(id);
            if (li) { li.querySelector('i').className = 'fa-solid fa-circle-xmark text-red-400 text-xs'; li.classList.remove('text-green-400'); li.classList.add('text-gray-300'); }
        });

        const modal = document.getElementById('editUserModal');
        const content = document.getElementById('editUserContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }
    function closeEditUserModal() {
        const modal = document.getElementById('editUserModal');
        const content = document.getElementById('editUserContent');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // ============================================
    // MODAL XEM CHI TIẾT
    // ============================================
    function openShowUserModal(button) {
        const user = JSON.parse(button.getAttribute('data-user'));
        document.getElementById('show_user_fullname').innerText = user.fullname;
        document.getElementById('show_user_email').innerText = user.email;
        document.getElementById('show_user_phone').innerText = user.phone || '—';
        document.getElementById('show_user_address').innerText = user.address || 'Chưa cập nhật';
        document.getElementById('show_user_created_at').innerText = user.created_at;

        // Avatar
        const avatarEl = document.getElementById('show_user_avatar');
        avatarEl.src = user.avatar_url;

        // Role badge
        const roleEl = document.getElementById('show_user_role');
        const roleMap = {
            'admin': '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200"><i class="fa-solid fa-shield-halved mr-1.5"></i> Admin</span>',
            'shipper': '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200"><i class="fa-solid fa-motorcycle mr-1.5"></i> Shipper</span>',
            'customer': '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200"><i class="fa-solid fa-user mr-1.5"></i> Customer</span>',
        };
        roleEl.innerHTML = roleMap[user.role] || user.role;

        // Status badge
        const statusEl = document.getElementById('show_user_status');
        if (user.status === 'locked') {
            statusEl.innerHTML = '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-600"><i class="fa-solid fa-lock mr-1.5"></i> Đã khóa</span>';
        } else {
            statusEl.innerHTML = '<span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700"><i class="fa-solid fa-circle-check mr-1.5"></i> Hoạt động</span>';
        }

        const modal = document.getElementById('showUserModal');
        const content = document.getElementById('showUserContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }
    function closeShowUserModal() {
        const modal = document.getElementById('showUserModal');
        const content = document.getElementById('showUserContent');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // ============================================
    // MODAL XÓA TÀI KHOẢN
    // ============================================
    function openDeleteUserModal(button) {
        const userId = button.getAttribute('data-user-id');
        const userName = button.getAttribute('data-user-name');
        document.getElementById('deleteUserForm').action = '/admin/users/' + userId;
        document.getElementById('delete_user_name').innerText = userName;

        const modal = document.getElementById('deleteUserModal');
        const content = document.getElementById('deleteUserContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }
    function closeDeleteUserModal() {
        const modal = document.getElementById('deleteUserModal');
        const content = document.getElementById('deleteUserContent');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // Auto-open modal nếu có lỗi validation từ server
    @if($errors->any() && old('form_type') === 'add_user')
        openAddUserModal();
    @endif
    @if($errors->any() && old('form_type') === 'edit_user')
        const editBtn = document.querySelector('[data-user-id-edit="{{ old('user_id') }}"]');
        if (editBtn) openEditUserModal(editBtn);
    @endif

    // Submit với client-side validation cho Add form
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        if (!validateForm('add', false)) {
            e.preventDefault();
            return;
        }
        const btn = document.getElementById('addUserSubmitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });

    // Submit với client-side validation cho Edit form
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        if (!validateForm('edit', true)) {
            e.preventDefault();
            return;
        }
        const btn = document.getElementById('editUserSubmitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        setTimeout(() => { btn.disabled = true; }, 10);
    });
</script>
@endpush
