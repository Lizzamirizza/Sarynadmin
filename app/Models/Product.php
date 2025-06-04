<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'size',
        'stock',
        'price',
        'image',
        'category_id',
        'admin_id',
    ];

    /**
     * Cast image as array for multi-upload
     */
    protected $casts = [
        'image' => 'array',
    ];
    
    /**
     * Relasi ke kategori produk
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke admin pembuat produk
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Relasi ke detail pesanan
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
