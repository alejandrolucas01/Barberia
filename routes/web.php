<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SecretariaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('admin/reportes', [DashboardController::class, 'reportesIndex'])->name('admin.reportes.index');
    Route::get('admin/reportes/general', [DashboardController::class, 'reporteGeneral'])->name('admin.reportes.general');
    Route::get('admin/reportes/sucursal', [DashboardController::class, 'reporteSucursal'])->name('admin.reportes.sucursal');

    // Rutas de Administrador
    Route::get('admin/sucursales/create', [AdminController::class, 'createSucursal'])->name('admin.sucursales.create');
    Route::post('admin/sucursales', [AdminController::class, 'storeSucursal'])->name('admin.sucursales.store');
    Route::get('admin/sucursales/{id}/edit', [AdminController::class, 'editSucursal'])->name('admin.sucursales.edit');
    Route::put('admin/sucursales/{id}', [AdminController::class, 'updateSucursal'])->name('admin.sucursales.update');
    Route::get('admin/sucursales/{id}', [AdminController::class, 'showSucursal'])->name('admin.sucursales.show');
    Route::delete('admin/sucursales/{id}', [AdminController::class, 'destroySucursal'])->name('admin.sucursales.destroy');

    Route::get('admin/secretarias/create', [AdminController::class, 'createSecretaria'])->name('admin.secretarias.create');
    Route::post('admin/secretarias', [AdminController::class, 'storeSecretaria'])->name('admin.secretarias.store');
    Route::get('admin/secretarias/{id}/edit', [AdminController::class, 'editSecretaria'])->name('admin.secretarias.edit');
    Route::put('admin/secretarias/{id}', [AdminController::class, 'updateSecretaria'])->name('admin.secretarias.update');
    Route::delete('admin/secretarias/{id}', [AdminController::class, 'destroySecretaria'])->name('admin.secretarias.destroy');

    Route::delete('admin/barberos/{id}', [AdminController::class, 'destroyBarbero'])->name('admin.barberos.destroy');

    Route::resource('admin/servicios', ServicioController::class)->names('admin.servicios');
    Route::resource('admin/clientes', ClienteController::class)->names('admin.clientes');

    // Rutas de Secretaria
    Route::group(['prefix' => 'secretaria', 'as' => 'secretaria.'], function () {
        Route::get('dashboard', [DashboardController::class, 'secretaria'])->name('dashboard');

        // Clientes
        Route::get('clientes', [SecretariaController::class, 'indexClientes'])->name('clientes.index');
        Route::get('clientes/create', [SecretariaController::class, 'createCliente'])->name('clientes.create');
        Route::post('clientes', [SecretariaController::class, 'storeCliente'])->name('clientes.store');
        Route::get('clientes/{id}/edit', [SecretariaController::class, 'editCliente'])->name('clientes.edit');
        Route::put('clientes/{id}', [SecretariaController::class, 'updateCliente'])->name('clientes.update');
        Route::delete('clientes/{id}', [SecretariaController::class, 'destroyCliente'])->name('clientes.destroy');

        // Barberos
        Route::get('barberos', [SecretariaController::class, 'indexBarberos'])->name('barberos.index');
        Route::get('barberos/create', [SecretariaController::class, 'createBarbero'])->name('barberos.create');
        Route::post('barberos', [SecretariaController::class, 'storeBarbero'])->name('barberos.store');
        Route::get('barberos/{id}/edit', [SecretariaController::class, 'editBarbero'])->name('barberos.edit');
        Route::put('barberos/{id}', [SecretariaController::class, 'updateBarbero'])->name('barberos.update');
        Route::delete('barberos/{id}', [SecretariaController::class, 'destroyBarbero'])->name('barberos.destroy');

        // Ventas / Citas
        Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/export', [VentaController::class, 'export'])->name('ventas.export');
        Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');

        Route::get('citas/{id}/edit', [SecretariaController::class, 'editCita'])->name('citas.edit');
        Route::put('citas/{id}', [SecretariaController::class, 'updateCita'])->name('citas.update');
        Route::patch('citas/{id}/completar', [SecretariaController::class, 'completarCita'])->name('citas.completar');
        Route::patch('citas/{id}/cancelar', [SecretariaController::class, 'cancelarCita'])->name('citas.cancelar');
    });
});


