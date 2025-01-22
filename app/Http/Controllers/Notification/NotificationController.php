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
            $factory = (new \Kreait\Firebase\Factory())
                ->withServiceAccount('sourcesave-sa.json');

            $messaging = $factory->createMessaging();

            $message = [
                'notification' => [
                    'title' => 'Hello!',
                    'body' => 'This is a Firebase Notification.',
                ],
                'token' => $fcm_token,
            ];

            $response = $messaging->send($message);


        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            echo $e->getMessage();
            return $this->sendError('Messaging error: ' . $e->getMessage());
        } catch (\Throwable $e) {

            return $this->sendError('General error: ' . $e->getMessage());
        }


    }


}
