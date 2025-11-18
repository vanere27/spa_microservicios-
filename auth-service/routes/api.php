<?php

use App\Http\Controllers\GatewayController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [GatewayController::class, 'auth_login']);
    Route::post('/create_user', [GatewayController::class, 'auth_create_user']);
    Route::post('/change_password', [GatewayController::class, 'auth_change_password']);
    Route::post('/forgot_password', [GatewayController::class, 'auth_forgot']);
    Route::post('/reset_password', [GatewayController::class, 'auth_reset']);
});

Route::prefix('servicios')->group(function () {
    Route::get('/', [GatewayController::class, 'servicios_index']);
    Route::post('/', [GatewayController::class, 'servicios_store']);
    Route::put('/{id}', [GatewayController::class, 'servicios_update']);
    Route::delete('/{id}', [GatewayController::class, 'servicios_delete']);
});

Route::prefix('reservas')->group(function () {
    Route::get('/', [GatewayController::class, 'reservas_index']);
    Route::post('/', [GatewayController::class, 'reservas_store']);
});

Route::prefix('reportes')->group(function () {
    Route::get('/excel', [GatewayController::class, 'reportes_excel']);
    Route::get('/pdf', [GatewayController::class, 'reportes_pdf']);
});

Route::prefix('auditoria')->group(function () {
    Route::get('/', [GatewayController::class, 'auditoria_index']);
});

Route::prefix('notificaciones')->group(function () {
    Route::post('/send', [GatewayController::class, 'notificaciones_send']);
});
