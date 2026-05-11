<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng thông báo cho người dùng.
     * Mỗi thông báo liên kết với 1 đơn hàng và ghi nhận loại trạng thái.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('Người nhận thông báo');
            $table->unsignedBigInteger('order_id')->comment('Đơn hàng liên quan');
            $table->string('type', 50)->comment('Loại thông báo = trạng thái đơn: pending, processing, ...');
            $table->string('title', 255)->comment('Tiêu đề thông báo');
            $table->text('message')->comment('Nội dung chi tiết');
            $table->boolean('is_read')->default(false)->comment('Đã đọc hay chưa');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // Index để query nhanh theo user + sắp xếp mới nhất
            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
