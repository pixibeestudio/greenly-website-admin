@extends('layouts.admin')

@section('title', 'Quản lý Banner - Greenly Admin')

@section('page-title', 'Quản lý Banner')

@section('content')
<div class="fade-in bg-cream-50 rounded-2xl relative min-h-[80vh]">
    <!-- 1. TOP SECTION: STATS & ACTIONS -->
    <div class="flex flex-col lg:flex-row justify-between items-end gap-6 mb-8">

        <!-- Card Thống kê -->
        <div class="w-full lg:w-1/3 bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-6 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <div class="absolute -right-4 -bottom-6 opacity-10">
                <i class="fa-solid fa-images text-9xl"></i>
            </div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-forest-100 text-sm font-semibold uppercase tracking-wider mb-1">Tổng Banner</p>
                    <h2 class="text-5xl font-bold tracking-tight text-white drop-shadow-md">{{ str_pad($banners->count(), 2, '0', STR_PAD_LEFT) }}</h2>
                </div>
                <div class="w-14 h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center text-organic-400 text-2xl shadow-inner">
                    <i class="fa-solid fa-images"></i>
                </div>
            </div>
        </div>

        <!-- Nút Thêm Banner -->
        <div class="w-full lg:w-2/3 flex justify-end">
            <button onclick="openBannerModal()" class="w-full sm:w-auto bg-forest-700 hover:bg-forest-800 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-forest-500/30 flex items-center justify-center gap-2 transition-all font-bold">
                <i class="fa-solid fa-plus"></i> Thêm Banner
            </button>
        </div>
    </div>

    <!-- 2. BẢNG DỮ LIỆU -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-xs font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">STT</th>
                        <th class="px-6 py-4">Hình ảnh</th>
                        <th class="px-6 py-4">Tiêu đề</th>
                        <th class="px-6 py-4 text-center">Thứ tự</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($banners as $index => $banner)
                    <tr class="hover:bg-forest-50/30 transition-colors group">
                        <!-- STT -->
                        <td class="px-6 py-4 text-center font-mono text-gray-500">{{ $index + 1 }}</td>

                        <!-- Hình ảnh -->
                        <td class="px-6 py-4">
                            <div class="w-36 h-18 rounded-xl border border-gray-200 overflow-hidden bg-white shadow-sm group-hover:shadow-md transition-shadow">
                                <img src="{{ asset('storage/' . $banner->image_url) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name=Banner&background=c8e6c9&color=2e7d32'">
                            </div>
                        </td>

                        <!-- Tiêu đề -->
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 text-base group-hover:text-forest-700 transition-colors">{{ $banner->title ?? 'Không có tiêu đề' }}</span>
                        </td>

                        <!-- Thứ tự -->
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-forest-50 text-forest-700 font-bold text-sm">{{ $banner->sort_order }}</span>
                        </td>

                        <!-- Toggle Switch Trạng thái -->
                        <td class="px-6 py-4 text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer toggle-switch" data-id="{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-forest-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-forest-600"></div>
                                <span class="ms-2 text-xs font-semibold toggle-label {{ $banner->is_active ? 'text-forest-600' : 'text-gray-400' }}">{{ $banner->is_active ? 'Bật' : 'Tắt' }}</span>
                            </label>
                        </td>

                        <!-- Hành động -->
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Nút Sửa -->
                                <button onclick="openEditBannerModal(this)" data-banner='@json($banner)' class="bg-amber-50 hover:bg-amber-100 text-amber-600 hover:text-amber-700 px-3 py-1.5 rounded-lg border border-amber-200 flex items-center gap-1.5 font-bold text-xs transition-all">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </button>
                                <!-- Nút Xóa -->
                                <button onclick="openDeleteBannerModal({{ $banner->id }}, '{{ $banner->title ?? 'Banner #' . $banner->id }}')" class="bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 px-3 py-1.5 rounded-lg border border-red-200 flex items-center gap-1.5 font-bold text-xs transition-all">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-image text-4xl text-gray-300 mb-3"></i>
                                <p>Chưa có banner nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL THÊM BANNER --}}
