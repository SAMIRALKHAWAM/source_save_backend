<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationController extends Controller
{

    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function sendNotification(array $fcm_tokens,$title,$body)
    {
        return $this->service->sendNotification($fcm_tokens,$title,$body);
    }


}
