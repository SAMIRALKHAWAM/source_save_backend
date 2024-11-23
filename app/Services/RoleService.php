<?php

namespace App\Services;

use Spatie\Permission\Models\Role;

class RoleService extends BaseService
{

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

}
