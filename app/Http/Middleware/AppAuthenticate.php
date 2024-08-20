<?php

namespace App\Http\Middleware;

use App\Exceptions\CodeException;
use App\Exceptions\ErrorCode;
use App\Models\App;
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
        $appInfo = App::query()->where('api_key', $apiKey)->first(['api_key']);
        if(empty($appInfo)) {
            throw new CodeException('forbidden.', ErrorCode::INVALID_TOKEN);
        }
        return $next($request);
    }
}
