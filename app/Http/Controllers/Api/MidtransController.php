<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Midtrans\CreateSnapTokenService;

class MidtransController extends Controller
{
    public function getSnapToken($orderId)
    {
        $order = Order::with('user', 'items')->findOrFail($orderId);

        if (!$order->number) {
            $order->number = 'ORD-' . now()->format('Ymd') . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $order->save();
        }

        $snapToken = (new CreateSnapTokenService($order))->getSnapToken();

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $order->id,
        ]);
    }
}
