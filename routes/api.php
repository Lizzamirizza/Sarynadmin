<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RestockedController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\UserController;

// Route public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/category/{categoryId}', [ProductController::class, 'getRelatedProducts']);

// Midtrans callback (bebas auth karena dipanggil oleh midtrans)
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

// Route yang butuh autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{order_id}/cancel', [OrderController::class, 'cancelOrder']);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus']);
    Route::get('/payments/order/{order}', [PaymentController::class, 'getByOrder']);

    // Midtrans snap token
    Route::post('/midtrans/snap-token', [MidtransController::class, 'createSnapToken']);
});

// Restocked routes
Route::prefix('restockeds')->group(function () {
    Route::get('/', [RestockedController::class, 'index']);           
    Route::post('/', [RestockedController::class, 'store']);         
    Route::get('{id}', [RestockedController::class, 'show']);        
    Route::put('{id}', [RestockedController::class, 'update']);       
    Route::delete('{id}', [RestockedController::class, 'destroy']);   
});
