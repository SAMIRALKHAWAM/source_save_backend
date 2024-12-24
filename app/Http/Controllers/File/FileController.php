<?php

namespace App\Http\Controllers\File;

use App\Enums\FileStatusEnum;
use App\Enums\FileUpdateTypeEnum;
use App\Enums\GroupStatusEnum;
use App\Http\Controllers\BaseCRUDController;
use App\Http\Controllers\Controller;
use App\Http\Requests\File\CreateFileRequest;
use App\Http\Requests\Files\ChangeFileStatusRequest;
use App\Http\Requests\Files\CheckInRequest;
use App\Http\Requests\Files\CheckOutRequest;
use App\Models\File;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\OldFile;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FileController extends BaseCRUDController
{

    public function __construct(FileService $service)
    {
        $this->service = $service;
        $this->createRequest = CreateFileRequest::class;

    }

    public function ChangeFileStatus(ChangeFileStatusRequest $request)
    {
        $arr = Arr::only($request->validated(), ['file_id', 'status']);
        $user = \auth('user')->user();
        $file = $this->service->getOne($arr['file_id']);
        $group_id = $file->GroupUser->group_id;
        $group_Admin = Group::GroupAdmin($group_id);
        if ($group_Admin->user_id == $user->id) {
            $this->service->update($arr['file_id'], ['status' => $arr['status']]);
            if ($arr['status'] == GroupStatusEnum::ACCEPTED) {
                $fileName = 'logs/file_logs/file_' . $file->id . '.log';
                $content = "File Registered: Accepted By $user->name \n";
                Storage::put($fileName, $content);
            }
        } else {
            throw new AccessDeniedHttpException('Access Denied : Dont Have Permission');
        }
        return \Success('file status updated Successfully');
    }

    public function CheckIn(CheckInRequest $request)
    {
        $user = \auth('user')->user();
        $arr = Arr::only($request->validated(), ['group_id', 'files']);
        $group_Admin = Group::GroupAdmin($arr['group_id']);
        if ($group_Admin->user_id == $user->id ||  $user->hasPermissionTo('Edit_File')) {
            File::whereIn('id', $arr['files'])->update(['availability' => FileStatusEnum::UNAVAILABLE, 'reserved_by' => $user->id]);
            foreach ($arr['files'] as $file) {
                $fileName = 'logs/file_logs/file_' . $file . '.log';
                $content = "File Reserved By $user->name \n";
                Storage::append($fileName, $content);
            }
            return \Success('Done Check In Files');
        }
        throw new AccessDeniedHttpException('Access Denied : Dont Have Permission');
    }


    public function CheckOut(CheckOutRequest $request)
    {
        $user = \auth('user')->user();
        $arr = Arr::only($request->validated(), ['file_id', 'update_type', 'url']);
        $file = $this->service->getOne($arr['file_id']);
        $group_id = $file->GroupUser->group_id;
        $new_group_user_id = GroupUser::where([
            'user_id' => $user->id,
            'group_id' => $group_id,
        ])->first()->id;
        $fileName = 'logs/file_logs/file_' . $file->id . '.log';
        $content = "File UnReserved \n";
        Storage::append($fileName, $content);
        if ($arr['update_type'] == FileUpdateTypeEnum::FULL_UPDATE && !empty($arr['url'])) {
            $old_file_arr = [
                'file_id' => $file->id,
                'group_user_id' => $file->group_user_id,
                'name' => $file->name,
                'description' => $file->description,
                'size_MB' => $file->size_MB,
                'url' => $file->url,
            ];
            $oldFile = OldFile::create($old_file_arr);
            $path = 'Files/';
            $uploadFile = \uploadFile($arr['url'], '(' . $oldFile->id . ')' . $file->name, $path);
            $new_file_arr = [
                'group_user_id' => $new_group_user_id,
                'name' => $file->name,
                'description' => $file->description,
                'size_MB' => number_format(Storage::size('public/' . $uploadFile['url']) / 1024 / 1024, 2),
                'url' => Storage::url('public/' . $uploadFile['url']),
                'reserved_by' => null,
                'availability' => FileStatusEnum::AVAILABLE,
                'status' => GroupStatusEnum::ACCEPTED,
            ];
            $file->update($new_file_arr);
        } else {
            $file->update([
                'availability' => FileStatusEnum::AVAILABLE,
                'reserved_by' => null,
            ]);
        }
        return \Success('Done Check Out File');

    }
}
