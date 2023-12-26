<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }
    
    public function sendNotification($userId, $title, $body, $data = [])
    {
        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
        ]);

        $message = CloudMessage::withTarget('topic', (string) $userId)
            ->withNotification($notification) // optional
            ->withData($data); // optional

        $this->messaging->send($message);
    }
}
