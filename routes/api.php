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
use App\Http\Controllers\UserController;




// Route untuk tes autentikasi dengan Laravel Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
    Route::post('/cart/checkout', [CartController::class, 'checkout']);
});

// Route untuk registrasi
Route::post('/register', [AuthController::class, 'register']);

// Route untuk login
Route::post('/login', [AuthController::class, 'login']);

// Route untuk mendapatkan daftar kategori (public route)
Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);

Route::get('/products/{id}', [ProductController::class, 'show']);

// Route kategori yang dilindungi autentikasi (hanya bisa diakses oleh pengguna yang sudah login)
Route::middleware('auth:sanctum')->get('/protected-categories', [CategoryController::class, 'index']);

// routes/api.php
Route::get('/products/category/{categoryId}', [ProductController::class, 'getRelatedProducts']);

// routes
Route::prefix('restockeds')->group(function () {
    Route::get('/', [RestockedController::class, 'index']);           
    Route::post('/', [RestockedController::class, 'store']);         
    Route::get('{id}', [RestockedController::class, 'show']);        
    Route::put('{id}', [RestockedController::class, 'update']);       
    Route::delete('{id}', [RestockedController::class, 'destroy']);   
});

Route::get('/midtrans/token/{orderId}', [MidtransController::class, 'getSnapToken']);

