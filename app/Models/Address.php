<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver_name',
        'phone',
        'province',
        'district',
        'ward',
        'street',
        'house_number',
        'label',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Accessor: Tạo chuỗi địa chỉ đầy đủ
    public function getFullAddressAttribute()
    {
        return $this->house_number . ', ' . $this->street . ', ' . $this->ward . ', ' . $this->district . ', ' . $this->province;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
