<div class="flex items-center justify-center gap-1.5">
    <button onclick="openShowBatchModal(this)"
            data-batch="{{ json_encode($batch->load(['product', 'supplier'])) }}"
            class="w-8 h-8 rounded-full text-blue-500 hover:bg-blue-50 transition-all flex items-center justify-center" title="Xem chi tiết">
        <i class="fa-solid fa-eye text-xs"></i>
    </button>
    <button onclick="openEditBatchModal(this)"
            data-batch="{{ json_encode($batch) }}"
            class="w-8 h-8 rounded-full text-green-500 hover:bg-green-50 transition-all flex items-center justify-center" title="Chỉnh sửa">
        <i class="fa-solid fa-pen text-xs"></i>
    </button>
    <button onclick="openDeleteBatchModal({{ $batch->id }}, '{{ htmlspecialchars($batch->batch_code, ENT_QUOTES) }}')"
            class="w-8 h-8 rounded-full text-red-500 hover:bg-red-50 transition-all flex items-center justify-center" title="Xóa">
        <i class="fa-solid fa-trash-can text-xs"></i>
    </button>
</div>
