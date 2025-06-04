<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk dengan pagination dan gambar dari storage.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 8);

            $products = Product::with(['category', 'admin'])
                ->where('stock', '>', 0)
                ->paginate($perPage);

            $products->getCollection()->transform(function ($product) {
                // Pastikan image dalam bentuk array
                $images = is_string($product->image)
                    ? json_decode($product->image, true)
                    : $product->image;

                // Ubah setiap path ke URL lengkap
                $product->images = collect($images)->map(function ($img) {
                    return $img ? asset('storage/' . $img) : null;
                });

                return $product;
            });

            return response()->json($products);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail produk berdasarkan ID.
     */
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'admin'])->findOrFail($id);

            // Pastikan image dalam bentuk array
            $images = is_string($product->image)
                ? json_decode($product->image, true)
                : $product->image;

            // Ubah ke URL lengkap
            $product->images = collect($images)->map(function ($img) {
                return $img ? asset('storage/' . $img) : null;
            });

            return response()->json($product);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ], 500);
        }
    }
}
