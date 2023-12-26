<?php

namespace App\Services;

use App\Services\FirebaseService;
use App\Models\User;


class AppNotificationService
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function sendToUser(User $user, array $data)
    {
        // Create a new notification for the user
        $notification = $this->firebaseService->sendNotification($user->id, $data['title'], $data['body']);

        return $notification;
    }
}
