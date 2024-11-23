<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseCRUDController;
use App\Http\Requests\Admin\Auth\AdminLoginRequest;
use App\Services\AdminService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends BaseCRUDController
{


    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }

    public function Login(AdminLoginRequest $request)
    {
        $arr = Arr::only($request->validated(), ['email', 'password']);
        $admin = $this->service->getAll(['email' => $arr['email']])->first();
        if (!$admin || !Hash::check($arr['password'], $admin->password)) {
            throw ValidationException::withMessages(['email or password not match']);
        }
        $admin['token'] = $admin->createToken('authToken', ['admin'])->accessToken;
        return \SuccessData('Admin Login Successfully', $admin);
    }

    public function Logout()
    {
        $admin = \auth('admin')->user();
        $admin->tokens()->where('scopes','["admin"]')->delete();
        return \Success('Admin Logout Successfully');
    }

}
