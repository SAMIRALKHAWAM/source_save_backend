<?php

use App\Http\Controllers\File\FileController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'store']);
Route::post('/verify_account', [UserController::class, 'VerifyAccount']);
Route::post('/resend_otp', [UserController::class, 'ResendOTP']);
Route::post('/reset_password', [UserController::class, 'ResetPassword']);
Route::post('/login', [UserController::class, 'Login']);
Route::group(['middleware' => ['auth:user', 'scope:user']], function () {
    Route::post('/logout', [UserController::class, 'Logout']);
    Route::post('/add_group', [GroupController::class, 'store']);

    Route::post('/add_file', [FileController::class, 'store']);
    Route::get('/get_files', [FileController::class, 'index']);
    Route::post('/change_file_status', [FileController::class, 'ChangeFileStatus']);

    Route::post('/check_in_files', [FileController::class, 'CheckIn']);
    Route::post('/check_out_file', [FileController::class, 'CheckOut']);

    Route::get('/get_permissions', [PermissionController::class, 'index']);

    Route::get('/get_users', [UserController::class, 'index']);
});
