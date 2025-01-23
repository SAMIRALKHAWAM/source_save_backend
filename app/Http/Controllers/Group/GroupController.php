<?php

namespace App\Http\Controllers\Group;

use App\Enums\GroupStatusEnum;
use App\Http\Controllers\BaseCRUDController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Requests\Group\ChangeStatusGroupRequest;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\GroupIdRequest;
use App\Http\Requests\User\UserIdRequest;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Services\GroupService;

use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GroupController extends BaseCRUDController
{


    protected $notification;

    public function __construct(GroupService $service,NotificationController $notificationController)
    {
        $this->service = $service;
        $this->createRequest = CreateGroupRequest::class;
        $this->notification = $notificationController;

    }

    public function ChangeStatus(ChangeStatusGroupRequest $request)
    {
        return $this->service->ChangeStatus($request);
    }

    public function LeaveGroup(GroupIdRequest $request){
        $group = $request->attributes->get('group');
        $user = \auth('user')->user();
        $this->service->leaveGroup($group, $user);
        return \Success('User Left Group Successfully');
    }

    public function GetUserGroups(UserIdRequest $request){
       return $this->service->GetUserGroups($request);
    }

    public function GetGroupUsers(GroupIdRequest $request)
    {
        return $this->service->GetGroupUsers($request);
    }

    public function GetGroupPermissions(GroupIdRequest $request)
    {
        return $this->service->GetGroupPermissions($request);
    }

}
