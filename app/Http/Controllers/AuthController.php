<?php

namespace App\Http\Controllers;

use App\Exceptions\CodeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @throws CodeException
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            throw new CodeException('Invalid Credentials', 400);
        }

        $user = $request->user();
        $token = $user->createToken('authToken')->plainTextToken;

        return $this->jsonDataResponse(['token' => $token, 'user' => $user]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->jsonResponse(['message' => 'Logged out successfully']);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $newToken = $request->user()->createToken('authToken')->plainTextToken;
        return $this->jsonResponse(['token' => $newToken]);
    }
}
