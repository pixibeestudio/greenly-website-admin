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
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('shipper_rating')->nullable()->after('delivery_date')->comment('Đánh giá shipper: 1-5 sao');
            $table->text('shipper_review')->nullable()->after('shipper_rating')->comment('Nhận xét của khách về shipper');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipper_rating', 'shipper_review']);
        });
    }
};
