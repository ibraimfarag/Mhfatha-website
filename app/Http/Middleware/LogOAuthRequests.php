<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class LogOAuthRequests
{
    public function handle($request, Closure $next)
    {
        Log::channel('oauth')->info('OAuth Request Logged:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'params' => $request->all(),
        ]);

        return $next($request);
    }
}
