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
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['manufacturing_date', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->date('manufacturing_date')->nullable()->comment('Ngày sản xuất');
            $table->date('expiry_date')->nullable()->comment('Ngày hết hạn');
        });
    }
};
