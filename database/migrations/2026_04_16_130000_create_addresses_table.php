<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('receiver_name');
            $table->string('phone', 15);
            $table->string('province');       // Tỉnh / Thành phố
            $table->string('district');       // Quận / Huyện
            $table->string('ward');           // Phường / Xã
            $table->string('street');         // Tên đường, Tòa nhà
            $table->string('house_number');   // Số nhà cụ thể
            $table->enum('label', ['home', 'office', 'other'])->default('home');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
