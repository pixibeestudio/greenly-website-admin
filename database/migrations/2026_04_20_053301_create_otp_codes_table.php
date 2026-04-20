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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->index();
            // Lưu HASH của OTP (không lưu plaintext) để chống lộ dữ liệu nếu DB bị rò rỉ
            $table->string('code_hash');
            // Loại OTP: 'login' (xác thực 2 lớp khi đăng nhập) | 'reset_password' (quên mật khẩu)
            $table->enum('type', ['login', 'reset_password'])->index();
            // Số lần nhập sai - quá 5 lần sẽ vô hiệu hóa OTP
            $table->unsignedTinyInteger('attempts')->default(0);
            // Thời điểm hết hạn (120 giây kể từ khi gửi)
            $table->timestamp('expires_at');
            // Đã dùng thành công chưa? (OTP chỉ dùng được 1 lần)
            $table->timestamp('used_at')->nullable();
            // IP người yêu cầu - phục vụ audit / chống spam
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['email', 'type', 'used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
