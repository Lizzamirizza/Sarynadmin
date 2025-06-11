<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;

class CartController extends Controller
{
    // Tampilkan semua item di cart user
    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        return response()->json([
    'cart' => $cartItems->map(function ($item) {
        return [
            'id' => $item->id, // ID dari cartItem, bukan produk
            'product' => $item->product,
            'quantity' => $item->quantity,
        ];
    }),
]);
    }

    // Tambah item ke cart (jika sudah ada produk sama, update quantity)
    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $user->cartItems()->where('product_id', $request->product_id)->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Buat cart item baru
            $user->cartItems()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Item berhasil ditambahkan ke cart']);
    }

    // Update quantity item cart
    public function update(Request $request, CartItem $cartItem)
    {
        $user = $request->user();

        if ($cartItem->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'Cart item berhasil diupdate']);
    }

    // Hapus item dari cart
    public function destroy(Request $request, CartItem $cartItem)
    {
        $user = $request->user();

        if ($cartItem->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item berhasil dihapus']);
    }

    // Checkout: buat order dari cart, kosongkan cart
    public function checkout(Request $request)
    {
        $user = $request->user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $order = new Order();
        $order->user_id = $user->id;
        $order->status = 'pending';
        $order->total_price = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        $order->save();

        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        $user->cartItems()->delete();

        return response()->json([
            'message' => 'Checkout berhasil',
            'order_id' => $order->id,
        ]);
    }

    public function clear(Request $request)
    {
        $user = $request->user();
        $user->cart()->delete();

        return response()->json(['message' => 'Cart cleared successfully.']);
    }

}
