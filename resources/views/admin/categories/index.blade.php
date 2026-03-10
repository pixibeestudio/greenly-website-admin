@extends('layouts.admin')

@section('title', 'Danh mục Sản phẩm - Greenly Admin')

@section('page-title', 'Danh mục Sản phẩm')

@section('content')
<div class="fade-in bg-cream-50 rounded-2xl relative min-h-[80vh]">
    <!-- 1. TOP SECTION: STATS CARD & ACTIONS -->
    <div class="flex flex-col lg:flex-row justify-between items-end gap-6 mb-8">
        
        <!-- Card Thống kê Tổng Danh Mục (Sang trọng, Gradient) -->
        <div class="w-full lg:w-1/3 bg-gradient-to-br from-forest-900 via-forest-800 to-forest-700 p-6 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <!-- Hiệu ứng lá mờ background -->
            <div class="absolute -right-4 -bottom-6 opacity-10">
                <i class="fa-solid fa-leaf text-9xl"></i>
            </div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-forest-100 text-sm font-semibold uppercase tracking-wider mb-1">Tổng Danh Mục</p>
                    <h2 class="text-5xl font-bold tracking-tight text-white drop-shadow-md">{{ str_pad($totalCategories, 2, '0', STR_PAD_LEFT) }}</h2>
                </div>
                <div class="w-14 h-14 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center text-organic-400 text-2xl shadow-inner">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
        </div>

        <!-- Thanh công cụ (Bộ lọc & Thêm mới) -->
        <div class="w-full lg:w-2/3 flex flex-col sm:flex-row justify-end items-center gap-4">
            
            <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Combobox chọn số lượng hiển thị -->
                <select name="per_page" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-gray-600 outline-none focus:border-forest-500 transition-all shadow-sm">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 mục</option>
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 mục</option>
                    <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30 mục</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 mục</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 mục</option>
                </select>

                <!-- Lọc theo ngày tạo (Date Picker custom) -->
                <div class="relative group w-full sm:w-auto">
                    <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500 transition-all">
                        <i class="fa-regular fa-calendar-days text-forest-600 mr-3"></i>
                        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="bg-transparent text-sm text-gray-600 outline-none w-32 cursor-pointer font-medium relative z-10">
                    </div>
                    <!-- Label nhỏ nổi lên khi hover -->
                    <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Lọc ngày tạo</span>
                </div>

                <!-- Tìm kiếm -->
                <div class="relative group w-full sm:w-auto">
                    <div class="flex items-center bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500 transition-all">
                        <i class="fa-solid fa-search text-gray-400 mr-3 group-focus-within:text-forest-500"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên danh mục..." class="bg-transparent text-sm text-gray-600 outline-none w-full sm:w-48 placeholder-gray-400">
                    </div>
                </div>
            </form>

            <!-- Nút Thêm Mới -->
            <button onclick="openModal()" class="w-full sm:w-auto bg-forest-700 hover:bg-forest-800 text-white px-6 py-2.5 rounded-xl shadow-lg shadow-forest-500/30 flex items-center justify-center gap-2 transition-all font-bold">
                <i class="fa-solid fa-plus"></i> Thêm Danh Mục
            </button>
        </div>
    </div>

    <!-- 2. BẢNG DỮ LIỆU (SIMPLE IN CARD STYLE) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-cream-100/50 text-gray-500 uppercase text-xs font-bold border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 w-16 text-center">ID</th>
                        <th class="px-6 py-4">Hình ảnh</th>
                        <th class="px-6 py-4">Tên Danh mục</th>
                        <th class="px-6 py-4 max-w-xs">Mô tả</th>
                        <th class="px-6 py-4">Ngày tạo</th>
                        <th class="px-6 py-4">Cập nhật</th>
                        <th class="px-6 py-4 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    
                    @forelse ($categories as $category)
                    <tr class="hover:bg-forest-50/30 transition-colors group">
                        <td class="px-6 py-4 text-center font-mono text-gray-500">#{{ $category->id }}</td>
                        <td class="px-6 py-4">
                            <div class="w-12 h-12 rounded-xl border border-gray-200 overflow-hidden bg-white shadow-sm group-hover:shadow-md transition-shadow p-1">
                                <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover rounded-lg" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($category->name) }}&background=c8e6c9&color=2e7d32'">
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 text-base group-hover:text-forest-700 transition-colors">{{ $category->name }}</span>
                        </td>
                        <td class="px-6 py-4 max-w-xs whitespace-normal">
                            <p class="text-gray-600 line-clamp-2 text-sm">{{ $category->description ?? 'Không có mô tả' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-700 font-medium">{{ $category->created_at->format('d/m/Y') }}</div>
                            <div class="text-[11px] text-gray-400">{{ $category->created_at->format('H:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($category->updated_at != $category->created_at)
                                <div class="text-gray-700 font-medium">{{ $category->updated_at->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-gray-400">{{ $category->updated_at->format('H:i A') }}</div>
                            @else
                                <div class="text-gray-400 italic text-sm">-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @include('admin.categories.partials.action-buttons', ['category' => $category])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-3"></i>
                                <p>Chưa có danh mục nào.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-b-2xl">
            <span class="text-sm text-gray-500 font-medium">Hiển thị {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} trên tổng số {{ $categories->total() }} danh mục</span>
            <div class="w-full sm:w-auto">
                {{ $categories->links('vendor.pagination.greenly') }}
            </div>
        </div>
    </div>
</div>

@include('admin.categories.partials.form-modal')
@include('admin.categories.partials.show-modal')
@include('admin.categories.partials.edit-modal')
@include('admin.categories.partials.delete-modal')

@endsection

@push('scripts')
<script>
    // Hàm mở modal xem chi tiết
    function openShowModal(button) {
        // Lấy dữ liệu JSON từ nút bấm
        const categoryData = JSON.parse(button.getAttribute('data-category'));
        
        // Cập nhật thông tin vào các trường trong modal
        document.getElementById('show_id').innerText = '#' + categoryData.id;
        document.getElementById('show_name').innerText = categoryData.name;
        document.getElementById('show_image').src = categoryData.image;
        document.getElementById('show_description').innerText = categoryData.description;
        document.getElementById('show_created_at').innerText = categoryData.created_at;
        document.getElementById('show_updated_at').innerText = categoryData.updated_at;

        // Xử lý hiệu ứng hiển thị modal
        const modal = document.getElementById('showCategoryModal');
        const modalContent = document.getElementById('showModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

    // Hàm đóng modal xem chi tiết
    function closeShowModal() {
        const modal = document.getElementById('showCategoryModal');
        const modalContent = document.getElementById('showModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Hàm mở modal cập nhật
    function openEditModal(button) {
        const category = JSON.parse(button.getAttribute('data-category'));
        
        // Điền thông tin vào form
        document.getElementById('edit_id').value = category.id;
        document.getElementById('edit_category_id').value = category.id;
        document.getElementById('edit_name').value = category.name;
        
        const descInput = document.getElementById('edit_description');
        descInput.value = category.description || '';
        if (typeof updateEditCharCount === 'function') {
            updateEditCharCount(descInput);
        }

        // Xử lý hiển thị ảnh
        const preview = document.getElementById('editImagePreview');
        const placeholder = document.getElementById('editUploadPlaceholder');
        const changeOverlay = document.getElementById('editChangeImageOverlay');
        
        if (category.image) {
            // Chú ý: Ở đây category.image lấy từ JSON ban đầu không chứa hàm asset, nên phải xử lý cẩn thận.
            // Tuy nhiên, data-category của edit mình dùng json_encode($category) nguyên gốc từ model
            // Đường dẫn trong DB là 'storage/categories/...'
            preview.src = '/' + category.image; 
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            changeOverlay.classList.remove('hidden');
        } else {
            preview.src = '#';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            changeOverlay.classList.add('hidden');
        }

        // Cập nhật action của form
        document.getElementById('editCategoryForm').action = '/admin/categories/' + category.id;

        // Hiển thị modal
        const modal = document.getElementById('editCategoryModal');
        const modalContent = document.getElementById('editModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

    // Hàm đóng modal cập nhật
    function closeEditModal() {
        const modal = document.getElementById('editCategoryModal');
        const modalContent = document.getElementById('editModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);

        // Reset thẻ báo lỗi form Sửa
        const editNameErr = document.getElementById('edit_name_error');
        const editImageErr = document.getElementById('edit_image_error');
        editNameErr.innerText = ''; editNameErr.classList.add('hidden');
        editImageErr.innerText = ''; editImageErr.classList.add('hidden');
    }

    // Hàm mở modal xác nhận xóa
    function openDeleteModal(id, name) {
        // Điền tên danh mục
        document.getElementById('delete_category_name').innerText = name;
        
        // Cập nhật action của form
        document.getElementById('deleteCategoryForm').action = '/admin/categories/' + id;
        
        // Hiển thị modal
        const modal = document.getElementById('deleteCategoryModal');
        const modalContent = document.getElementById('deleteModalContent');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

    // Hàm đóng modal xác nhận xóa
    function closeDeleteModal() {
        const modal = document.getElementById('deleteCategoryModal');
        const modalContent = document.getElementById('deleteModalContent');
        
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Hàm validate ảnh dùng chung cho cả 2 form
    function validateImage(fileInput, errorId) {
        const file = fileInput.files[0];
        const errorEl = document.getElementById(errorId);

        if (!file) return true;

        if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
            errorEl.innerText = 'Chỉ cho phép tải hình ảnh PNG, JPG, WEBP';
            errorEl.classList.remove('hidden');
            fileInput.value = '';
            return false;
        }

        if (file.size > 2097152) {
            errorEl.innerText = 'Chỉ cho phép hình ảnh có kích thước dưới 2MB';
            errorEl.classList.remove('hidden');
            fileInput.value = '';
            return false;
        }

        errorEl.innerText = '';
        errorEl.classList.add('hidden');
        return true;
    }

    // Hàm reset toàn bộ form Thêm khi đóng modal
    const _originalCloseModal = closeModal;
    closeModal = function() {
        _originalCloseModal();

        // Reset thẻ báo lỗi
        const addNameErr = document.getElementById('add_name_error');
        const addImageErr = document.getElementById('add_image_error');
        addNameErr.innerText = ''; addNameErr.classList.add('hidden');
        addImageErr.innerText = ''; addImageErr.classList.add('hidden');

        // Reset input tên + xóa viền đỏ
        const nameInput = document.getElementById('categoryName');
        nameInput.value = '';
        nameInput.classList.remove('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');

        // Reset textarea mô tả + bộ đếm ký tự
        document.getElementById('categoryDesc').value = '';
        const charCount = document.getElementById('charCount');
        charCount.innerText = '0';
        charCount.classList.remove('text-organic-500');

        // Reset ảnh preview về mặc định
        document.getElementById('imageUpload').value = '';
        document.getElementById('imagePreview').src = '#';
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('uploadPlaceholder').classList.remove('hidden');
        document.getElementById('changeImageOverlay').classList.add('hidden');

        // Reset nút submit
        clearTimeout(window._submitBtnTimeout);
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fa-solid fa-save"></i> Lưu danh mục';
        submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
        submitBtn.disabled = false;
    };

    // Validate tên chống toàn số khi submit Form Thêm
    document.getElementById('addCategoryForm').addEventListener('submit', function (e) {
        const nameValue = document.getElementById('categoryName').value;
        const nameErr = document.getElementById('add_name_error');

        if (/^\d+$/.test(nameValue.trim())) {
            e.preventDefault();
            clearTimeout(window._submitBtnTimeout);
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fa-solid fa-save"></i> Lưu danh mục';
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            submitBtn.disabled = false;
            nameErr.innerText = 'Không cho phép đặt tên bằng số';
            nameErr.classList.remove('hidden');
            showErrorNotification('Dữ liệu không hợp lệ');
            return;
        }

        nameErr.innerText = '';
        nameErr.classList.add('hidden');
    });

    // Validate tên chống toàn số khi submit Form Sửa
    document.getElementById('editCategoryForm').addEventListener('submit', function (e) {
        const nameValue = document.getElementById('edit_name').value;
        const nameErr = document.getElementById('edit_name_error');

        if (/^\d+$/.test(nameValue.trim())) {
            e.preventDefault();
            clearTimeout(window._updateBtnTimeout);
            const updateBtn = document.getElementById('updateBtn');
            updateBtn.innerHTML = '<i class="fa-solid fa-save"></i> Cập nhật danh mục';
            updateBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            updateBtn.disabled = false;
            nameErr.innerText = 'Không cho phép đặt tên bằng số';
            nameErr.classList.remove('hidden');
            showErrorNotification('Dữ liệu không hợp lệ');
            return;
        }

        nameErr.innerText = '';
        nameErr.classList.add('hidden');
    });

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            showErrorNotification("{{ $errors->first() }}");

            @if(old('_method') == 'PUT')
                // Mở lại Form Sửa nếu lỗi từ thao tác Cập nhật (PUT)
                const editCatId = "{{ old('category_id') }}";
                // Tìm nút Sửa trong bảng để lấy dữ liệu category gốc (bao gồm ảnh)
                const allEditBtns = document.querySelectorAll('[onclick^="openEditModal"]');
                let targetBtn = null;
                allEditBtns.forEach(btn => {
                    try {
                        const cat = JSON.parse(btn.getAttribute('data-category'));
                        if (String(cat.id) === String(editCatId)) targetBtn = btn;
                    } catch(e) {}
                });
                if (targetBtn) {
                    openEditModal(targetBtn);
                    // Ghi đè lại giá trị name/description bằng old() (giá trị user đã nhập)
                    document.getElementById('edit_name').value = "{{ old('name') }}";
                    document.getElementById('edit_description').value = "{{ old('description') }}";
                    if (typeof updateEditCharCount === 'function') {
                        updateEditCharCount(document.getElementById('edit_description'));
                    }
                }
                // Hiện lỗi name nếu có
                @if($errors->has('name'))
                    let editNameErr = document.getElementById('edit_name_error');
                    editNameErr.innerText = "{{ $errors->first('name') }}";
                    editNameErr.classList.remove('hidden');
                @endif
            @else
                // Mở lại Form Thêm nếu lỗi từ thao tác Thêm mới (POST)
                openModal();
                @if($errors->has('name'))
                    let addNameErr = document.getElementById('add_name_error');
                    addNameErr.innerText = "{{ $errors->first('name') }}";
                    addNameErr.classList.remove('hidden');
                @endif
            @endif
        });
    @endif
</script>

@if($errors->has('name'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tự động mở lại modal Thêm Danh mục để người dùng thấy lỗi
        openModal();
        const nameError = document.getElementById('name_error');
        if (nameError) {
            nameError.innerText = '{{ $errors->first("name") }}';
            nameError.classList.remove('hidden');
        }
        
        const nameInput = document.getElementById('categoryName');
        if (nameInput) {
            nameInput.classList.add('border-red-500', 'focus:ring-red-500', 'focus:border-red-500');
        }
    });
</script>
@endif

@endpush
