<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseCRUDController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Requests\User\Auth\CreateUserRequest;
use App\Http\Requests\User\Auth\ResendOTPRequest;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Http\Requests\User\Auth\UserLoginRequest;
use App\Http\Requests\User\Auth\VerifyAccountRequest;
use App\Http\Requests\User\GetUserLogRequest;
use App\Mail\OTPVerificationMail;
use App\Models\Admin;
use App\Models\GroupUser;
use App\Models\User;
use App\Services\UserService;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use phpseclib3\Crypt\DSA\Formats\Signature\ASN1;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends BaseCRUDController
{

    public function __construct(UserService $service)
    {
        $this->service = $service;
        $this->createRequest = CreateUserRequest::class;
    }

    public function Login(UserLoginRequest $request)
    {

        return $this->service->Login($request);
    }

    public function Logout()
    {
        return $this->service->Logout();
    }

    public function VerifyAccount(VerifyAccountRequest $request)
    {
        return $this->service->VerifyAccount($request);
    }

    public function ResendOTP(ResendOTPRequest $request)
    {
        return $this->service->ResendOTP($request);
    }


    public function ResetPassword(ResetPasswordRequest $request)
    {
        return $this->service->ResetPassword($request);
    }


}


