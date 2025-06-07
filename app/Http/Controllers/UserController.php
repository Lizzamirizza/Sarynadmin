<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'Data user berhasil diambil',
            'user' => $request->user(),
        ]);
    }
}
