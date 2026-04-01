<div class="flex items-center justify-center gap-1.5">
    {{-- Nút 1: Xem chi tiết (Luôn hiện) --}}
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
            class="w-8 h-8 rounded-full bg-forest-50 text-forest-600 hover:bg-forest-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xem chi tiết">
        <i class="fa-solid fa-eye text-xs"></i>
    </button>

    {{-- Nút 2: Xử lý đơn (Chỉ hiện khi pending → chuyển sang processing) --}}
    @if($order->order_status === 'pending')
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="inline">
            @csrf
            @method('PUT')
            <input type="hidden" name="order_status" value="processing">
            <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
            <button type="submit"
                    class="w-8 h-8 rounded-full bg-cyan-50 text-cyan-600 hover:bg-cyan-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Xử lý đơn">
                <i class="fa-solid fa-box-open text-xs"></i>
            </button>
        </form>
    @endif

    {{-- Nút 3: Gán Shipper (Chỉ hiện khi processing) --}}
    @if($order->order_status === 'processing')
        <button onclick="openAssignShipperModal({{ $order->id }})"
                class="w-8 h-8 rounded-full bg-green-50 text-green-500 hover:bg-green-600 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Gán Shipper">
            <i class="fa-solid fa-truck-fast text-xs"></i>
        </button>
    @endif

    {{-- Nút 4: Hủy đơn (Chỉ hiện khi pending, processing, ready_for_pickup) --}}
    @if(in_array($order->order_status, ['pending', 'processing', 'ready_for_pickup']))
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="inline"
              onsubmit="return confirm('Bạn có chắc muốn hủy đơn {{ $order->order_code }}?')">
            @csrf
            @method('PUT')
            <input type="hidden" name="order_status" value="cancelled">
            <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
            <button type="submit"
                    class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center" title="Hủy đơn">
                <i class="fa-solid fa-ban text-xs"></i>
            </button>
        </form>
    @endif
</div>
