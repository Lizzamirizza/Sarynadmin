<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // 🟢 Registrasi user baru
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'address'    => 'required|string',
            'province'  => 'required|string|max:100',
            'city'      => 'required|string|max:100',
            'subcity' => 'required|string|max:100',
            'postalcode'  => 'required|string|max:10',
            'phone'   => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'address'  => $request->address,
            'province' => $request->province,
            'city'     => $request->city,
            'subcity'  => $request->subcity,
            'postalcode'   => $request->postalcode,
            'phone'    => $request->phone,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user'    => $user,
        ], 201);
    }

    // 🟡 Login (token-based API login)
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    // 🔴 Logout (hapus token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

}