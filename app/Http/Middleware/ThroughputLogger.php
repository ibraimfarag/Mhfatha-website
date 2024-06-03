<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ThroughputLogger
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $processingTime = $endTime - $startTime;

        // Log::channel('api_throughput')->info("Request to {$request->method()} {$request->fullUrl()} processed in {$processingTime} seconds.");
        if (Str::startsWith($request->path(), 'api')) {
            Log::channel('api_throughput')->info("API Request to {$request->method()} {$request->fullUrl()} processed in {$processingTime} seconds.");
        }
        return $response;
    }
}
