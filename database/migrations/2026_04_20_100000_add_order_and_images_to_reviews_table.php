<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột để liên kết review với order/order_detail + lưu ảnh đính kèm.
     * - order_detail_id: UNIQUE → đảm bảo mỗi sản phẩm trong 1 đơn chỉ được đánh giá 1 lần
     * - images: JSON chứa danh sách URL ảnh đánh giá
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('order_detail_id')->nullable()->unique()->after('order_id');
            $table->json('images')->nullable()->after('comment');

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_detail_id')->references('id')->on('order_details')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['order_detail_id']);
            $table->dropUnique(['order_detail_id']);
            $table->dropColumn(['order_id', 'order_detail_id', 'images']);
        });
    }
};
