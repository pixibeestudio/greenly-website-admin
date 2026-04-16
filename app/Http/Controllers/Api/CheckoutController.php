<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use App\Models\Batch;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // Đặt hàng từ giỏ hàng (yêu cầu đăng nhập)
    public function checkout(Request $request)
    {
        // Bước 1: Validate dữ liệu đầu vào
        $request->validate([
            'shipping_name'    => 'required|string|max:255',
            'shipping_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'payment_method'   => 'required|in:COD,banking',
            'shipping_fee'     => 'required|numeric|min:0',
            'note'             => 'nullable|string|max:500',
        ]);

        $userId = auth()->id();

        // Bước 2: Kiểm tra giỏ hàng có sản phẩm không
        $cartItems = Cart::where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng đang trống, không thể đặt hàng!',
            ], 400);
        }

        // Bước 3: Bắt đầu Transaction
        DB::beginTransaction();

        try {
            // Bước 4: Tính tổng tiền hàng (sub_total)
            $subTotal = 0;
            $orderDetailsData = [];

            foreach ($cartItems as $item) {
                $product = Product::find($item->product_id);

                if (!$product) {
                    throw new \Exception("Sản phẩm ID #{$item->product_id} không tồn tại!");
                }

                // Lấy giá bán: ưu tiên giá giảm, nếu không có thì lấy giá gốc
                $price = ($product->discount_price && $product->discount_price > 0)
                    ? $product->discount_price
                    : $product->price;

                $lineTotal = $price * $item->quantity;
                $subTotal += $lineTotal;

                $orderDetailsData[] = [
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $price,
                ];
            }

            $shippingFee = (float) $request->shipping_fee;
            $grandTotal = $subTotal + $shippingFee;

            // Bước 5: Tạo đơn hàng (total_money = tiền hàng, shipping_fee riêng)
            $order = Order::create([
                'user_id'          => $userId,
                'total_money'      => $subTotal,
                'shipping_fee'     => $shippingFee,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'pending',
                'order_status'     => 'pending',
                'shipping_name'    => $request->shipping_name,
                'shipping_address' => $request->shipping_address,
                'shipping_phone'   => $request->shipping_phone,
                'note'             => $request->note,
            ]);

            // Bước 6: Lưu chi tiết đơn hàng + trừ kho theo lô (FIFO - lô cũ nhất trước)
            foreach ($orderDetailsData as &$detailData) {
                $remainingQty = $detailData['quantity'];

                // Lấy các lô hàng còn tồn kho, sắp xếp theo lô cũ nhất (FIFO)
                $batches = Batch::where('product_id', $detailData['product_id'])
                    ->where('current_quantity', '>', 0)
                    ->orderBy('id', 'asc')
                    ->get();

                // Kiểm tra tổng tồn kho có đủ không
                $totalStock = $batches->sum('current_quantity');
                if ($totalStock < $remainingQty) {
                    $product = Product::find($detailData['product_id']);
                    throw new \Exception(
                        "Sản phẩm \"{$product->name}\" không đủ tồn kho! (Cần: {$remainingQty}, Còn: {$totalStock})"
                    );
                }

                // Trừ kho theo từng lô (FIFO)
                $firstBatchId = null;
                foreach ($batches as $batch) {
                    if ($remainingQty <= 0) break;

                    if (!$firstBatchId) {
                        $firstBatchId = $batch->id;
                    }

                    $deduct = min($batch->current_quantity, $remainingQty);
                    $batch->current_quantity -= $deduct;
                    $batch->save();

                    $remainingQty -= $deduct;
                }

                // Lưu chi tiết đơn hàng (gắn batch_id của lô đầu tiên)
                OrderDetail::create([
                    'order_id'   => $order->id,
                    'product_id' => $detailData['product_id'],
                    'batch_id'   => $firstBatchId,
                    'quantity'   => $detailData['quantity'],
                    'price'      => $detailData['price'],
                ]);
            }

            // Bước 7: Xóa toàn bộ giỏ hàng
            Cart::where('user_id', $userId)->delete();

            // Bước 8: Commit transaction
            DB::commit();

            $responseData = [
                'order_id'     => $order->id,
                'order_code'   => $order->order_code,
                'total_money'  => (int) ($order->total_money + $order->shipping_fee),
                'order_status' => $order->order_status,
            ];

            // Nếu thanh toán bằng chuyển khoản, trả thêm payment_url cho app tạo QR
            if ($request->payment_method === 'banking') {
                $responseData['payment_url'] = PaymentController::generatePaymentUrl($order->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data'    => $responseData,
            ], 200);

        } catch (\Exception $e) {
            // Rollback nếu có lỗi
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Đặt hàng thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API xác nhận thanh toán (khi người dùng đã chuyển khoản)
     */
    public function confirmPayment(Request $request, $id)
    {
        $userId = auth()->id();

        // Tìm đơn hàng theo ID và kiểm tra quyền sở hữu
        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json([
                'status'  => 404,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        // Kiểm tra nếu đơn hàng đã thanh toán rồi
        if ($order->payment_status === 'completed') {
            return response()->json([
                'status'  => 400,
                'message' => 'Đơn hàng đã được thanh toán trước đó',
            ], 400);
        }

        // Cập nhật trạng thái thanh toán thành 'completed' (theo enum trong DB)
        $order->update(['payment_status' => 'completed']);

        return response()->json([
            'status'  => 200,
            'message' => 'Xác nhận thanh toán thành công',
        ], 200);
    }

    /**
     * API kiểm tra trạng thái thanh toán (app polling mỗi 3 giây)
     * GET /api/payment/status/{id}
     */
    public function checkPaymentStatus($id)
    {
        $userId = auth()->id();

        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        return response()->json([
            'success'        => true,
            'payment_status' => $order->payment_status,
        ], 200);
    }
}
