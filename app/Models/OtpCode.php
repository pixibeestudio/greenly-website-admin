<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model đại diện cho một mã OTP đã được phát hành.
 *
 * Chính sách bảo mật:
 *  - Lưu HASH của OTP (không lưu plaintext)
 *  - Thời hạn sử dụng: 120 giây kể từ khi phát hành
 *  - Single-use: đã xác thực thành công => đánh dấu used_at
 *  - Tối đa 5 lần nhập sai => OTP bị vô hiệu (đánh dấu như đã dùng)
 */
class OtpCode extends Model
{
    protected $fillable = [
        'email',
        'code_hash',
        'type',
        'attempts',
        'expires_at',
        'used_at',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public const TYPE_LOGIN          = 'login';
    public const TYPE_RESET_PASSWORD = 'reset_password';

    public const MAX_ATTEMPTS = 5;

    /** Kiểm tra OTP còn hiệu lực (chưa hết hạn + chưa dùng + chưa vượt số lần sai) */
    public function isValid(): bool
    {
        return $this->used_at === null
            && $this->attempts < self::MAX_ATTEMPTS
            && $this->expires_at->isFuture();
    }
}
