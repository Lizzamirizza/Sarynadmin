<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'amount',
        'snap_token',
        'status',               // pending, paid, failed, expired, dll
        'payment_date',         // timestamp pembayaran sukses
        'midtrans_transaction_id',
        'midtrans_status',      // status dari Midtrans (capture, settlement, etc)
    ];

    /**
     * Relasi ke order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
