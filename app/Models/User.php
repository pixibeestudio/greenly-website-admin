<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'status',
        'work_status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mask email để hiển thị an toàn: nantibiame@gmail.com -> nan****@gmail.com
     * Dùng trong bảng danh sách để bảo mật thông tin người dùng.
     */
    public function getMaskedEmailAttribute(): string
    {
        if (empty($this->email) || !str_contains($this->email, '@')) {
            return $this->email ?? '';
        }

        [$local, $domain] = explode('@', $this->email, 2);
        // Lấy tối đa 3 ký tự đầu của phần local; nếu ngắn hơn thì giữ nguyên
        $visibleLen = min(3, max(1, mb_strlen($local) - 1));
        $visible    = mb_substr($local, 0, $visibleLen);

        return $visible . '****@' . $domain;
    }

    /**
     * Mask số điện thoại: 0896720844 -> *******844 (chỉ lộ 3 số cuối)
     */
    public function getMaskedPhoneAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }

        $len = strlen($this->phone);
        if ($len <= 3) {
            return $this->phone;
        }

        return str_repeat('*', $len - 3) . substr($this->phone, -3);
    }
}
