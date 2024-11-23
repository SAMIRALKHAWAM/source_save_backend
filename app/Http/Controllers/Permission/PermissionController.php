<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\BaseCRUDController;
use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends BaseCRUDController
{

    public function __construct(PermissionService $service)
    {
        $this->service = $service;
    }
}
