<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);}

        // Hitung total dan persiapkan item_details untuk Midtrans
        $total = 0;
        $itemDetails = [];

        foreach ($cartItems as $item) {
            $itemDetails[] = [
                'id' => $item->product_id,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
                'name' => $item->product->name,
            ];

            $total += $item->product->price * $item->quantity;
        }

        // Buat order ke database
        $order = new Order();
        $order->user_id = $user->id;
        $order->number = 'ORD-' . now()->format('Ymd') . '-' . Str::random(5);
        $order->status = 'pending';
        $order->total_price = $total;
        $order->save();

        // Masukkan order item
        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        // Kosongkan cart
        $user->cartItems()->delete();

        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Buat payload Snap
        $payload = [
            'transaction_details' => [
                'order_id' => $order->number,
                'gross_amount' => $total,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => $itemDetails,
        ];

        // Ambil Snap Token
        $snapToken = Snap::getSnapToken($payload);

        // Simpan snap_token di order (optional)
        $order->snap_token = $snapToken;
        $order->save();

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $order->id,
        ]);
    }
}
