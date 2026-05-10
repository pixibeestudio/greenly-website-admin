<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('momo_transactions', function (Blueprint $table) {
            $table->id();

            // Liên kết với đơn hàng
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();

            // Định danh giao dịch phía Greenly (gửi cho MoMo làm orderId)
            // Format: GREENLY_<order_id>_<timestamp>
            $table->string('momo_order_id', 100)->unique()->comment('orderId gửi cho MoMo');
            $table->string('momo_request_id', 100)->comment('requestId gửi cho MoMo');

            // Loại thanh toán: 'app' (mở app MoMo qua deeplink) hoặc 'qr' (quét mã QR)
            $table->enum('payment_type', ['app', 'qr']);

            // Số tiền giao dịch (đồng)
            $table->unsignedBigInteger('amount');

            // URL trả về từ MoMo
            $table->text('pay_url')->nullable()->comment('URL web/deeplink mở app MoMo');
            $table->text('qr_code_url')->nullable()->comment('URL ảnh QR (khi type=qr)');
            $table->text('deeplink')->nullable()->comment('Deeplink momo:// mở app MoMo');

            // Mã giao dịch nội bộ của MoMo (chỉ có sau khi thanh toán thành công)
            $table->string('momo_trans_id', 100)->nullable();

            // Trạng thái giao dịch
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');

            // Mã kết quả & thông điệp từ MoMo (resultCode = 0 nghĩa là thành công)
            $table->integer('result_code')->nullable();
            $table->string('message', 500)->nullable();

            // Lưu raw response & IPN để audit/debug
            $table->json('raw_response')->nullable()->comment('Response từ MoMo khi tạo giao dịch');
            $table->json('raw_ipn')->nullable()->comment('Body IPN MoMo gửi về');

            // Cờ đánh dấu IPN đã được xác thực chữ ký HMAC chưa
            $table->boolean('signature_verified')->default(false);

            // Thời điểm hoàn tất giao dịch
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Index hỗ trợ truy vấn nhanh
            $table->index('order_id');
            $table->index('status');
            $table->index('momo_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('momo_transactions');
    }
};
