<?php

namespace App\Http\Middleware;

use Closure;

class AppAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        $apiKey = $request->header('x-api-key');
        return $next($request);
    }
}
