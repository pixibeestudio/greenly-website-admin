<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shipper_id',
        'total_money',
        'shipping_fee',
        'payment_method',
        'payment_receipt',
        'payment_status',
        'order_status',
        'shipping_address',
        'shipping_name',
        'shipping_phone',
        'note',
        'delivery_date',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
    ];

    // Quan hệ: Đơn hàng thuộc về 1 người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: Đơn hàng có nhiều chi tiết đơn hàng
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Quan hệ: Đơn hàng thuộc về 1 shipper (nullable)
    public function shipper()
    {
        return $this->belongsTo(User::class, 'shipper_id');
    }

    // Accessor: Sinh mã đơn hàng hiển thị giao diện
    public function getOrderCodeAttribute()
    {
        $dateString = $this->created_at->format('ymd');
        $idString = str_pad($this->id, 3, '0', STR_PAD_LEFT);
        return '#ORD-' . $dateString . '-' . $idString;
    }

    // Accessor: Tổng tiền cuối cùng (tổng tiền hàng + phí ship)
    public function getFinalAmountAttribute()
    {
        return $this->total_money + $this->shipping_fee;
    }
}
