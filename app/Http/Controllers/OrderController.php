<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with(['orderDetails.product', 'payments'])->get();
        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['orderDetails.product', 'payments']);
        return response()->json($order);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        if ($request->status === 'paid' && $order->payments) {
            $order->payments->status = 'paid';
            $order->payments->payments_date = now();
            $order->payments->save();
        }

        $order->load(['orderDetails.product', 'payments']);

        return response()->json([
            'message' => 'Order status updated',
            'order' => $order
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->user_id = $user->id;
            $order->total_price = $request->total;
            $order->status = 'pending';
            $order->save();

            $order->order_number = 'ORDER-' . str_pad($order->id, 8, '0', STR_PAD_LEFT);
            $order->save();

            foreach ($request->items as $item) {
                $product = \App\Models\Product::find($item['id']);

                $order->orderDetails()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            DB::commit();

            $order->load(['orderDetails.product', 'payments']);

            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal membuat order', 'message' => $e->getMessage()], 500);
        }
    }

    // Tambahkan method cancelOrder
    public function cancelOrder(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak punya akses membatalkan order ini'], 403);
        }

        try {
            DB::beginTransaction();

            // Hapus snap token terkait (misal ada model SnapToken terkait order)
            // Asumsi kamu punya relasi snapToken di model Order, jika tidak bisa sesuaikan
            if (method_exists($order, 'snapToken')) {
                $order->snapToken()->delete();
            } else {
                // Kalau tidak ada model relasi, hapus langsung di DB
                DB::table('snap_tokens')->where('order_id', $order->id)->delete();
            }

            // Hapus detail order
            $order->orderDetails()->delete();

            // Hapus pembayaran (jika ada)
            $order->payments()->delete();

            // Hapus order utama
            $order->delete();

            DB::commit();

            return response()->json(['message' => 'Order berhasil dibatalkan dan dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membatalkan order', 'error' => $e->getMessage()], 500);
        }
    }
}
