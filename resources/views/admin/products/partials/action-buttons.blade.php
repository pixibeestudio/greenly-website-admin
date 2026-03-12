<div class="flex items-center justify-center gap-2">
    <button onclick="openShowProductModal(this)"
            data-product="{{ json_encode([
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'unit' => $product->unit,
                'description' => $product->description ?? 'Không có mô tả',
                'origin' => $product->origin ?? 'Chưa cập nhật',
                'is_active' => $product->is_active,
                'category_name' => $product->category->name ?? 'Không có',
                'stock' => (int) ($product->batches_sum_current_quantity ?? 0),
                'images' => $product->images->map(fn($img) => asset('storage/' . $img->image_path))->toArray(),
                'created_at' => $product->created_at->format('d/m/Y H:i'),
                'updated_at' => $product->updated_at->eq($product->created_at) ? 'Chưa được cập nhật' : $product->updated_at->format('d/m/Y H:i'),
            ]) }}"
            class="w-8 h-8 rounded-full bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xem chi tiết">
        <i class="fa-solid fa-eye text-xs"></i>
    </button>
    <button onclick="openEditProductModal(this)"
            data-product="{{ json_encode($product) }}"
            class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Chỉnh sửa">
        <i class="fa-solid fa-pen text-xs"></i>
    </button>
    <button onclick="openDeleteProductModal({{ $product->id }}, '{{ htmlspecialchars($product->name, ENT_QUOTES) }}')"
            class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xóa">
        <i class="fa-solid fa-trash-can text-xs"></i>
    </button>
</div>