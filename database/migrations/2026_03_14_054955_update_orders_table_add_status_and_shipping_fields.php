<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Dùng raw SQL để rename + thay đổi kiểu enum → string cho cột status
        DB::statement("ALTER TABLE `orders` CHANGE `status` `order_status` VARCHAR(20) NOT NULL DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            // Thêm cột trạng thái thanh toán
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending')->after('payment_method');

            // Thêm cột thông tin giao hàng
            $table->string('shipping_name', 100)->nullable()->after('shipping_address');
            $table->string('shipping_phone', 20)->nullable()->after('shipping_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'shipping_name', 'shipping_phone']);
        });

        DB::statement("ALTER TABLE `orders` CHANGE `order_status` `status` ENUM('pending','shipping','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
