<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/create_user', [UserController::class,'create_user']);
Route::post('/login', [UserController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [UserController::class,'logout']);
    Route::post('/change_password', [UserController::class,'change_password']);
});
Route::post('/forgot_password', [UserController::class, 'forgot_password']);
Route::post('/reset_password', [UserController::class, 'reset_password']);









