<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\MidtransService;

class MidtransController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    // Buat Snap Token
    public function createSnapToken(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'amount' => 'required|numeric|min:1',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
        ]);

        $payment = Payment::updateOrCreate(
            ['order_id' => $request->order_id],
            [
                'amount' => $request->amount,
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'status' => 'pending',
            ]
        );

        $snapToken = $this->midtrans->createTransactionFromPayment($payment);
        $payment->snap_token = $snapToken;
        $payment->save();

        $order = $payment->order;

        return response()->json([
            'snap_token' => $snapToken,
            'order_number' => $order->order_number ?? null,
            'payment_id' => $payment->id,
        ]);
    }

    // Callback Midtrans untuk update status pembayaran
    public function callback(Request $request)
    {
        $notif = new \Midtrans\Notification();

        $orderNumber = $notif->order_id;
        $transactionStatus = $notif->transaction_status;

        $payment = Payment::whereHas('order', function ($q) use ($orderNumber) {
            $q->where('order_number', $orderNumber);
        })->first();

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($transactionStatus == 'settlement') {
            $payment->status = 'paid';
            $payment->payment_date = now();
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $payment->status = 'failed';
        } elseif ($transactionStatus == 'pending') {
            $payment->status = 'pending';
        }

        $payment->midtrans_status = $transactionStatus;
        $payment->midtrans_transaction_id = $notif->transaction_id ?? null;
        $payment->save();

        return response()->json(['status' => 'ok']);
    }
}
