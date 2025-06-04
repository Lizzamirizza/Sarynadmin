<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restocked;
use Illuminate\Http\Request;

class RestockedController extends Controller
{
    // GET /api/restockeds
    public function index()
    {
        return Restocked::with(['product', 'admin'])->latest()->get();
    }

    // POST /api/restockeds
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'admin_id' => 'required|exists:admins,id',
            'quantity' => 'required|integer|min:0',
            'restocked_at' => 'required|date',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restockeds', 'public');
        }

        $restocked = Restocked::create($validated);
        return response()->json($restocked->load(['product', 'admin']), 201);
    }

    // GET /api/restockeds/{id}
    public function show($id)
    {
        $restocked = Restocked::with(['product', 'admin'])->findOrFail($id);
        return response()->json($restocked);
    }

    // PUT /api/restockeds/{id}
    public function update(Request $request, $id)
    {
        $restocked = Restocked::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'admin_id' => 'sometimes|exists:admins,id',
            'quantity' => 'sometimes|integer|min:0',
            'restocked_at' => 'sometimes|date',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle image update
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('restockeds', 'public');
        }

        $restocked->update($validated);
        return response()->json($restocked->load(['product', 'admin']));
    }

    // DELETE /api/restockeds/{id}
    public function destroy($id)
    {
        $restocked = Restocked::findOrFail($id);
        $restocked->delete();
        return response()->json(['message' => 'Deleted successfully.']);
    }
}
