<?php

use App\Models\sucursales;
use App\Models\Cliente;
use App\Models\Barbero;
use App\Models\Servicio;
use App\Models\Cita;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create a single appointment with multiple services via API and consolidates properties', function () {
    putenv('PERSONAL_API_KEY=');

    $sucursal = sucursales::create(['nombre' => 'Test Branch']);
    $barbero = Barbero::create(['nombre' => 'John Barber', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente = Cliente::create(['nombre' => 'Jane Client', 'correo' => 'jane@example.com']);
    
    // Create two services
    $servicio1 = Servicio::create(['nombre' => 'Corte', 'duracion_minutos' => 30, 'precio' => 150.00]);
    $servicio2 = Servicio::create(['nombre' => 'Barba', 'duracion_minutos' => 15, 'precio' => 80.00]);

    // Send POST request with array of services
    $response = $this->postJson('/api/citas', [
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicios' => [$servicio1->id, $servicio2->id],
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseCount('citas', 1);

    $cita = Cita::first();

    // Verify consolidated attributes from virtual accessor
    expect($cita->servicio->nombre)->toBe('Corte, Barba');
    expect((float)$cita->servicio->precio)->toBe(230.0);
    expect($cita->servicio->duracion_minutos)->toBe(45);
    expect($cita->hora_fin)->toBe('10:45'); // 10:00 + 45 min
});

test('appointments on the same barbero for different clients at overlapping times will clash', function () {
    putenv('PERSONAL_API_KEY=');

    $sucursal = sucursales::create(['nombre' => 'Test Branch']);
    $barbero = Barbero::create(['nombre' => 'John Barber', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente1 = Cliente::create(['nombre' => 'Client One', 'correo' => 'one@example.com']);
    $cliente2 = Cliente::create(['nombre' => 'Client Two', 'correo' => 'two@example.com']);
    $servicio = Servicio::create(['nombre' => 'Corte', 'duracion_minutos' => 30, 'precio' => 150.00]);

    // Create first appointment: Client One, 10:00 - 10:30
    $cita1 = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente1->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'hora_fin' => '10:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);
    $cita1->servicios()->attach($servicio->id);

    // Try to create overlapping appointment: Client Two, 10:15 - 10:45 (overlaps)
    $response = $this->postJson('/api/citas', [
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente2->id,
        'servicios' => [$servicio->id],
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:15',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);

    $response->assertStatus(409);
});

test('appointments on the same barbero for the same client at overlapping times will not clash', function () {
    putenv('PERSONAL_API_KEY=');

    $sucursal = sucursales::create(['nombre' => 'Test Branch']);
    $barbero = Barbero::create(['nombre' => 'John Barber', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente = Cliente::create(['nombre' => 'Jane Client', 'correo' => 'jane@example.com']);
    $servicio = Servicio::create(['nombre' => 'Corte', 'duracion_minutos' => 30, 'precio' => 150.00]);

    // Create first appointment: Jane Client, 10:00 - 10:30
    $cita1 = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'hora_fin' => '10:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);
    $cita1->servicios()->attach($servicio->id);

    // Try to create overlapping appointment for the same client: Jane Client, 10:15 - 10:45 (overlaps, but same client)
    $response = $this->postJson('/api/citas', [
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente->id,
        'servicios' => [$servicio->id],
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:15',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);

    $response->assertStatus(201);
});

test('appointments on the same barbero that only touch boundaries will not clash', function () {
    putenv('PERSONAL_API_KEY=');

    $sucursal = sucursales::create(['nombre' => 'Test Branch']);
    $barbero = Barbero::create(['nombre' => 'John Barber', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente1 = Cliente::create(['nombre' => 'Client One', 'correo' => 'one@example.com']);
    $cliente2 = Cliente::create(['nombre' => 'Client Two', 'correo' => 'two@example.com']);
    $servicio = Servicio::create(['nombre' => 'Corte', 'duracion_minutos' => 30, 'precio' => 150.00]);

    // Create first appointment: Client One, 10:00 - 10:30
    $cita1 = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente1->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'hora_fin' => '10:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);
    $cita1->servicios()->attach($servicio->id);

    // Try to create touching appointment: Client Two, 10:30 - 11:00 (starts exactly when first ends)
    $response = $this->postJson('/api/citas', [
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente2->id,
        'servicios' => [$servicio->id],
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);

    $response->assertStatus(201);
});

test('updating an appointment to overlap with another client will clash, but same client is allowed', function () {
    putenv('PERSONAL_API_KEY=');

    $sucursal = sucursales::create(['nombre' => 'Test Branch']);
    $barbero = Barbero::create(['nombre' => 'John Barber', 'hora_entrada' => '09:00', 'hora_salida' => '17:00', 'sucursal_id' => $sucursal->id]);
    $cliente1 = Cliente::create(['nombre' => 'Client One', 'correo' => 'one@example.com']);
    $cliente2 = Cliente::create(['nombre' => 'Client Two', 'correo' => 'two@example.com']);
    $servicio = Servicio::create(['nombre' => 'Corte', 'duracion_minutos' => 30, 'precio' => 150.00]);

    // Create appointment 1: Client One, 10:00 - 10:30
    $cita1 = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente1->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '10:00',
        'hora_fin' => '10:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);
    $cita1->servicios()->attach($servicio->id);

    // Create appointment 2: Client Two, 11:00 - 11:30
    $cita2 = Cita::create([
        'barbero_id' => $barbero->id,
        'cliente_id' => $cliente2->id,
        'servicio_id' => $servicio->id,
        'fecha' => '2026-06-01',
        'hora_inicio' => '11:00',
        'hora_fin' => '11:30',
        'tipo_atencion' => 'con_cita',
        'estado' => 'pendiente',
    ]);
    $cita2->servicios()->attach($servicio->id);

    // Try to update appointment 2 to overlap with appointment 1: Client Two, 10:15 - 10:45 (clashes)
    $response = $this->putJson("/api/citas/{$cita2->id}", [
        'hora_inicio' => '10:15',
    ]);
    $response->assertStatus(409);

    // Try to update appointment 2 to overlap but change client to Client One (should be allowed, same client)
    $response = $this->putJson("/api/citas/{$cita2->id}", [
        'cliente_id' => $cliente1->id,
        'hora_inicio' => '10:15',
    ]);
    $response->assertStatus(200);
});


