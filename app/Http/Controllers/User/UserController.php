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
        $arr = Arr::only($request->validated(), ['email', 'password','fcm_token']);
        $actor = User::where('email', $arr['email'])->first();
        $role = 'user';
        if (!$actor) {
            $actor = Admin::where('email', $arr['email'])->first();
            $role = 'admin';
        }
        if (!Hash::check($arr['password'], $actor->password)) {
            throw ValidationException::withMessages(['email or password not match']);
        }
        if (!empty($arr['fcm_token'])){
            $actor->update(['fcm_token' => $arr['fcm_token']]);
        }
        $actor['role'] = $role;
        $actor['token'] = $actor->createToken('authToken', [$role])->accessToken;

        return \SuccessData('user Login Successfully', $actor);
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
        $mail = new OTPVerificationMail();
        $mail->sendEmail($user);
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

    public function getUserLog(GetUserLogRequest $request)
    {
        $arr = Arr::only($request->validated(), ['userId', 'groupId']);
        $user = \auth('user')->user();
        $group_user_admin = GroupUser::where('user_id', $user->id)->where('group_id', $arr['groupId'])->first();
        if (!$group_user_admin) {
            throw new AccessDeniedHttpException('Access Denied : you not admin of group');
        }
        $file = Storage::get('logs/user_logs/user_' . $arr['userId'] . '.log');
        return \SuccessData('User Log Found Successfully', $file);
    }
}


