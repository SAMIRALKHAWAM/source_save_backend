<?php

namespace App\Services;

use App\Enums\GroupStatusEnum;
use App\Http\Requests\Files\GetFilesRequest;
use App\Models\File;
use App\Models\Group;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FileService extends BaseService
{
    protected $groupUserService;

    public function __construct(File $model, GroupUserService $groupUserService)
    {
        $this->model = $model;
        $this->groupUserService = $groupUserService;
    }

    public function create($data)
    {
        $user = \auth('user')->user();
        $group = Group::find($data['group_id']);
        $group_Admin = Group::GroupAdmin($group->id);
        $role = Role::where(['name' => $group->name])->first();
        if ($user->hasRole($role->name) && ($group_Admin->user_id == $user->id || $role->hasPermissionTo('Add_File'))) {
            $group_user = $this->groupUserService->getAll([
                'user_id' => $user->id,
                'group_id' => $data['group_id'],
            ])->first();
            $data['group_user_id'] = $group_user->id;
            $path = 'Files/';
            $uploadFile = \uploadFile($data['url'], $data['name'], $path);
            $data['url'] = Storage::url('public/' . $uploadFile['url']);
            $data['size_MB'] = 0;
            return parent::create($data); // TODO: Change the autogenerated stub
        } else {
            throw new AccessDeniedHttpException('Access Denied : Dont Have Permission');
        }
    }

    public function getAll($where = [])
    {
        $user = \auth('user')->user();
        $request = \app(GetFilesRequest::class);
        $arr = Arr::only($request->validated(), ['group_id', 'status']);
        $group = Group::find($arr['group_id']);
        $group_Admin = Group::GroupAdmin($group->id);
        $group_users = $group->GroupUsers()->pluck('id');
        $files = File::whereIn('group_user_id', $group_users);
        if (!empty($arr['status'])) {
            if (($arr['status'] != GroupStatusEnum::ACCEPTED && $group_Admin->user_id == $user->id) || $arr['status'] == GroupStatusEnum::ACCEPTED) {
                $files->where('status', $arr['status']);
            } else {
                throw new AccessDeniedHttpException('Access Denied : Dont Have Permission');
            }
        }
       return $files->get();
    }

}
