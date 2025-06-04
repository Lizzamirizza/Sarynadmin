<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return redirect()->route('filament.admin.pages.dashboard');
// });
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});
