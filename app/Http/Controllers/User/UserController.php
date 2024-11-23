<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseCRUDController;
use App\Http\Requests\User\Auth\CreateUserRequest;
use App\Http\Requests\User\Auth\ResendOTPRequest;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Http\Requests\User\Auth\UserLoginRequest;
use App\Http\Requests\User\Auth\VerifyAccountRequest;
use App\Mail\OTPVerificationMail;
use App\Models\User;
use App\Services\UserService;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use phpseclib3\Crypt\DSA\Formats\Signature\ASN1;

class UserController extends BaseCRUDController
{
    protected $mail;

    public function __construct(UserService $service, OTPVerificationMail $mail)
    {
        $this->service = $service;
        $this->mail = $mail;
        $this->createRequest = CreateUserRequest::class;
    }

    public function Login(UserLoginRequest $request)
    {
        $arr = Arr::only($request->validated(), ['email', 'password']);
        $user = $this->service->getAll(['email' => $arr['email']])->first();
        if (!$user || !Hash::check($arr['password'], $user->password)) {
            throw ValidationException::withMessages(['email or password not match']);
        }
        $user['token'] = $user->createToken('authToken', ['user'])->accessToken;
        return \SuccessData('user Login Successfully', $user);
    }

    public function Logout()
    {
        $user = \auth('user')->user();
        $user->tokens()->where('scopes', '["user"]')->delete();
        return \Success('user Logout Successfully');
    }

    public function VerifyAccount(VerifyAccountRequest $request)
    {
        $arr = Arr::only($request->validated(), ['email', 'code']);
        $user = User::where('email', $arr['email'])->first();
        if (Hash::check($arr['code'], $user->Code?->code) && \now()->between($user->Code?->from_date_time, $user->Code?->to_date_time)) {
            $user->update(['email_verified_at' => \now()]);
            $user->Code()->delete();
            unset($user->Code);
            $user['token'] = $user->createToken('authToken', ['user'])->accessToken;
            return \SuccessData('Email verified successfully', $user);
        }
        throw ValidationException::withMessages(['Code Wrong Or Expired']);
    }

    public function ResendOTP(ResendOTPRequest $request)
    {
        $arr = Arr::only($request->validated(), ['email']);
        $user = User::where('email', $arr['email'])->first();
        $this->mail->sendEmail($user);
        return \Success('OTP Send Successfully');
    }


    public function ResetPassword(ResetPasswordRequest $request)
    {
        $arr = Arr::only($request->validated(), ['email', 'code', 'password']);
        $user = User::where('email', $arr['email'])->first();
        if (Hash::check($arr['code'], $user->Code?->code) && \now()->between($user->Code?->from_date_time, $user->Code?->to_date_time)) {
            $user->update(['password' => $arr['password']]);
            $user->Code()->delete();
            unset($user->Code);
            return \Success('Password Changed successfully');
        }
        throw ValidationException::withMessages(['Code Wrong Or Expired']);
    }
}


