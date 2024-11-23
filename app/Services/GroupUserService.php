<?php

namespace App\Services;

use App\Models\GroupUser;

class GroupUserService extends BaseService
{

    public function __construct(GroupUser $model)
    {
        $this->model = $model;
    }

}
