<?php

namespace App\Http\Controllers\File;

use App\Enums\FileStatusEnum;
use App\Enums\FileUpdateTypeEnum;
use App\Enums\GroupStatusEnum;
use App\Http\Controllers\BaseCRUDController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Requests\File\CreateFileRequest;
use App\Http\Requests\File\GetFileEditByUserController;
use App\Http\Requests\File\ShowFileVersionsRequest;
use App\Http\Requests\Files\ChangeFileStatusRequest;
use App\Http\Requests\Files\CheckInRequest;
use App\Http\Requests\Files\CheckOutRequest;

use App\Http\Requests\Files\GetUserFilesRequest;
use App\Http\Requests\OldFile\OldFileIdRequest;

use App\Models\File;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\OldFile;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FileController extends BaseCRUDController
{
    protected $notification;


    public function __construct(FileService $service, NotificationController $notificationController)
    {
        $this->service = $service;
        $this->createRequest = CreateFileRequest::class;
        $this->notification = $notificationController;


    }

    public function ChangeFileStatus(ChangeFileStatusRequest $request)
    {
        return $this->service->ChangeFileStatus($request);
    }

    public function CheckIn(CheckInRequest $request)
    {

        return $this->service->CheckIn($request);

    }


    public function CheckOut(CheckOutRequest $request)
    {
        return $this->service->CheckOut($request);
    }

    public function ShowFileVersions(ShowFileVersionsRequest $request)
    {
        return $this->service->ShowFileVersions($request);
    }

    public function returnToOldVersion(OldFileIdRequest $request)
    {
        return $this->service->returnToOldVersion($request);
    }

    public function getFileLog(ShowFileVersionsRequest $request)
    {
        return $this->service->getFileLog($request);
    }

    public function getUserFiles(GetUserFilesRequest $request)
    {
        return $this->service->getUserFiles($request);
    }

    public function GetFilesEditByUser(GetFileEditByUserController $request)
    {
       return $this->service->GetFilesEditByUser($request);
    }

}
