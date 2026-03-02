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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name', 255);
            $table->string('slug', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('discount_price', 10, 2)->default(0.00)->comment('Giá khuyến mãi (Lớn hơn 0 là có sale)');
            $table->string('unit', 50)->comment('Kg, Gram, Bó...');
            $table->longText('description')->nullable();
            $table->string('origin', 100)->nullable()->comment('Xuất xứ');
            $table->boolean('is_active')->default(1)->comment('1: Đang bán, 0: Ngừng bán');
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
