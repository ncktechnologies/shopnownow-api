<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppNotificationService;
use App\Models\User;

class AppNotificationController extends Controller
{
    protected $appNotificationService;

    public function __construct(AppNotificationService $appNotificationService)
    {
        $this->appNotificationService = $appNotificationService;
    }

    public function sendToUser(Request $request, User $user)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $notification = $this->appNotificationService->sendToUser($user, $data);

        return response()->json(['message' => 'Notification sent successfully', 'notification' => $notification]);
    }

    public function sendToUsersById(Request $request)
    {
        $data = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $users = User::find($data['user_ids']);

        foreach ($users as $user) {
            $this->appNotificationService->sendToUser($user, $data);
        }

        return response()->json(['message' => 'Notifications sent successfully']);
    }

    public function sendToAllUsers(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $this->appNotificationService->sendToUser($user, $data);
        }

        return response()->json(['message' => 'Notifications sent to all users successfully']);
    }
}
