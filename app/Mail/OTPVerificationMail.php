<?php

namespace App\Mail;


use App\Http\Requests\Auth\SendOTPRequest;
use App\Models\Code;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;


class OTPVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $client;
    protected $gmailService;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {

        $this->client = new Google_Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setAccessType('offline');
        $this->client->setAccessToken(env('GOOGLE_ACCESS_TOKEN'));

        $this->gmailService = new Google_Service_Gmail($this->client);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            $this->refreshToken();
        }
    }


    private function getAccessToken()
    {
        return cache('google_access_token') ?? env('GOOGLE_ACCESS_TOKEN');
    }

    // Refresh the access token using the refresh token
    private function refreshToken()
    {
        $newToken = $this->client->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));
        $this->saveAccessToken($newToken['access_token']);
        $this->client->setAccessToken($newToken['access_token']);

    }

    // Save the access token in cache
    private function saveAccessToken($token)
    {
        cache(['google_access_token' => $token]);
    }

    // Send email via Gmail API
    public function sendEmail($user)
    {

        $to = $user->email;
        $subject = "otp Verification Code";
        $body = rand(111111, 999999);

        $user->Code()->delete();

        Code::create([
            'user_id' => $user->id,
            'code' => Hash::make($body),
            'from_date_time' => \now(),
            'to_date_time' => \now()->addMinutes(15),
        ]);

        $message = new Google_Service_Gmail_Message();

        $rawMessageString = "To: $to\r\n";
        $rawMessageString .= "Subject: $subject\r\n";
        $rawMessageString .= "MIME-Version: 1.0\r\n";
        $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $rawMessageString .= "Welcome to Our System\r\nyour verification code is: " . $body;


        $rawMessage = strtr(base64_encode($rawMessageString), ['+' => '-', '/' => '_']);
        $message->setRaw($rawMessage);

        try {
            $this->gmailService->users_messages->send('me', $message); // 'me' refers to the authenticated user (the one you impersonate)
        } catch (\Exception $e) {
            throw new \Exception('Error Send Email' . $e->getMessage());
        }
    }

}
