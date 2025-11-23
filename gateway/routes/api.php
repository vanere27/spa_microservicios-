<?php

use App\Http\Controllers\GatewayController;

// AUTH
Route::prefix('api/auth')->group(function () {
    Route::post('auth/login', [GatewayController::class, 'login']);
    Route::post('auth/logout', [GatewayController::class, 'logout']);
    Route::post('auth/create_user', [GatewayController::class, 'createUser']);
    Route::post('auth/forgot', [GatewayController::class, 'forgotPassword']);
    Route::post('auth/reset', [GatewayController::class, 'resetPassword']);
});
// SERVICIOS
Route::get('servicios', [GatewayController::class, 'serviciosIndex']);
Route::post('servicios', [GatewayController::class, 'serviciosStore']);
Route::put('servicios/{id}', [GatewayController::class, 'serviciosUpdate']);
Route::delete('servicios/{id}', [GatewayController::class, 'serviciosDelete']);
Route::get('servicios/{id}', [GatewayController::class, 'serviciosShow']);


// RESERVAS 
Route::prefix('reservas')->group(function () {
    Route::get('/', [GatewayController::class, 'reservasIndex']);          // listar
    Route::post('/', [GatewayController::class, 'reservasStore']);         // crear
    Route::get('/{id}', [GatewayController::class, 'reservasShow']);       // consultar una
    Route::put('/{id}', [GatewayController::class, 'reservasUpdate']);     // actualizar
    Route::delete('/{id}', [GatewayController::class, 'reservasDelete']);  // eliminar
});


// REPORTES
Route::get('reportes/pdf', [GatewayController::class, 'reportePDF']);
Route::get('reportes/excel', [GatewayController::class, 'reporteExcel']);


// AUDITORÍA
// AUDITORÍA
Route::get('auditoria', [GatewayController::class, 'auditoriaIndex']);
Route::post('auditoria', [GatewayController::class, 'auditoriaStore']);

Route::get('auditoria/usuario/{usuario}', [GatewayController::class, 'auditoriaByUser']);

Route::get('auditoria/{id}', [GatewayController::class, 'auditoriaShow']);
Route::delete('auditoria/{id}', [GatewayController::class, 'auditoriaDelete']);



// NOTIFICACIONES
// NOTIFICACIONES CORREO
Route::post('notificaciones/enviar', [GatewayController::class, 'notificacionesCorreo']);