<div id="addBannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div id="addBannerModalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 transform scale-95 transition-transform duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between px-8 py-5">
            <div>
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-plus-circle text-forest-600"></i> Thêm Banner Mới</h3>
                <p class="text-sm text-gray-400 mt-1">Tải lên hình ảnh banner và thiết lập thông tin hiển thị.</p>
            </div>
            <button onclick="closeBannerModal()" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="addBannerForm" action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="px-8 pb-8">
            @csrf
            <div class="border border-gray-100 rounded-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8">

                    <!-- CỘT TRÁI: Ảnh Banner (2/5) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Ảnh Banner <span class="text-red-500">*</span></label>

                        <!-- Vùng Upload / Preview -->
                        <div class="relative cursor-pointer group" onclick="document.getElementById('addBannerImage').click()">
                            <!-- Placeholder (khung dashed) -->
                            <div id="addPlaceholder" class="w-full aspect-[16/9] rounded-xl border-2 border-dashed border-gray-300 group-hover:border-forest-400 flex flex-col items-center justify-center bg-gray-50 group-hover:bg-forest-50/30 transition-all">
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-forest-400 mb-3"></i>
                                <p class="text-sm font-semibold text-gray-600">Chọn hình ảnh</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP. Tối đa 2MB</p>
                            </div>
                            <!-- Ảnh Preview (ẩn mặc định) -->
                            <img id="addPreview" src="#" alt="Preview" class="w-full aspect-[16/9] object-cover rounded-xl border border-gray-200 hidden">
                            <!-- Overlay đổi ảnh khi hover lên preview -->
                            <div id="addChangeOverlay" class="absolute inset-0 bg-black/40 rounded-xl items-center justify-center hidden group-hover:flex transition-all">
                                <span class="text-white text-sm font-bold"><i class="fa-solid fa-camera mr-1"></i> Đổi ảnh</span>
                            </div>
                        </div>
                        <input type="file" name="image" id="addBannerImage" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewBannerImage(this, 'addPreview', 'addPlaceholder', 'addChangeOverlay')" class="hidden">
                        <p id="addImageError" class="text-red-500 text-xs mt-2 hidden"></p>
                        @error('image')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- CỘT PHẢI: Thông tin (3/5) -->
                    <div class="md:col-span-3 flex flex-col gap-5">
                        <!-- Tiêu đề -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề Banner</label>
                            <input type="text" name="title" value="{{ old('title') }}" placeholder="VD: Khuyến mãi mùa hè, Sản phẩm mới..." class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Thứ tự hiển thị -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Thứ tự hiển thị</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" placeholder="0" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all">
                            <p class="text-xs text-gray-400 mt-1">Số nhỏ hơn sẽ hiển thị trước. Mặc định: 0</p>
                        </div>

                        <!-- Nút Submit -->
                        <div class="flex justify-end gap-3 pt-4 mt-auto">
                            <button type="button" onclick="closeBannerModal()" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold text-sm transition-all">Hủy</button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-forest-700 hover:bg-forest-800 text-white font-bold text-sm shadow-lg shadow-forest-500/30 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-save"></i> Lưu Banner
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SỬA BANNER --}}
<div id="editBannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div id="editBannerModalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 transform scale-95 transition-transform duration-300">
        <!-- Header -->
        <div class="flex items-center justify-between px-8 py-5">
            <div>
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-amber-500"></i> Cập Nhật Banner</h3>
                <p class="text-sm text-gray-400 mt-1">Thay đổi hình ảnh hoặc chỉnh sửa thông tin banner.</p>
            </div>
            <button onclick="closeEditBannerModal()" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editBannerForm" action="" method="POST" enctype="multipart/form-data" class="px-8 pb-8">
            @csrf
            @method('PUT')
            <div class="border border-gray-100 rounded-2xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-8">

                    <!-- CỘT TRÁI: Ảnh Banner (2/5) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Ảnh Banner</label>

                        <!-- Vùng Upload / Preview -->
                        <div class="relative cursor-pointer group" onclick="document.getElementById('editBannerImage').click()">
                            <!-- Placeholder (nếu không có ảnh) -->
                            <div id="editPlaceholder" class="w-full aspect-[16/9] rounded-xl border-2 border-dashed border-gray-300 group-hover:border-amber-400 flex flex-col items-center justify-center bg-gray-50 group-hover:bg-amber-50/30 transition-all hidden">
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-amber-400 mb-3"></i>
                                <p class="text-sm font-semibold text-gray-600">Chọn hình ảnh mới</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP. Tối đa 2MB</p>
                            </div>
                            <!-- Ảnh Preview -->
                            <img id="editPreview" src="#" alt="Preview" class="w-full aspect-[16/9] object-cover rounded-xl border border-gray-200">
                            <!-- Overlay đổi ảnh -->
                            <div id="editChangeOverlay" class="absolute inset-0 bg-black/40 rounded-xl items-center justify-center hidden group-hover:flex transition-all">
                                <span class="text-white text-sm font-bold"><i class="fa-solid fa-camera mr-1"></i> Đổi ảnh</span>
                            </div>
                        </div>
                        <input type="file" name="image" id="editBannerImage" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewBannerImage(this, 'editPreview', 'editPlaceholder', 'editChangeOverlay')" class="hidden">
                        <p id="editImageError" class="text-red-500 text-xs mt-2 hidden"></p>
                    </div>

                    <!-- CỘT PHẢI: Thông tin (3/5) -->
                    <div class="md:col-span-3 flex flex-col gap-5">
                        <!-- Tiêu đề -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề Banner</label>
                            <input type="text" name="title" id="editBannerTitle" placeholder="VD: Khuyến mãi mùa hè, Sản phẩm mới..." class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all">
                        </div>

                        <!-- Thứ tự hiển thị -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Thứ tự hiển thị</label>
                            <input type="number" name="sort_order" id="editBannerSortOrder" min="0" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all">
                            <p class="text-xs text-gray-400 mt-1">Số nhỏ hơn sẽ hiển thị trước. Mặc định: 0</p>
                        </div>

                        <!-- Nút Submit -->
                        <div class="flex justify-end gap-3 pt-4 mt-auto">
                            <button type="button" onclick="closeEditBannerModal()" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold text-sm transition-all">Hủy</button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm shadow-lg shadow-amber-500/30 transition-all flex items-center gap-2">
                                <i class="fa-solid fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL XÁC NHẬN XÓA --}}
