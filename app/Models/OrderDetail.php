<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'batch_id',
        'quantity',
        'price',
    ];

    // Quan hệ: Chi tiết đơn hàng thuộc về 1 đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ: Chi tiết đơn hàng thuộc về 1 sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Quan hệ: Chi tiết đơn hàng thuộc về 1 lô hàng (nullable)
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // Quan hệ: 1 order_detail tương ứng với 1 review (nếu đã đánh giá)
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
