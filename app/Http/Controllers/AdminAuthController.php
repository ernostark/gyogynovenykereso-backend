<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{

    public function login(AdminLoginRequest $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Hibás email cím vagy jelszó!',
            ], 401);
        }

        $expiresAt = Carbon::now()->addDay();

        $token = $admin->createToken('admin_auth_token', ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Sikeres bejelentkezés!',
            'token' => $token,
            'expires_at' => $expiresAt->toDateTimeString(),
            'admin' => $admin,
        ], 200);
    }

    public function logout(Request $request)
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Nincs bejelentkezett admin.',
            ], 401);
        }

        $admin->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sikeres kijelentkezés!',
        ], 200);
    }
}
