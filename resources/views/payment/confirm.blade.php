<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận thanh toán - Greenly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .pulse-dot {
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-green-700">Greenly</span>
            </div>
            <p class="text-gray-500 text-sm mt-1">Thanh toán đơn hàng</p>
        </div>

        {{-- Card chính --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- Header card --}}
            <div class="bg-gradient-to-r from-green-600 to-green-500 px-6 py-5">
                <p class="text-green-100 text-sm">Mã đơn hàng</p>
                <p class="text-white text-xl font-bold tracking-wide mt-1">{{ $orderCode }}</p>
            </div>

            {{-- Nội dung --}}
            <div class="p-6">

                @if($alreadyPaid)
                    {{-- Trạng thái: ĐÃ THANH TOÁN --}}
                    <div class="text-center py-6">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Thanh toán thành công!</h2>
                        <p class="text-gray-500 mt-2">Đơn hàng đã được xác nhận thanh toán.</p>
                        <p class="text-gray-400 text-sm mt-1">Bạn có thể đóng trang này.</p>
                    </div>
                @else
                    {{-- Thông tin đơn hàng --}}
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">Tổng thanh toán</span>
                            <span class="text-2xl font-bold text-orange-500">{{ number_format($grandTotal, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">Phương thức</span>
                            <span class="text-sm font-medium text-gray-700">Chuyển khoản ngân hàng</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">Trạng thái</span>
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-yellow-600">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full pulse-dot"></span>
                                Chờ thanh toán
                            </span>
                        </div>
                    </div>

                    {{-- Nút xác nhận --}}
                    <button id="btnConfirm"
                        onclick="confirmPayment()"
                        class="w-full bg-green-600 hover:bg-green-700 active:bg-green-800 text-white font-semibold py-4 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg text-base">
                        Xác nhận thanh toán
                    </button>

                    {{-- Loading state (ẩn mặc định) --}}
                    <div id="loadingState" class="hidden text-center py-4">
                        <svg class="animate-spin h-8 w-8 text-green-600 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-gray-500 mt-2 text-sm">Đang xử lý...</p>
                    </div>

                    {{-- Kết quả thành công (ẩn mặc định) --}}
                    <div id="successState" class="hidden text-center py-6">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Thanh toán thành công!</h2>
                        <p class="text-gray-500 mt-2">Ứng dụng sẽ tự động cập nhật.</p>
                        <p class="text-gray-400 text-sm mt-1">Bạn có thể đóng trang này.</p>
                    </div>

                    {{-- Lỗi (ẩn mặc định) --}}
                    <div id="errorState" class="hidden text-center py-4">
                        <p class="text-red-500 text-sm font-medium" id="errorMessage"></p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-400 text-xs mt-6">&copy; 2026 Greenly Store. Môi trường thử nghiệm.</p>
    </div>

    <script>
        function confirmPayment() {
            const btn = document.getElementById('btnConfirm');
            const loading = document.getElementById('loadingState');
            const success = document.getElementById('successState');
            const error = document.getElementById('errorState');

            // Ẩn nút, hiện loading
            btn.classList.add('hidden');
            loading.classList.remove('hidden');
            error.classList.add('hidden');

            fetch("{{ route('payment.confirm', ['orderId' => $order->id, 'token' => $token]) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => response.json())
            .then(data => {
                loading.classList.add('hidden');
                success.classList.remove('hidden');
            })
            .catch(err => {
                loading.classList.add('hidden');
                btn.classList.remove('hidden');
                error.classList.remove('hidden');
                document.getElementById('errorMessage').textContent = 'Có lỗi xảy ra, vui lòng thử lại.';
            });
        }
    </script>

</body>
</html>
