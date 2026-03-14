<div class="flex items-center justify-center gap-2">
    <button onclick="openShowOrderModal(this)"
            data-order="{{ json_encode([
                'id' => $order->id,
                'order_code' => $order->order_code,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'payment_method_label' => match($order->payment_method) {
                    'COD' => 'COD (Tiền mặt)',
                    'bank_transfer' => 'Chuyển khoản ngân hàng',
                    'vnpay' => 'VNPAY',
                    'momo' => 'Ví MoMo',
                    default => $order->payment_method,
                },
                'total_money' => $order->total_money,
                'shipping_fee' => $order->shipping_fee,
                'final_amount' => $order->final_amount,
                'shipping_name' => $order->shipping_name ?? ($order->user->name ?? 'N/A'),
                'shipping_phone' => $order->shipping_phone ?? 'N/A',
                'shipping_address' => $order->shipping_address,
                'note' => $order->note,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'order_details' => $order->orderDetails->map(fn($d) => [
                    'product_name' => $d->product->name ?? 'SP không xác định',
                    'product_image' => $d->product && $d->product->image ? asset('storage/' . $d->product->image) : null,
                    'quantity' => $d->quantity,
                    'price' => $d->price,
                ])->toArray(),
            ]) }}"
            class="w-8 h-8 rounded-full bg-forest-50 text-forest-600 hover:bg-forest-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xem & Xử lý">
        <i class="fa-solid fa-eye text-xs"></i>
    </button>
    <button onclick="window.print()"
            class="w-8 h-8 rounded-full text-gray-400 hover:bg-gray-100 transition-all flex items-center justify-center" title="In hóa đơn">
        <i class="fa-solid fa-print text-xs"></i>
    </button>
</div>
