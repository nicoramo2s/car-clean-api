<?php

namespace Tests\app\Http\Controller\VehicleControllerTest;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function store_creates_a_new_vehicle_with_valid_data(): void
    {
        // Prepare
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        $vehicleData = [
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2022,
            'color' => 'Blue',
            'license_plate' => 'ABC1234',
            'client_id' => $client->id,
        ];

        // Execute
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/vehicles', $vehicleData);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('vehicles', [
            'license_plate' => 'ABC1234',
            'client_id' => $client->id,
        ]);
        $response->assertJsonFragment([
            'license_plate' => 'ABC1234',
        ]);
    }

    #[Test]
    public function store_returns_validation_errors_for_invalid_data(): void
    {
        // Prepare
        $user = User::factory()->create();

        // Execute (missing required fields)
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/vehicles', []);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['brand', 'model', 'license_plate', 'client_id']);
    }

    #[Test]
    public function store_prevents_creating_vehicle_for_client_belonging_to_another_user(): void
    {
        // Prepare
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);

        $vehicleData = [
            'brand' => 'Honda',
            'model' => 'Civic',
            'license_plate' => 'XYZ5678',
            'client_id' => $otherClient->id,
        ];

        // Execute
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/vehicles', $vehicleData);

        // Assert
        // The validator will fail because 'exists:clients,id' with GlobalScope on Client
        // will not find the client if it belongs to another user.
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['client_id']);
    }
}
