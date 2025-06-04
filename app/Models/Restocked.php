<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restocked extends Model
{
    use HasFactory;

    // Tambahkan 'price' ke dalam array $fillable
    protected $fillable = [
        'product_id',
        'admin_id',
        'quantity',
        'price',
        'restocked_at',
        'notes',
        'image', 
         
    ];

    protected $dates = ['restocked_at'];

    // Relasi ke model Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke model Admin
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

   
    public function getStatusAttribute(): string
    {
        return $this->quantity > 0 ? 'restocked' : 'soon';
    }

    

}
