<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Enums\NotificationType;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user_id' => 'required|integer',
            'type' => 'required|string|in:private,general,by_region,by_device_type,by_category,by_gender',
            'message' => 'required|string',
        ]);

        // Extract request data
        $userId = $request->input('user_id');
        $type = $request->input('type');
        $message = $request->input('message');

        // Dispatch the NotificationEvent
        NotificationEvent::dispatch($type, $message, $userId);

        return response()->json(['message' => 'Notification sent successfully'], 200);
    }
}
