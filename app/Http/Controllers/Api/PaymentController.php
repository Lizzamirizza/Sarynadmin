<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function show($id)
    {
        $payment = Payment::with('order')->findOrFail($id);
        return response()->json($payment);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string',
            'amount' => 'required|numeric',
            'status' => 'required|string',
        ]);

        $payment = Payment::create($request->all());

        return response()->json($payment, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'payment_date' => 'nullable|date',
            'midtrans_status' => 'nullable|string',
            'midtrans_transaction_id' => 'nullable|string',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update($request->only(['status', 'payment_date', 'midtrans_status', 'midtrans_transaction_id']));

        return response()->json($payment);
    }

    public function getByOrder($orderId)
    {
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json($payment);
    }

    /**
     * ✅ Generate Midtrans Snap Token
     */
    public function getSnapToken(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . now()->timestamp,
                'gross_amount' => $order->total_price ?? 10000, // atau amount dari relasi
            ],
            'customer_details' => [
                'first_name' => $order->customer_name ?? 'Customer',
                'email' => $order->customer_email ?? 'email@example.com',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return response()->json(['token' => $snapToken]);
    }

    /**
     * ✅ Terima Notifikasi dari Midtrans
     */
    public function handleNotification(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;
        $paymentDate = now();

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Cari data payment berdasarkan order_id
        $payment = Payment::where('order_id', explode('-', $orderId)[1] ?? null)->first();

        if ($payment) {
            $payment->update([
                'status' => $transactionStatus,
                'payment_date' => $paymentDate,
                'midtrans_status' => $transactionStatus,
                'midtrans_transaction_id' => $transactionId,
            ]);
        }

        return response()->json(['message' => 'Notification handled']);
    }
}
