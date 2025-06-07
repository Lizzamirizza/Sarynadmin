<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // import trait yang benar

class OrderController extends Controller
{
    use AuthorizesRequests; // gunakan trait di dalam class

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->get();
        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);
        return response()->json($order->load('items.product'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated']);
    }
}
