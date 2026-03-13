<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 
        'name', 
        'slug', 
        'image', 
        'price', 
        'discount_price', 
        'unit', 
        'description', 
        'origin', 
        'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    // Accessor: Tổng số lượng tồn kho từ tất cả lô hàng
    public function getTotalStockAttribute()
    {
        return $this->batches->sum('current_quantity');
    }
}
