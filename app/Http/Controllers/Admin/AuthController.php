<?php

namespace app\Http\Controllers\Admin;

use App\Exceptions\CodeException;
use App\Exceptions\ErrorCode;
use App\Http\Controllers\Controller;
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
            throw new CodeException(errMsg: 'Invalid Credentials', errCode: ErrorCode::WRONG_PARAMS);
        }

        $user = $request->user();
        $token = $user->createToken('authToken')->plainTextToken;

        return $this->jsonDataResponse(['token' => $token]);
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

    public function info(Request $request): JsonResponse
    {
        $user = $request->user();
        $info = [
            'userId' => $user->id,
            'userName' => $user->name,
        ];
        return $this->jsonDataResponse($info);
    }
}
