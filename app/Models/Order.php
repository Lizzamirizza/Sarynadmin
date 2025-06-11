<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'note',
        'order_number',  // tambahkan ini supaya bisa mass assign
    ];

    // Menambahkan event untuk otomatis menghasilkan order_number
    protected static function booted()
    {
        static::creating(function ($order) {
            // Ambil order_number terakhir yang ada
            $lastOrder = Order::orderBy('id', 'desc')->first();
            $lastOrderNumber = $lastOrder ? $lastOrder->order_number : 'ORDER-00000000';

            // Generate order_number berikutnya
            $order->order_number = 'ORDER-' . str_pad((intval(substr($lastOrderNumber, 6)) + 1), 8, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
