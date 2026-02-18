<?php

namespace Tests\app\Http\Controller\VehicleControllerTest;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function update_modifies_an_existing_vehicle_with_valid_data(): void
    {
        // Prepare
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['client_id' => $client->id]);

        $updateData = [
            'brand' => 'UpdatedBrand',
            'model' => 'UpdatedModel',
            'year' => 2023,
            'color' => 'Red',
        ];

        // Execute
        $response = $this->actingAs($user, 'api')->putJson("/api/v1/vehicles/{$vehicle->id}", $updateData);

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'brand' => 'UpdatedBrand',
            'model' => 'UpdatedModel',
        ]);
        $response->assertJsonFragment([
            'brand' => 'UpdatedBrand',
            'model' => 'UpdatedModel',
        ]);
    }

    #[Test]
    public function update_returns_404_for_vehicle_belonging_to_another_user(): void
    {
        // Prepare
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);
        $otherVehicle = Vehicle::factory()->create(['client_id' => $otherClient->id]);

        $updateData = [
            'brand' => 'HackerBrand',
            'model' => 'HackerModel',
        ];

        // Execute
        $response = $this->actingAs($user, 'api')->putJson("/api/v1/vehicles/{$otherVehicle->id}", $updateData);

        // Assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function update_returns_validation_errors_for_invalid_data(): void
    {
        // Prepare
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['client_id' => $client->id]);

        // Execute (missing required fields)
        $response = $this->actingAs($user, 'api')->putJson("/api/v1/vehicles/{$vehicle->id}", []);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['brand', 'model']);
    }
}
