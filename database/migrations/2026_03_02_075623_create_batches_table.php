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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('batch_code', 50)->comment('Mã lô hàng');
            $table->decimal('import_price', 10, 2)->default(0.00);
            $table->integer('quantity')->default(0)->comment('Số lượng nhập ban đầu');
            $table->integer('current_quantity')->default(0)->comment('Số lượng còn lại trong kho');
            $table->date('manufacturing_date')->comment('Ngày sản xuất');
            $table->date('expiry_date')->comment('Ngày hết hạn');
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
