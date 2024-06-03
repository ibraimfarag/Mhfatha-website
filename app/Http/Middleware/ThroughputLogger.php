<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\GeoIP;
use Illuminate\Support\Str;

class ThroughputLogger
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $processingTime = $endTime - $startTime;

        $requestBytes = strlen($request->getContent()); // Get request body size in bytes
        $clientIp = $request->ip(); // Get client IP address

        if (Str::startsWith($request->path(), 'api')) {
            Log::info("API Request to {$request->method()} {$request->fullUrl()} processed in {$processingTime} seconds. Requested Bytes: {$requestBytes}, Client IP: {$clientIp}");
        }

        return $response;
    }
}
