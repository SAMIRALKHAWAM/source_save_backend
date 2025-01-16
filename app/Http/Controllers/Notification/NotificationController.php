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
        try {
            $messaging = (new \Kreait\Firebase\Factory)
                ->withServiceAccount('sourcesave-sa.json')
                ->createMessaging();

            $message = [
                'notification' => [
                    'title' => 'Hello!',
                    'body' => 'This is a Firebase Notification.',
                ],
                'token' => $fcm_token,
            ];

            $response = $messaging->send($message);


        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            echo 'Messaging error: ' . $e->getMessage();
        } catch (\Throwable $e) {
            echo 'General error: ' . $e->getMessage();
        }


    }



}
