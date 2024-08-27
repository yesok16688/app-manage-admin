<?php

namespace App\Http\Middleware;

use App\Exceptions\CodeException;
use App\Exceptions\ErrorCode;
use App\Models\AppVersion;
use Closure;
use Illuminate\Http\Request;

class AppAuthenticate
{
    /**
     * @throws CodeException
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $apiKey = $request->json('api_key');
        if (empty($apiKey)) {
            throw new CodeException('forbidden.', ErrorCode::INVALID_TOKEN);
        }
        $appInfo = AppVersion::query()->with('app')->where('api_key', $apiKey)->first();
        if(empty($appInfo)) {
            throw new CodeException('forbidden.', ErrorCode::INVALID_TOKEN);
        }
        $request->offsetSet('app_info', $appInfo);
        return $next($request);
    }
}
