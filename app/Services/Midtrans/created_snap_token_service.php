<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;
use App\Models\Order;

class CreateSnapTokenService
{
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;

        // tarik dari config/midtrans.php
        \Midtrans\Config::$serverKey     = config('midtrans.server_key');
        \Midtrans\Config::$isProduction  = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized   = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds         = config('midtrans.is_3ds');
    }

    /**
     * Kembalikan snap_token – disimpan di DB agar reuse
     */
    public function getSnapToken(): string
    {
        // kalau sudah pernah dibuat, gunakan kembali
        if ($this->order->snap_token) {
            return $this->order->snap_token;
        }

        // ---------- 1. transaction_details ----------
        $transactionDetails = [
            'order_id'     => $this->order->number,          // contoh: ORD-20250606-123
            'gross_amount' => (int) $this->order->total_price, // Midtrans butuh integer
        ];

        // ---------- 2. item_details ----------
        $itemDetails = $this->order->items->map(function ($item) {
            return [
                'id'       => 'product-' . $item->product_id,
                'price'    => (int) $item->price,
                'quantity' => $item->quantity,
                'name'     => $item->name,
            ];
        })->toArray();

        // kalau tidak punya tabel order_items, kirim 1 baris ringkasan
        if (empty($itemDetails)) {
            $itemDetails[] = [
                'id'       => 'order-' . $this->order->id,
                'price'    => (int) $this->order->total_price,
                'quantity' => 1,
                'name'     => 'Pesanan #' . $this->order->number,
            ];
        }

        // ---------- 3. customer_details ----------
        $customer = $this->order->user; // relasi ke users
        $customerDetails = [
            'first_name' => $customer->name,
            'email'      => $customer->email,
            'phone'      => $customer->phone ?? '081234567890',
        ];

        // ---------- 4. gabungkan & panggil Snap ----------
        $params = [
            'transaction_details' => $transactionDetails,
            'item_details'        => $itemDetails,
            'customer_details'    => $customerDetails,
        ];

        $snapToken = Snap::getSnapToken($params);

        // simpan di DB supaya tidak generate ulang
        $this->order->snap_token = $snapToken;
        $this->order->save();

        return $snapToken;
    }
}
