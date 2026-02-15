<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Unauthorized',
                    'data' => null
                ], 401);
            }

            $user = Auth::guard('web')->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login Success',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'message' => 'Data User Berhasil Diambil',
                'data' => new UserResource($user)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logout Berhasil',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ]);
        }
    }

    // lanjutkan di menit 48:00

}
