<?php

namespace App\Http\Middleware;

use Closure;

class AppAuthenticate
{
    public function handle($request, Closure $next, ...$guards)
    {
        return $next($request);
    }
}
