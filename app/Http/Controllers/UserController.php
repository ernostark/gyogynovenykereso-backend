<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{

    public function register(RegisterUserRequest $request)
    {
        try {
            $request->validated();

            $user = User::create([
                "name" => $request["name"],
                "email" => $request["email"],
                "password" => bcrypt($request["password"]),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sikeres regisztráció!',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a regisztráció során.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function login(LoginUserRequest $request)
    {
        $request->validated();

        if (!Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            return response()->json(['message' => 'Helytelen email vagy jelszó!'], 401);
        }

        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres bejelentkezés!',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sikeres kijelentkezés!'], 200);
    }

    public function updateProfile(UpdateUserProfile $request)
    {
        $user = $request->user();

        $user->update($request->validated());

        return response()->json([
            'message' => 'Profil sikeresen frissítve!',
            'user' => $user,
        ], 200);
    }
}
