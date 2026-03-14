<!-- Modal Xem & Xử lý Đơn hàng -->
<div id="showOrderModal"
    class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center hidden opacity-0 transition-opacity duration-300">

    <!-- Modal Content -->
    <div id="showOrderContent"
        class="bg-gray-50 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300">

        <!-- Header Sticky -->
        <div class="sticky top-0 z-20 bg-white border-b border-gray-100 px-8 py-5 rounded-t-2xl flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-forest-800 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-green-600"></i> Chi Tiết Đơn Hàng
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">Xem thông tin và xử lý đơn hàng <span id="show_order_code" class="font-bold text-forest-700"></span></p>
            </div>
            <button type="button" onclick="closeShowOrderModal()"
                class="w-9 h-9 rounded-full bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition-colors border border-gray-200 hover:border-red-200">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-8 md:p-10">
            <div class="bg-white rounded-3xl shadow-lg shadow-gray-200/50 border border-gray-100 p-8 md:p-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- ================= CỘT TRÁI: THÔNG TIN ================= -->
                    <div class="flex flex-col gap-6">

                        <!-- Nhóm 1: Thông tin đơn hàng -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-receipt text-amber-500"></i> Thông tin đơn hàng
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Thời gian đặt</p>
                                    <p id="show_order_created_at" class="text-sm font-bold text-gray-700"></p>
                                </div>
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Trạng thái ĐH</p>
                                    <div id="show_order_status_badge"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Nhóm 2: Thông tin khách hàng -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-user text-blue-500"></i> Khách hàng & Giao hàng
                            </h3>
                            <div class="space-y-3">
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Người nhận</p>
                                    <p class="text-sm font-bold text-gray-700">
                                        <i class="fa-solid fa-user-tag text-forest-500 mr-1.5"></i>
                                        <span id="show_order_customer_name"></span>
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Số điện thoại</p>
                                    <p class="text-sm font-bold text-gray-700">
                                        <i class="fa-solid fa-phone text-green-500 mr-1.5"></i>
                                        <span id="show_order_customer_phone"></span>
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Địa chỉ giao hàng</p>
                                    <p id="show_order_customer_address" class="text-sm text-gray-700 leading-relaxed"></p>
                                </div>
                                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Ghi chú</p>
                                    <p id="show_order_note" class="text-sm text-gray-500 italic"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Nhóm 3: Thanh toán -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-wallet text-emerald-500"></i> Thanh toán
                            </h3>
                            <div class="bg-gray-50 rounded-xl px-4 py-4 border border-gray-100 space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Phương thức</span>
                                    <span id="show_order_payment_method" class="text-xs font-bold text-gray-700"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Trạng thái TT</span>
                                    <div id="show_order_payment_status_badge"></div>
                                </div>
                                <hr class="border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Tiền hàng</span>
                                    <span id="show_order_total_money" class="text-sm font-bold text-gray-700"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Phí vận chuyển</span>
                                    <span id="show_order_shipping_fee" class="text-sm font-bold text-gray-700"></span>
                                </div>
                                <hr class="border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-bold text-gray-700">Tổng cộng</span>
                                    <span id="show_order_final_amount" class="text-lg font-bold text-forest-700"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= CỘT PHẢI: MẶT HÀNG & HÀNH ĐỘNG ================= -->
                    <div class="flex flex-col gap-6">

                        <!-- Nhóm 4: Danh sách mặt hàng -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-cart-shopping text-orange-500"></i> Mặt hàng đã mua
                            </h3>
                            <div id="show_order_items" class="bg-gray-50 rounded-xl px-4 py-2 border border-gray-100 max-h-72 overflow-y-auto custom-scrollbar">
                                <!-- JS render danh sách mặt hàng vào đây -->
                            </div>
                        </div>

                        <!-- Nhóm 5: Form xử lý đơn hàng -->
                        <div>
                            <h3 class="text-sm font-bold text-forest-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-gears text-purple-500"></i> Xử lý đơn hàng
                            </h3>
                            <form id="updateOrderStatusForm" method="POST" action="">
                                @csrf
                                @method('PUT')
                                <div class="bg-gray-50 rounded-xl px-5 py-5 border border-gray-100 space-y-4">
                                    <!-- Trạng thái đơn hàng -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Trạng thái đơn hàng</label>
                                        <select id="select_order_status" name="order_status"
                                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all">
                                            <option value="pending">Chờ xác nhận</option>
                                            <option value="confirmed">Đã xác nhận</option>
                                            <option value="processing">Đang xử lý</option>
                                            <option value="shipping">Đang giao hàng</option>
                                            <option value="completed">Đã giao thành công</option>
                                            <option value="cancelled">Đã hủy</option>
                                        </select>
                                    </div>
                                    <!-- Trạng thái thanh toán -->
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Trạng thái thanh toán</label>
                                        <select id="select_payment_status" name="payment_status"
                                            class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 outline-none focus:border-forest-500 focus:ring-1 focus:ring-forest-500 transition-all">
                                            <option value="pending">Chưa thanh toán</option>
                                            <option value="completed">Đã thanh toán</option>
                                            <option value="failed">Thanh toán thất bại</option>
                                        </select>
                                    </div>
                                    <!-- Nút cập nhật -->
                                    <button type="submit" id="update_order_submit_btn"
                                        class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-xl shadow-lg shadow-forest-500/20 transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-check-circle"></i> Cập nhật đơn hàng
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="sticky bottom-0 bg-white border-t border-gray-100 px-8 py-4 rounded-b-2xl flex justify-end">
            <button type="button" onclick="closeShowOrderModal()"
                class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                Đóng
            </button>
        </div>
    </div>
</div>
