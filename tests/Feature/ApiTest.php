<?php

use App\Models\sucursales;
use App\Models\Cliente;
use App\Models\Barbero;
use App\Models\Servicio;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('api returns 401 unauthorized when wrong API key is provided and key is configured', function () {
    // Set config/env temporarily
    config(['app.personal_api_key' => 'secret_key']);
    // Actually our middleware checks env('PERSONAL_API_KEY') directly, so let's mock putenv or config
    // To make it fully testable via env(), we can mock env helper or just set the env var using putenv:
    putenv('PERSONAL_API_KEY=test_api_key');

    $response = $this->getJson('/api/sucursales');
    $response->assertStatus(401);

    $response = $this->withHeaders(['X-API-KEY' => 'wrong_key'])->getJson('/api/sucursales');
    $response->assertStatus(401);

    $response = $this->withHeaders(['X-API-KEY' => 'test_api_key'])->getJson('/api/sucursales');
    $response->assertStatus(200);

    // Reset env
    putenv('PERSONAL_API_KEY=');
});

test('can perform CRUD operations on sucursales via API', function () {
    // Ensure no API key required for this test
    putenv('PERSONAL_API_KEY=');

    // Create
    $response = $this->postJson('/api/sucursales', [
        'nombre' => 'Sucursal Central',
        'direccion' => 'Av. Principal 123',
        'telefono' => '1234567890',
        'dias_apertura' => ['lunes', 'martes'],
        'hora_apertura' => '09:00',
        'hora_cierre' => '18:00',
    ]);
    $response->assertStatus(201);
    $response->assertJsonFragment(['nombre' => 'Sucursal Central']);

    $sucursalId = $response->json('id');

    // List
    $response = $this->getJson('/api/sucursales');
    $response->assertStatus(200);
    $response->assertJsonCount(1);

    // Show
    $response = $this->getJson("/api/sucursales/{$sucursalId}");
    $response->assertStatus(200);
    $response->assertJsonFragment(['nombre' => 'Sucursal Central']);

    // Update
    $response = $this->putJson("/api/sucursales/{$sucursalId}", [
        'nombre' => 'Sucursal Central Actualizada',
    ]);
    $response->assertStatus(200);
    $response->assertJsonFragment(['nombre' => 'Sucursal Central Actualizada']);

    // Delete
    $response = $this->deleteJson("/api/sucursales/{$sucursalId}");
    $response->assertStatus(200);

    $this->assertDatabaseMissing('sucursales', ['id' => $sucursalId]);
});

test('can perform CRUD operations on clientes via API', function () {
    putenv('PERSONAL_API_KEY=');

    // Create
    $response = $this->postJson('/api/clientes', [
        'nombre' => 'John Doe',
        'telefono' => '9876543210',
        'correo' => 'john@example.com',
    ]);
    $response->assertStatus(201);
    $response->assertJsonFragment(['nombre' => 'John Doe']);

    $clienteId = $response->json('id');

    // Show
    $response = $this->getJson("/api/clientes/{$clienteId}");
    $response->assertStatus(200);

    // Update
    $response = $this->putJson("/api/clientes/{$clienteId}", [
        'nombre' => 'John Smith',
    ]);
    $response->assertStatus(200);
    $response->assertJsonFragment(['nombre' => 'John Smith']);

    // Delete
    $response = $this->deleteJson("/api/clientes/{$clienteId}");
    $response->assertStatus(200);

    $this->assertDatabaseMissing('clientes', ['id' => $clienteId]);
});
