<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\File\FileController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AdminController::class, 'Login']);
Route::group(['middleware' => ['auth:admin', 'scope:admin']], function () {
    Route::post('/logout', [AdminController::class, 'Logout']);
    Route::get('/get_groups',[GroupController::class,'index']);
    Route::post('/change_group_status',[GroupController::class,'ChangeStatus']);
    Route::get('/get_group_users',[GroupController::class,'GetGroupUsers']);
    Route::get('/get_group_permissions',[GroupController::class,'GetGroupPermissions']);

    Route::get('/get_users',[UserController::class,'index']);
    Route::get('/get_user_groups',[GroupController::class,'GetUserGroups']);
    Route::get('/get_user_files',[FileController::class,'getUserFiles']);
});
