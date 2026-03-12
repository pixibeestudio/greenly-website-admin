<!-- Modal Background Overlay -->
<div id="editSupplierModal"
    class="fixed inset-0 bg-black/50 z-60 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <!-- Modal Content -->
    <div id="editSupplierContent"
        class="bg-gray-50 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-blue-600"></i> Cập Nhật Nhà Cung Cấp
                </h2>
                <p class="text-gray-500 text-sm mt-0.5">Chỉnh sửa thông tin đối tác cung ứng.</p>
            </div>
            <button type="button" onclick="closeEditSupplierModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editSupplierForm" action="" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="form_type" value="edit_supplier">
            <input type="hidden" name="supplier_id" id="edit_supplier_hidden_id" value="{{ old('supplier_id') }}">

            <div class="p-8 md:p-10">
                <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                    <div class="space-y-6">

                        <!-- Dòng 1: ID + Tên Nhà Cung Cấp -->
                        <div class="flex gap-4">
                            <!-- ID (readonly) -->
                            <div class="w-24">
                                <label for="edit_supplier_id" class="block text-sm font-bold text-gray-700 mb-2">ID</label>
                                <input type="text" id="edit_supplier_id" readonly
                                    class="w-full bg-gray-100 border border-gray-300 text-gray-500 text-base font-mono rounded-xl block px-4 py-3 outline-none cursor-not-allowed">
                            </div>
                            <!-- Tên nhà cung cấp -->
                            <div class="flex-1">
                                <label for="edit_supplier_name" class="block text-sm font-bold text-gray-700 mb-2">Tên nhà cung cấp <span class="text-red-500">*</span></label>
                                <input type="text" id="edit_supplier_name" name="name" required
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm"
                                    placeholder="VD: Nông trại VinEco...">
                                <p id="edit_supplier_name_error" class="text-red-500 text-xs mt-1.5 {{ old('form_type') == 'edit_supplier' && $errors->has('name') ? '' : 'hidden' }}">{{ old('form_type') == 'edit_supplier' ? $errors->first('name') : '' }}</p>
                            </div>
                        </div>

                        <!-- Dòng 2: Người liên hệ + SĐT -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Người liên hệ -->
                            <div>
                                <label for="edit_supplier_contact_name" class="block text-sm font-bold text-gray-700 mb-2">Người liên hệ</label>
                                <div class="relative">
                                    <i class="fa-solid fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" id="edit_supplier_contact_name" name="contact_name"
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="VD: Nguyễn Văn A...">
                                </div>
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label for="edit_supplier_phone" class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                                <div class="relative">
                                    <i class="fa-solid fa-phone absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" id="edit_supplier_phone" name="phone"
                                        class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                        placeholder="VD: 0912.345.678">
                                </div>
                                <p id="edit_supplier_phone_error" class="text-red-500 text-xs mt-1.5 {{ old('form_type') == 'edit_supplier' && $errors->has('phone') ? '' : 'hidden' }}">{{ old('form_type') == 'edit_supplier' ? $errors->first('phone') : '' }}</p>
                            </div>
                        </div>

                        <!-- Dòng 3: Địa chỉ -->
                        <div>
                            <label for="edit_supplier_address" class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ (Khu vực) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fa-solid fa-location-dot absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" id="edit_supplier_address" name="address" required
                                    class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block pl-10 pr-4 py-3 outline-none transition-all shadow-sm"
                                    placeholder="VD: Long Thành, Đồng Nai">
                            </div>
                            <p id="edit_supplier_address_error" class="text-red-500 text-xs mt-1.5 {{ old('form_type') == 'edit_supplier' && $errors->has('address') ? '' : 'hidden' }}">{{ old('form_type') == 'edit_supplier' ? $errors->first('address') : '' }}</p>
                        </div>

                        <!-- Dòng 4: Chứng chỉ -->
                        <div>
                            <label for="edit_supplier_certificate" class="block text-sm font-bold text-gray-700 mb-2">Chứng chỉ</label>
                            <input type="text" id="edit_supplier_certificate" name="certificate"
                                class="w-full bg-white border border-gray-300 text-gray-800 text-base rounded-xl focus:ring-2 focus:ring-forest-500 focus:border-forest-500 block px-4 py-3 outline-none transition-all shadow-sm"
                                placeholder="VD: VietGAP, GlobalGAP, ISO 22000 (cách nhau bằng dấu phẩy)">
                            <p class="text-[11px] text-gray-400 mt-1">Nhập nhiều chứng chỉ cách nhau bằng dấu phẩy.</p>
                        </div>

                        <!-- Dòng 5: Trạng thái -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Trạng thái hợp tác</label>
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="is_active" value="1" id="edit_supplier_active_1"
                                        class="w-4 h-4 text-forest-600 focus:ring-forest-500 border-gray-300">
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-forest-700">
                                        <i class="fa-solid fa-circle-check text-green-500 mr-1"></i> Đang hợp tác
                                    </span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="is_active" value="0" id="edit_supplier_active_0"
                                        class="w-4 h-4 text-gray-600 focus:ring-gray-500 border-gray-300">
                                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-800">
                                        <i class="fa-solid fa-ban text-gray-400 mr-1"></i> Ngừng hợp tác
                                    </span>
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- ================= BUTTONS ================= -->
                    <div class="flex justify-end items-center gap-4 mt-8 pt-6 border-t border-gray-100">
                        <button type="button" onclick="closeEditSupplierModal()"
                            class="px-6 py-3 bg-white text-gray-600 font-bold rounded-xl hover:bg-gray-100 transition-colors border border-transparent hover:border-gray-200">
                            Hủy
                        </button>
                        <button type="submit" id="edit_supplier_submit_btn"
                            class="px-8 py-3 bg-forest-700 text-white font-bold rounded-xl hover:bg-forest-800 shadow-lg shadow-forest-500/30 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> Cập Nhật
                        </button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

@if(old('form_type') == 'edit_supplier' && $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động mở lại modal Sửa nhà cung cấp khi có lỗi validation
        const supplierId = "{{ old('supplier_id') }}";
        document.getElementById('editSupplierForm').action = '/admin/suppliers/' + supplierId;
        document.getElementById('edit_supplier_id').value = supplierId;
        document.getElementById('edit_supplier_hidden_id').value = supplierId;

        // Khôi phục giá trị cũ
        document.getElementById('edit_supplier_name').value = "{{ old('name') }}";
        document.getElementById('edit_supplier_contact_name').value = "{{ old('contact_name') }}";
        document.getElementById('edit_supplier_phone').value = "{{ old('phone') }}";
        document.getElementById('edit_supplier_address').value = "{{ old('address') }}";
        document.getElementById('edit_supplier_certificate').value = "{{ old('certificate') }}";

        // Khôi phục trạng thái radio
        const isActive = "{{ old('is_active', '1') }}";
        const radioEl = document.querySelector('#editSupplierForm input[name="is_active"][value="' + isActive + '"]');
        if (radioEl) radioEl.checked = true;

        // Mở modal
        const modal = document.getElementById('editSupplierModal');
        const content = document.getElementById('editSupplierContent');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    });
</script>
@endif
