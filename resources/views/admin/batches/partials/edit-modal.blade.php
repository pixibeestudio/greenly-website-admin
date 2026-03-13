<!-- Modal Background Overlay -->
<div id="editBatchModal"
    class="fixed inset-0 bg-black/50 z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <!-- Modal Content -->
    <div id="editBatchContent"
        class="bg-gray-50 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i> Sửa Lô Hàng
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Cập nhật thông tin lô hàng trong kho.</p>
            </div>
            <button type="button" onclick="closeEditBatchModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editBatchForm" action="" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_type" value="edit_batch">

            <div class="p-8 md:p-10">
                <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                    <div class="space-y-6">

                        <!-- Dòng 0: ID (readonly) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Mã lô (ID)</label>
                            <input type="text" id="edit_batch_id" readonly
                                class="w-full bg-gray-100 border border-gray-200 text-gray-500 text-base rounded-xl block px-4 py-3 outline-none shadow-sm cursor-not-allowed font-mono">
                        </div>

                        <!-- Dòng 1: Mã Lô Hàng -->
                        <div>
                            <label for="edit_batch_code" class="block text-sm font-bold text-gray-700 mb-2">Mã lô hàng <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fa-solid fa-barcode absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" id="edit_batch_code" name="batch_code" required
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm font-mono"
                                    placeholder="VD: LOT-2026-001">
                            </div>
                            <p id="edit_batch_code_error" class="text-red-500 text-xs mt-1.5 {{ old('form_type') == 'edit_batch' && $errors->has('batch_code') ? '' : 'hidden' }}">{{ old('form_type') == 'edit_batch' ? $errors->first('batch_code') : '' }}</p>
                        </div>

                        <!-- Dòng 2: Sản phẩm + Nhà cung cấp -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Sản phẩm -->
                            <div>
                                <label for="edit_batch_product_id" class="block text-sm font-bold text-gray-700 mb-2">Sản phẩm <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="edit_batch_product_id" name="product_id" required
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm appearance-none cursor-pointer">
                                        <option value="" disabled>-- Chọn sản phẩm --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none text-sm"></i>
                                </div>
                            </div>

                            <!-- Nhà cung cấp -->
                            <div>
                                <label for="edit_batch_supplier_id" class="block text-sm font-bold text-gray-700 mb-2">Nhà cung cấp <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="edit_batch_supplier_id" name="supplier_id" required
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm appearance-none cursor-pointer">
                                        <option value="" disabled>-- Chọn NCC --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Dòng 3: Giá nhập + Số lượng ban đầu -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Giá nhập -->
                            <div>
                                <label for="edit_batch_import_price" class="block text-sm font-bold text-gray-700 mb-2">Giá nhập (đơn giá) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" id="edit_batch_import_price" name="import_price" required
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-4 pr-12 py-3 outline-none transition-all shadow-sm font-mono text-right"
                                        placeholder="0" autocomplete="off" inputmode="numeric"
                                        oninput="formatBatchCurrency(this)" onkeydown="return filterBatchCurrencyKeydown(event)">
                                    <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-bold text-sm pointer-events-none">₫</span>
                                </div>
                            </div>

                            <!-- Số lượng ban đầu -->
                            <div>
                                <label for="edit_batch_quantity" class="block text-sm font-bold text-gray-700 mb-2">SL ban đầu <span class="text-red-500">*</span></label>
                                <input type="number" id="edit_batch_quantity" name="quantity" required min="1"
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm"
                                    placeholder="VD: 100">
                            </div>
                        </div>

                        <!-- Dòng 4: Số lượng tồn kho hiện tại -->
                        <div>
                            <label for="edit_batch_current_quantity" class="block text-sm font-bold text-gray-700 mb-2">Tồn kho hiện tại <span class="text-red-500">*</span></label>
                            <input type="number" id="edit_batch_current_quantity" name="current_quantity" required min="0"
                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm"
                                placeholder="VD: 50">
                            <p class="text-[11px] text-gray-400 mt-1">Số lượng sản phẩm hiện có trong kho của lô này.</p>
                        </div>

                    </div>

                    <!-- ================= BUTTONS ================= -->
                    <div class="flex justify-end items-center gap-4 mt-8 pt-6 border-t border-gray-100">
                        <button type="button" onclick="closeEditBatchModal()"
                            class="px-6 py-3 bg-white text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200">
                            Hủy
                        </button>
                        <button type="submit" id="edit_batch_submit_btn"
                            class="px-8 py-3 bg-forest-700 text-white font-bold rounded-xl hover:bg-forest-800 shadow-lg shadow-forest-500/30 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> Cập nhật
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

@if(old('form_type') == 'edit_batch' && $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động mở lại modal Sửa lô hàng khi có lỗi validation
        const modal = document.getElementById('editBatchModal');
        const content = document.getElementById('editBatchContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);

        // Khôi phục giá nhập (format lại dấu chấm ngàn)
        const price = "{{ old('import_price') }}";
        if (price) {
            const priceNum = parseInt(price) || 0;
            document.getElementById('edit_batch_import_price').value = priceNum > 0 ? priceNum.toLocaleString('vi-VN') : '';
        }
    });
</script>
@endif
