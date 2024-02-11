<?php

namespace App\Listeners;

use App\Events\NotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Broadcast;

class NotificationListener
{
    public function handle(NotificationEvent $event)
    {
        $type = $event->type;
        $message = $event->message;
        $userId = $event->userId;

        switch ($type) {
            case 'private':
                $this->sendPrivateNotification($userId, $message);
                break;
            case 'general':
                // Handle general notification logic
                break;
            // Handle other notification types
        }
    }

    protected function sendPrivateNotification($userId, $message)
    {
        $userChannel = 'private-user.' . $userId;

        Broadcast::channel($userChannel, function ($user) use ($message) {
            return $message;
        });
    }
}
