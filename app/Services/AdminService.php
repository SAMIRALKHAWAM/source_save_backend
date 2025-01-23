<?php

namespace App\Services;

use App\Models\Admin;

class AdminService extends BaseService
{

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }

    public function Logout(){
        $admin = \auth('admin')->user();
        $admin->tokens()->where('scopes','["admin"]')->delete();
        return \Success('Admin Logout Successfully');
    }

}
