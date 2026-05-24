<?php

use App\Http\Controllers\Api\SucursalController;
use App\Http\Controllers\Api\BarberoController;
use App\Http\Controllers\Api\ServicioController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\CheckApiKey;
use Illuminate\Support\Facades\Route;

Route::middleware([CheckApiKey::class])->group(function () {
    Route::apiResource('sucursales', SucursalController::class);
    Route::apiResource('barberos', BarberoController::class);
    Route::apiResource('servicios', ServicioController::class);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('citas', CitaController::class);
    Route::apiResource('users', UserController::class);

    // Extra actions
    Route::patch('citas/{id}/completar', [CitaController::class, 'completar']);
    Route::patch('citas/{id}/cancelar', [CitaController::class, 'cancelar']);
});
