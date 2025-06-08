<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
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

    // PaymentController.php
    public function getByOrder($orderId)
    {
        $payment = Payment::where('order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json($payment);
    }

}
