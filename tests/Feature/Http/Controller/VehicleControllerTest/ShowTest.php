<?php

namespace Tests\app\Http\Controller\VehicleControllerTest;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function show_returns_vehicle_by_id(): void
    {
        // Prepare
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['client_id' => $client->id]);

        // Execute
        $response = $this->actingAs($user, 'api')->get("/api/v1/vehicles/{$vehicle->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $vehicle->id,
            'brand' => $vehicle->brand,
        ]);
    }

    #[Test]
    public function show_returns_404_for_non_existent_vehicle(): void
    {
        // Prepare
        $user = User::factory()->create();

        // Execute
        $response = $this->actingAs($user, 'api')->get('/api/v1/vehicles/999');

        // Assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function show_returns_404_for_vehicle_belonging_to_another_user(): void
    {
        // Prepare
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);
        $otherVehicle = Vehicle::factory()->create(['client_id' => $otherClient->id]);

        // Execute
        $response = $this->actingAs($user, 'api')->get("/api/v1/vehicles/{$otherVehicle->id}");

        // Assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
