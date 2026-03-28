<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    // Quan hệ: Giỏ hàng thuộc về 1 sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ: Giỏ hàng thuộc về 1 người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
