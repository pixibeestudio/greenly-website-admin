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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shipper_id')->nullable()->comment('Mã nhân viên giao hàng');
            $table->decimal('total_money', 12, 2)->default(0.00);
            $table->decimal('shipping_fee', 10, 2)->default(0.00)->comment('Phí vận chuyển');
            $table->string('payment_method', 50)->default('COD');
            $table->string('payment_receipt', 255)->nullable()->comment('Đường dẫn file ảnh hóa đơn');
            $table->enum('status', ['pending', 'shipping', 'completed', 'cancelled'])->default('pending');
            $table->text('shipping_address');
            $table->text('note')->nullable();
            $table->dateTime('delivery_date')->nullable()->comment('Thời gian giao hàng thành công');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shipper_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
