<?php

use App\Http\Controllers\File\CompareFileController;
use App\Http\Controllers\File\FileController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'store']);
Route::post('/verify_account', [UserController::class, 'VerifyAccount'])->middleware('verify_account');
Route::post('/resend_otp', [UserController::class, 'ResendOTP']);
Route::post('/reset_password', [UserController::class, 'ResetPassword'])->middleware('reset_password');
Route::post('/login', [UserController::class, 'Login'])->middleware('login');
Route::group(['middleware' => ['auth:user', 'scope:user']], function () {
    Route::post('/logout', [UserController::class, 'Logout']);
    Route::post('/add_group', [GroupController::class, 'store']);

    Route::post('/add_file', [FileController::class, 'store']);
    Route::get('/get_files', [FileController::class, 'index']);
    Route::post('/change_file_status', [FileController::class, 'ChangeFileStatus'])->middleware('change_file_status');

    Route::post('/check_in_files', [FileController::class, 'CheckIn'])->middleware('check_in_files');
    Route::post('/check_out_file', [FileController::class, 'CheckOut']);

    Route::get('/get_permissions', [PermissionController::class, 'index']);

    Route::get('/get_users', [UserController::class, 'index']);

    Route::get('/get_groups', [GroupController::class, 'index']);
    Route::get('/get_group_users', [GroupController::class, 'GetGroupUsers']);
    Route::get('/get_group_permissions', [GroupController::class, 'GetGroupPermissions']);

    Route::post('/leave_group', [GroupController::class, 'LeaveGroup'])->middleware('leave_group');

    Route::get('/show_file_versions', [FileController::class, 'ShowFileVersions']);

    Route::post('/return_to_old_version', [FileController::class, 'returnToOldVersion'])->middleware('return_to_old_version');

    Route::get('/get_file_log', [FileController::class, 'getFileLog']);


    Route::get('/get_user_files', [FileController::class, 'getUserFiles'])->middleware('get_user_files');
    Route::get('/compareFiles', [CompareFileController::class, 'compareFiles']);
    Route::get('/get_edited_files', [FileController::class, 'GetFilesEditByUser'])->middleware('get_user_edited_files');



});
