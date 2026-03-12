<div class="flex items-center justify-center gap-2">
    <button onclick="openEditSupplierModal(this)"
            data-supplier="{{ json_encode($supplier) }}"
            class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Chỉnh sửa">
        <i class="fa-solid fa-pen text-xs"></i>
    </button>
    <button onclick="openDeleteSupplierModal({{ $supplier->id }}, '{{ htmlspecialchars($supplier->name, ENT_QUOTES) }}')"
            class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xóa">
        <i class="fa-solid fa-trash-can text-xs"></i>
    </button>
</div>
