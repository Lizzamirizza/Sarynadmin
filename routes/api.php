<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RestockedController;


// Route untuk tes autentikasi dengan Laravel Sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route untuk registrasi
Route::post('/register', [AuthController::class, 'register']);

// Route untuk login
Route::post('/login', [AuthController::class, 'login']);

// Route untuk logout
Route::post('/logout', [AuthController::class, 'logout']);

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

