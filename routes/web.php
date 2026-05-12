<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('admin/dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])->name('admin.dashboard');
    
    // Rutas de Administrador
    Route::get('admin/sucursales/create', [\App\Http\Controllers\AdminController::class, 'createSucursal'])->name('admin.sucursales.create');
    Route::post('admin/sucursales', [\App\Http\Controllers\AdminController::class, 'storeSucursal'])->name('admin.sucursales.store');
    Route::get('admin/sucursales/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editSucursal'])->name('admin.sucursales.edit');
    Route::put('admin/sucursales/{id}', [\App\Http\Controllers\AdminController::class, 'updateSucursal'])->name('admin.sucursales.update');
    Route::get('admin/sucursales/{id}', [\App\Http\Controllers\AdminController::class, 'showSucursal'])->name('admin.sucursales.show');
    Route::delete('admin/sucursales/{id}', [\App\Http\Controllers\AdminController::class, 'destroySucursal'])->name('admin.sucursales.destroy');
    
    Route::get('admin/secretarias/create', [\App\Http\Controllers\AdminController::class, 'createSecretaria'])->name('admin.secretarias.create');
    Route::post('admin/secretarias', [\App\Http\Controllers\AdminController::class, 'storeSecretaria'])->name('admin.secretarias.store');
    Route::get('admin/secretarias/{id}/edit', [\App\Http\Controllers\AdminController::class, 'editSecretaria'])->name('admin.secretarias.edit');
    Route::put('admin/secretarias/{id}', [\App\Http\Controllers\AdminController::class, 'updateSecretaria'])->name('admin.secretarias.update');
    Route::delete('admin/secretarias/{id}', [\App\Http\Controllers\AdminController::class, 'destroySecretaria'])->name('admin.secretarias.destroy');

    Route::delete('admin/barberos/{id}', [\App\Http\Controllers\AdminController::class, 'destroyBarbero'])->name('admin.barberos.destroy');

    Route::resource('admin/servicios', \App\Http\Controllers\ServicioController::class)->names('admin.servicios');
    Route::resource('admin/clientes', \App\Http\Controllers\ClienteController::class)->names('admin.clientes');

    // Rutas de Secretaria
    Route::group(['prefix' => 'secretaria', 'as' => 'secretaria.'], function () {
        Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'secretaria'])->name('dashboard');
        
        // Clientes
        Route::get('clientes', [\App\Http\Controllers\SecretariaController::class, 'indexClientes'])->name('clientes.index');
        Route::get('clientes/create', [\App\Http\Controllers\SecretariaController::class, 'createCliente'])->name('clientes.create');
        Route::post('clientes', [\App\Http\Controllers\SecretariaController::class, 'storeCliente'])->name('clientes.store');
        Route::get('clientes/{id}/edit', [\App\Http\Controllers\SecretariaController::class, 'editCliente'])->name('clientes.edit');
        Route::put('clientes/{id}', [\App\Http\Controllers\SecretariaController::class, 'updateCliente'])->name('clientes.update');
        Route::delete('clientes/{id}', [\App\Http\Controllers\SecretariaController::class, 'destroyCliente'])->name('clientes.destroy');
        
        // Barberos
        Route::get('barberos', [\App\Http\Controllers\SecretariaController::class, 'indexBarberos'])->name('barberos.index');
        Route::get('barberos/create', [\App\Http\Controllers\SecretariaController::class, 'createBarbero'])->name('barberos.create');
        Route::post('barberos', [\App\Http\Controllers\SecretariaController::class, 'storeBarbero'])->name('barberos.store');
        Route::get('barberos/{id}/edit', [\App\Http\Controllers\SecretariaController::class, 'editBarbero'])->name('barberos.edit');
        Route::put('barberos/{id}', [\App\Http\Controllers\SecretariaController::class, 'updateBarbero'])->name('barberos.update');
        Route::delete('barberos/{id}', [\App\Http\Controllers\SecretariaController::class, 'destroyBarbero'])->name('barberos.destroy');
        
        // Ventas / Citas
        Route::get('ventas', [\App\Http\Controllers\VentaController::class, 'index'])->name('ventas.index');
        Route::get('ventas/create', [\App\Http\Controllers\VentaController::class, 'create'])->name('ventas.create');
        Route::post('ventas', [\App\Http\Controllers\VentaController::class, 'store'])->name('ventas.store');
        
        Route::get('citas/{id}/edit', [\App\Http\Controllers\SecretariaController::class, 'editCita'])->name('citas.edit');
        Route::put('citas/{id}', [\App\Http\Controllers\SecretariaController::class, 'updateCita'])->name('citas.update');
        Route::patch('citas/{id}/completar', [\App\Http\Controllers\SecretariaController::class, 'completarCita'])->name('citas.completar');
        Route::patch('citas/{id}/cancelar', [\App\Http\Controllers\SecretariaController::class, 'cancelarCita'])->name('citas.cancelar');
    });
});

require __DIR__.'/settings.php';
