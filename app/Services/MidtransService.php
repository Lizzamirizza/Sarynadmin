<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }


    public function createTransactionFromPayment($payment)
    {
        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $payment->order_id,
                'gross_amount' => $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $payment->customer_name,
                'email' => $payment->customer_email,
                'phone' => $payment->customer_phone,
            ],
            'enabled_payments' => ['credit_card', 'bank_transfer', 'gopay'],
            'vtweb' => [],
        ];

        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }

    /**
     * Batalkan transaksi Midtrans berdasarkan order id
     */
    public function cancelTransaction($orderId)
    {
        try {
            // Order id di Midtrans biasanya sama dengan yang dikirim saat create transaction
            $midtransOrderId = 'ORDER-' . $orderId;

            $response = Transaction::cancel($midtransOrderId);

            return $response;
        } catch (\Exception $e) {
            // Log error jika gagal batalkan transaksi Midtrans
            \Log::error('Gagal batalkan transaksi Midtrans: ' . $e->getMessage());
            return false;
        }
    }
}
