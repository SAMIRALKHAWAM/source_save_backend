<?php

namespace App\Services;

use App\Enums\GroupStatusEnum;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Requests\Group\ChangeStatusGroupRequest;
use App\Http\Requests\Group\GetGroupsRequest;
use App\Http\Requests\Group\GroupIdRequest;
use App\Http\Requests\User\UserIdRequest;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class GroupService extends BaseService
{

    protected $roleService;
    protected $notification;

    public function __construct(Group $model, NotificationController $notificationController,RoleService $roleService)
    {
        $this->model = $model;
        $this->roleService = $roleService;
        $this->notification = $notificationController;
    }

    public function create($data)
    {
        $user = \auth('user')->user();
        $fileName = 'logs/user_logs/user_' . $user->id . '.log';
        $content = "Add Group : {$data['name']} \n";
        $tokens = User::whereIn('id', $data['users'])
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')->toArray();
        Storage::append($fileName, $content);
        $this->notification->sendNotification($tokens, 'New Group ' . $data['name'], 'You are member of this group');
        return parent::create($data); // TODO: Change the autogenerated stub
    }

    public function getAll($where = [])
    {

        $request = app(GetGroupsRequest::class);
        $arr = Arr::only($request->validated(), ['status']);
        if (\request()->route()->getPrefix() == 'user_api') {
            $user = \auth('user')->user();
            $groups = Group::whereHas('GroupUsers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            if (!empty($arr['status'])) {
                if ($arr['status'] == GroupStatusEnum::PENDING) {
                    $where = [
                        'approved_by' => null,
                    ];
                } else {
                    $where = [
                        ['approved_by', '!=', null]
                    ];
                }
            }
            return $groups->where($where)->get();
        }
        // if admin_api route prefix
        if (!empty($arr['status'])) {
            if ($arr['status'] == GroupStatusEnum::PENDING) {
                $where = [
                    'approved_by' => null,
                ];
            } else {
                $where = [
                    ['approved_by', '!=', null]
                ];
            }
        }
        return parent::getAll($where); // TODO: Change the autogenerated stub
    }

    public function GetGroupUsers(GroupIdRequest $request){
        $arr = Arr::only($request->validated(), ['groupId']);
        $group = $this->getOne($arr['groupId']);
        $users = $group->GroupUsers;
        return \SuccessData('Group Users Found Successfully', $users);
    }

    public function GetGroupPermissions(GroupIdRequest $request){
        $arr = Arr::only($request->validated(), ['groupId']);
        $group = $this->getOne($arr['groupId']);
        $role = $this->roleService->getAll(['name' => $group->name])->first();
        $permissions = $role->Permissions()->get(['id', 'name']);
        $permissions->map(function ($permission) {
            unset($permission->pivot);
        });
        return \SuccessData('Group Permissions Found Successfully', $permissions);
    }

    public function GetUserGroups(UserIdRequest $request){
        $arr = Arr::only($request->validated(),['userId']);
        $user = User::find($arr['userId']);
        $group_users = $user->GroupUsers;
        $groups =  $group_users->map(function ($group){
            return  $group->Group;
        });
        return \SuccessData('groups Found successfully',$groups);
    }

    public function ChangeStatus(ChangeStatusGroupRequest $request){
        $arr = Arr::only($request->validated(), ['groupId', 'status']);
        if ($arr['status'] == GroupStatusEnum::ACCEPTED) {
            $this->update($arr['groupId'], [
                'status' => $arr['status'],
                'approved_by' => \auth('admin')->user()->id,
            ]);
        } else {
            $this->delete($arr['groupId']);
        }
        return \Success('group status changes successfully');
    }

    public function leaveGroup($group, $user)
    {
        // Remove user from group
        $group->GroupUsers()->where('user_id', $user->id)->delete();

        // Get FCM tokens of remaining users
        $userIds = GroupUser::where('group_id', $group->id)->pluck('user_id')->toArray();
        $tokens = User::whereIn('id', $userIds)->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

        // Send notification
        $this->notification->sendNotification(
            $tokens,
            'Group ' . $group->name,
            'User ' . $user->name . ' left the group'
        );
    }

}
