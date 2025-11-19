<?php
 
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/create_user', [UserController::class,'create_user']);
Route::post('/login', [UserController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [UserController::class,'logout']);
    Route::post('/change_password', [UserController::class,'change_password']);
});
