<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceRedirect;

class DeviceRedirectController extends Controller
{
    public function redirectToAppStore(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        $deviceType = '';

        if (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false || stripos($userAgent, 'Mac') !== false) {
            $deviceType = 'iOS';
        } elseif (stripos($userAgent, 'Android') !== false || stripos($userAgent, 'Windows') !== false) {
            $deviceType = 'Android';
        } else {
            // Default to iOS if the device type is unknown
            $deviceType = 'Android';        }

        $deviceRedirect = DeviceRedirect::where('device_type', $deviceType)->first();

        if ($deviceRedirect) {
            $deviceRedirect->increment('redirect_count');
            $redirectUrl = $deviceRedirect->app_store_link;
        } else {
            // Default to a generic URL if the device type is not found in the database
            $redirectUrl = 'https://example.com';
        }

        return redirect($redirectUrl);
    }

    public function getRedirectCounts()
    {
        $redirects = DeviceRedirect::all();
        return response()->json($redirects);
    }
}
