<?php

namespace App\Http\Middleware;

use App\Exceptions\CodeException;
use App\Exceptions\ErrorCode;
use Closure;
use Exception;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class Auth2FACode
{
    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $user = $request->user();
        if(!$user || !$user->two_face_secret_key) {
            return $next($request);
        }
        if(!$request->input('code2fa')) {
            throw new CodeException('invalid 2FA code', ErrorCode::WRONG_2FA_CODE);
        }

        $google2fa = new Google2FA();
        if(!$google2fa->verifyKey($user->two_face_secret_key, $request->input('code2fa'))) {
            throw new CodeException('invalid 2FA code', ErrorCode::WRONG_2FA_CODE);
        }
        return $next($request);
    }
}
