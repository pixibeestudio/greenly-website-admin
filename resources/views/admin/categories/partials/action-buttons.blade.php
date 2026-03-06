<div class="flex items-center justify-center gap-2">
    <button onclick="openShowModal(this)" 
            data-category="{{ json_encode([
                'id' => $category->id,
                'name' => $category->name,
                'image' => asset($category->image),
                'description' => $category->description ?? 'Không có mô tả',
                'created_at' => $category->created_at->format('d/m/Y - H:i A'),
                'updated_at' => $category->updated_at == $category->created_at ? 'Chưa được cập nhật' : $category->updated_at->format('d/m/Y - H:i A')
            ]) }}"
            class="w-8 h-8 rounded-full bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xem chi tiết">
        <i class="fa-solid fa-eye text-xs"></i>
    </button>
    <button onclick="openEditModal(this)"
            data-category="{{ json_encode($category) }}"
            class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Chỉnh sửa">
        <i class="fa-solid fa-pen text-xs"></i>
    </button>
    <button onclick="openDeleteModal({{ $category->id }}, '{{ htmlspecialchars($category->name, ENT_QUOTES) }}')" 
            class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xóa">
        <i class="fa-solid fa-trash-can text-xs"></i>
    </button>
</div>