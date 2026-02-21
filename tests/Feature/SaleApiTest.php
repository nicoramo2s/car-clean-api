<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Service;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

it('creates a sale with items', function (): void {
    $user = User::factory()->create();
    $client = Client::query()->create([
        'user_id' => $user->id,
        'name' => 'Cliente Ventas',
        'email' => 'cliente-ventas@example.com',
        'phone' => '+5491111111111',
    ]);
    $vehicle = Vehicle::query()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'brand' => 'Ford',
        'model' => 'Fiesta',
        'year' => 2020,
        'color' => 'Azul',
        'license_plate' => 'AA111BB',
    ]);
    $service = Service::query()->create([
        'user_id' => $user->id,
        'name' => 'Lavado premium',
        'description' => 'Lavado completo',
        'price' => 15000,
        'active' => true,
    ]);

    $payload = [
        'client_id' => $client->id,
        'vehicle_id' => $vehicle->id,
        'payment_method' => 'cash',
        'should_invoice' => true,
        'point_of_sale' => 5,
        'items' => [
            [
                'service_id' => $service->id,
                'unit_price' => 15000,
            ],
        ],
    ];

    $response = $this->actingAs($user, 'api')->postJson('/api/v1/sales', $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonPath('data.should_invoice', true);
    $response->assertJsonCount(1, 'data.items');
    $this->assertDatabaseHas('sales', [
        'user_id' => $user->id,
        'client_id' => $client->id,
        'vehicle_id' => $vehicle->id,
        'payment_method' => 'cash',
        'subtotal' => 15000,
        'total' => 15000,
    ]);
    $this->assertDatabaseHas('sales_items', [
        'service_id' => $service->id,
        'unit_price' => 15000,
        'total' => 15000,
    ]);
    $this->assertDatabaseHas('electronic_invoices', [
        'user_id' => $user->id,
        'point_of_sale' => 5,
        'status' => 'pending',
    ]);
});

it('lists only authenticated user sales', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $client = Client::query()->create([
        'user_id' => $user->id,
        'name' => 'Cliente Uno',
        'email' => 'cliente-uno@example.com',
        'phone' => '+5491122222222',
    ]);
    $vehicle = Vehicle::query()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2021,
        'color' => 'Negro',
        'license_plate' => 'BB222CC',
    ]);

    $otherClient = Client::query()->create([
        'user_id' => $otherUser->id,
        'name' => 'Cliente Dos',
        'email' => 'cliente-dos@example.com',
        'phone' => '+5491133333333',
    ]);
    $otherVehicle = Vehicle::query()->create([
        'user_id' => $otherUser->id,
        'client_id' => $otherClient->id,
        'brand' => 'Honda',
        'model' => 'Civic',
        'year' => 2019,
        'color' => 'Blanco',
        'license_plate' => 'CC333DD',
    ]);

    $sale = Sale::query()->create([
        'user_id' => $user->id,
        'client_id' => $client->id,
        'vehicle_id' => $vehicle->id,
        'subtotal' => 10000,
        'total' => 10000,
        'payment_method' => 'cash',
        'paid_at' => now(),
        'should_invoice' => false,
    ]);

    Sale::query()->create([
        'user_id' => $otherUser->id,
        'client_id' => $otherClient->id,
        'vehicle_id' => $otherVehicle->id,
        'subtotal' => 20000,
        'total' => 20000,
        'payment_method' => 'card',
        'paid_at' => now(),
        'should_invoice' => false,
    ]);

    SalesItem::query()->create([
        'sale_id' => $sale->id,
        'service_id' => Service::query()->create([
            'user_id' => $user->id,
            'name' => 'Aspirado',
            'description' => null,
            'price' => 10000,
            'active' => true,
        ])->id,
        'unit_price' => 10000,
        'total' => 10000,
    ]);

    $response = $this->actingAs($user, 'api')->getJson('/api/v1/sales');

    $response->assertSuccessful();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $sale->id);
    $response->assertJsonStructure([
        'success',
        'message',
        'data',
        'meta',
        'links',
    ]);
});
