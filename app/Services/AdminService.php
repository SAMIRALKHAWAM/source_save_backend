<?php

namespace App\Services;

use App\Models\Admin;

class AdminService extends BaseService
{

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }


}
