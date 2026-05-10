<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model lưu lịch sử giao dịch MoMo.
 * Mỗi đơn hàng có thể có nhiều record (nếu user retry thanh toán),
 * nhưng chỉ 1 record có status='success'.
 */
class MomoTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'momo_order_id',
        'momo_request_id',
        'payment_type',
        'amount',
        'pay_url',
        'qr_code_url',
        'deeplink',
        'momo_trans_id',
        'status',
        'result_code',
        'message',
        'raw_response',
        'raw_ipn',
        'signature_verified',
        'completed_at',
    ];

    protected $casts = [
        'raw_response'       => 'array',
        'raw_ipn'            => 'array',
        'signature_verified' => 'boolean',
        'completed_at'       => 'datetime',
    ];

    // Quan hệ: 1 giao dịch thuộc 1 đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scope: lọc giao dịch theo trạng thái
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
