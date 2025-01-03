<?php

namespace App\Http\Controllers\Group;

use App\Enums\GroupStatusEnum;
use App\Http\Controllers\BaseCRUDController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\ChangeStatusGroupRequest;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\GroupIdRequest;
use App\Http\Requests\Group\LeaveGroupRequest;
use App\Models\Group;
use App\Services\GroupService;
use App\Services\RoleService;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GroupController extends BaseCRUDController
{

    protected $roleService;

    public function __construct(GroupService $service,RoleService $roleService)
    {
        $this->service = $service;
        $this->roleService = $roleService;
        $this->createRequest = CreateGroupRequest::class;

    }

    public function ChangeStatus(ChangeStatusGroupRequest $request)
    {
        $arr = Arr::only($request->validated(), ['groupId', 'status']);
        if ($arr['status'] == GroupStatusEnum::ACCEPTED) {
            $this->service->update($arr['groupId'], [
                'status' => $arr['status'],
                'approved_by' => \auth('admin')->user()->id,
            ]);
        } else {
            $this->service->delete($arr['groupId']);
        }
        return \Success('group status changes successfully');
    }

    public function GetGroupUsers(GroupIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['groupId']);
        $group = $this->service->getOne($arr['groupId']);
        $users = $group->GroupUsers;
        return \SuccessData('Group Users Found Successfully', $users);
    }

    public function GetGroupPermissions(GroupIdRequest $request)
    {
        $arr = Arr::only($request->validated(), ['groupId']);
        $group = $this->service->getOne($arr['groupId']);
        $role = $this->roleService->getAll(['name' => $group->name])->first();
        $permissions = $role->Permissions()->get(['id', 'name']);
        $permissions->map(function ($permission) {
            unset($permission->pivot);
        });
        return \SuccessData('Group Permissions Found Successfully', $permissions);
    }

    public function LeaveGroup(GroupIdRequest $request){
        $arr = Arr::only($request->validated(), ['groupId']);
        $userId = \auth('user')->user()->id;
        $group = Group::where('id',$arr['groupId'])->whereHas('GroupUsers',function ($q) use ($userId){
            $q->where('user_id',$userId)->where('is_admin',0);
        })->first();
        if (!$group){
            throw new AccessDeniedHttpException('Access Denied : Not In Group Or You is Admin Of this Group');
        }
        $group->GroupUsers()->where('user_id',$userId)->delete();
        return \Success('User Leave Group Successfully');
    }

}
