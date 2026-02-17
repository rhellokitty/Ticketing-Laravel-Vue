<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function register(RegisterStoreRequest $request)
    {

        $data = $request->validated();

        DB::beginTransaction();

        try {
            $user = new User;
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->role = $data['role'];
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Register Berhasil',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ],
                200
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if (auth()->user()->role == 'user' && $user->id != auth()->user()->id) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses untuk menghapus user ini'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'message' => 'User Berhasil Dihapus',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi Kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
