<?php

use App\Models\sucursales;
use App\Models\Cliente;
use App\Models\Barbero;
use App\Models\Servicio;
use App\Models\Cita;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaAgendada;
use App\Mail\CitaCancelada;
use App\Mail\CitaCompletada;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('sends email when booking is con_cita', function () {
    Mail::fake();

    $sucursal = sucursales::create(['nombre' => 'Sucursal']);
    $barbero = Barbero::create(['nombre' => 'Barbero', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente = Cliente::create(['nombre' => 'Client', 'correo' => 'client@example.com']);
    $servicio = Servicio::create(['nombre' => 'Service', 'duracion_minutos' => 30, 'precio' => 100]);

    $cita = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'hora_fin' => '10:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente'
    ]);

    Mail::assertSent(CitaAgendada::class, function ($mail) use ($cliente) {
        return $mail->hasTo($cliente->correo);
    });
});

test('does not send booking email when booking is sin_cita', function () {
    Mail::fake();

    $sucursal = sucursales::create(['nombre' => 'Sucursal']);
    $barbero = Barbero::create(['nombre' => 'Barbero', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente = Cliente::create(['nombre' => 'Client', 'correo' => 'client@example.com']);
    $servicio = Servicio::create(['nombre' => 'Service', 'duracion_minutos' => 30, 'precio' => 100]);

    $cita = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'hora_fin' => '10:30',
        'tipo_atencion' => 'sin_cita',
        'estado' => 'pendiente'
    ]);

    Mail::assertNotSent(CitaAgendada::class);
});

test('sends cancellation and completion emails when status changes', function () {
    Mail::fake();

    $sucursal = sucursales::create(['nombre' => 'Sucursal']);
    $barbero = Barbero::create(['nombre' => 'Barbero', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente = Cliente::create(['nombre' => 'Client', 'correo' => 'client@example.com']);
    $servicio = Servicio::create(['nombre' => 'Service', 'duracion_minutos' => 30, 'precio' => 100]);

    $cita = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'hora_fin' => '10:30',
        'tipo_atencion' => 'sin_cita',
        'estado' => 'pendiente'
    ]);

    // Complete the cita (should trigger CitaCompletada for either type_atencion)
    $cita->update(['estado' => 'completada', 'metodo_pago' => 'tarjeta']);

    Mail::assertSent(CitaCompletada::class, function ($mail) use ($cliente) {
        return $mail->hasTo($cliente->correo);
    });

    // Reset Mail Fake to verify cancellation
    Mail::fake();

    $cita2 = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '11:00',
        'hora_fin' => '11:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente'
    ]);

    // Cancel the cita (con_cita)
    $cita2->update(['estado' => 'cancelada']);

    Mail::assertSent(CitaCancelada::class, function ($mail) use ($cliente) {
        return $mail->hasTo($cliente->correo);
    });

    // Reset Mail Fake to verify cancellation for sin_cita
    Mail::fake();

    $cita3 = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '12:00',
        'hora_fin' => '12:30',
        'tipo_atencion' => 'sin_cita',
        'estado' => 'pendiente'
    ]);

    // Cancel the cita (sin_cita)
    $cita3->update(['estado' => 'cancelada']);

    Mail::assertSent(CitaCancelada::class, function ($mail) use ($cliente) {
        return $mail->hasTo($cliente->correo);
    });
});
