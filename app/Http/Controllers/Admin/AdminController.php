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


    public function Logout()
    {
        return $this->service->Logout();
    }

}