<div id="deleteBannerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
    <div id="deleteBannerModalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform scale-95 transition-transform duration-300">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-trash-can text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Xác nhận xóa Banner</h3>
            <p class="text-gray-500 text-sm mb-6">Bạn có chắc chắn muốn xóa banner <strong id="deleteBannerName" class="text-red-600"></strong>? Hành động này không thể hoàn tác.</p>

            <form id="deleteBannerForm" action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center gap-3">
                    <button type="button" onclick="closeDeleteBannerModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold text-sm transition-all">Hủy</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-sm shadow-lg shadow-red-500/30 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-trash-can"></i> Xóa Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // === PREVIEW ẢNH (dùng FileReader, hỗ trợ overlay đổi ảnh) ===
    function previewBannerImage(input, previewId, placeholderId, overlayId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        const overlay = overlayId ? document.getElementById(overlayId) : null;

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validate client-side
            if (!['image/jpeg', 'image/png', 'image/jpg', 'image/webp'].includes(file.type)) {
                const errId = input.id === 'addBannerImage' ? 'addImageError' : 'editImageError';
                const errEl = document.getElementById(errId);
                errEl.innerText = 'Chỉ chấp nhận định dạng: jpeg, png, jpg, webp.';
                errEl.classList.remove('hidden');
                input.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                const errId = input.id === 'addBannerImage' ? 'addImageError' : 'editImageError';
                const errEl = document.getElementById(errId);
                errEl.innerText = 'Kích thước ảnh không được vượt quá 2MB.';
                errEl.classList.remove('hidden');
                input.value = '';
                return;
            }

            // Xóa lỗi cũ nếu có
            const errId = input.id === 'addBannerImage' ? 'addImageError' : 'editImageError';
            const errEl = document.getElementById(errId);
            errEl.innerText = ''; errEl.classList.add('hidden');

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                // Hiện overlay "Đổi ảnh" khi đã có ảnh preview
                if (overlay) overlay.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }

    // === MODAL THÊM ===
    function openBannerModal() {
        const modal = document.getElementById('addBannerModal');
        const content = document.getElementById('addBannerModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function closeBannerModal() {
        const modal = document.getElementById('addBannerModal');
        const content = document.getElementById('addBannerModalContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);

        // Reset form
        document.getElementById('addBannerForm').reset();
        document.getElementById('addPreview').src = '#';
        document.getElementById('addPreview').classList.add('hidden');
        document.getElementById('addPlaceholder').classList.remove('hidden');
        document.getElementById('addChangeOverlay').classList.add('hidden');
        const addErr = document.getElementById('addImageError');
        addErr.innerText = ''; addErr.classList.add('hidden');
    }

    // === MODAL SỬA ===
    function openEditBannerModal(button) {
        const banner = JSON.parse(button.getAttribute('data-banner'));

        document.getElementById('editBannerForm').action = '/admin/banners/' + banner.id;
        document.getElementById('editBannerTitle').value = banner.title || '';
        document.getElementById('editBannerSortOrder').value = banner.sort_order || 0;

        // Hiển thị ảnh hiện tại
        const preview = document.getElementById('editPreview');
        const placeholder = document.getElementById('editPlaceholder');
        const overlay = document.getElementById('editChangeOverlay');

        if (banner.image_url) {
            preview.src = '/storage/' + banner.image_url;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            overlay.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            overlay.classList.add('hidden');
        }

        const modal = document.getElementById('editBannerModal');
        const content = document.getElementById('editBannerModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function closeEditBannerModal() {
        const modal = document.getElementById('editBannerModal');
        const content = document.getElementById('editBannerModalContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);

        document.getElementById('editBannerImage').value = '';
        const editErr = document.getElementById('editImageError');
        editErr.innerText = ''; editErr.classList.add('hidden');
    }

    // === MODAL XÓA ===
    function openDeleteBannerModal(id, name) {
        document.getElementById('deleteBannerName').innerText = name;
        document.getElementById('deleteBannerForm').action = '/admin/banners/' + id;

        const modal = document.getElementById('deleteBannerModal');
        const content = document.getElementById('deleteBannerModalContent');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function closeDeleteBannerModal() {
        const modal = document.getElementById('deleteBannerModal');
        const content = document.getElementById('deleteBannerModalContent');
        modal.classList.add('opacity-0');
        content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // === AJAX TOGGLE SWITCH ===
    document.querySelectorAll('.toggle-switch').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const bannerId = this.getAttribute('data-id');
            const label = this.closest('label').querySelector('.toggle-label');
            const checkbox = this;

            fetch('/admin/banners/' + bannerId + '/toggle-active', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật label text + màu
                    label.innerText = data.is_active ? 'Bật' : 'Tắt';
                    label.classList.toggle('text-forest-600', data.is_active);
                    label.classList.toggle('text-gray-400', !data.is_active);
                }
            })
            .catch(() => {
                // Revert lại nếu lỗi
                checkbox.checked = !checkbox.checked;
                if (typeof showErrorNotification === 'function') {
                    showErrorNotification('Cập nhật trạng thái thất bại!');
                }
            });
        });
    });

    // === TỰ ĐỘNG MỞ LẠI MODAL KHI CÓ LỖI VALIDATE ===
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            openBannerModal();
        });
    @endif
</script>
@endpush
