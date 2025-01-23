<?php

namespace App\Services;

class NotificationService
{

    public function sendNotification(array $fcm_tokens, $title, $body)
    {
        try {
            $factory = (new \Kreait\Firebase\Factory())
                ->withServiceAccount('sourcesave-sa.json');

            $messaging = $factory->createMessaging();

            foreach ($fcm_tokens as $fcm_token) {
                $message = [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'token' => $fcm_token,
                ];

                $response = $messaging->send($message);
            }


        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            echo $e->getMessage();
            return $this->sendError('Messaging error: ' . $e->getMessage());
        } catch (\Throwable $e) {
            echo $e->getMessage();
            return $this->sendError('General error: ' . $e->getMessage());
        }


    }

}
