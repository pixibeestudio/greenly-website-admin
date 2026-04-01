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
        // Chuyển đơn hàng có trạng thái cũ 'confirmed' sang 'processing'
        DB::table('orders')->where('order_status', 'confirmed')->update(['order_status' => 'processing']);

        // Chuyển đơn hàng có trạng thái cũ 'completed' sang 'delivered'
        DB::table('orders')->where('order_status', 'completed')->update(['order_status' => 'delivered']);

        // Cập nhật cột order_status thành ENUM 6 trạng thái mới
        DB::statement("ALTER TABLE `orders` MODIFY `order_status` ENUM('pending','processing','ready_for_pickup','shipping','delivered','cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục lại trạng thái cũ
        DB::table('orders')->where('order_status', 'delivered')->update(['order_status' => 'completed']);
        DB::table('orders')->where('order_status', 'ready_for_pickup')->update(['order_status' => 'processing']);

        DB::statement("ALTER TABLE `orders` MODIFY `order_status` VARCHAR(20) NOT NULL DEFAULT 'pending'");
    }
};
