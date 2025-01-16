<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationController extends Controller
{

    public function sendNotification($fcm_token)
    {
        $messaging = Firebase::messaging();
        $message = [
            'notification' => [
                'title' => 'Hello!',
                'body' => 'This is a Firebase Notification.',
            ],
            'token' => $fcm_token,
        ];

        $messaging->send($message);

    }



}
