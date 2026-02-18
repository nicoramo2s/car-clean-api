<?php

namespace Tests\app\Http\Controller\VehicleControllerTest;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function destroy_soft_deletes_a_vehicle(): void
    {
        // Prepare
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['client_id' => $client->id]);

        // Execute
        $response = $this->actingAs($user, 'api')->delete("/api/v1/vehicles/{$vehicle->id}");

        // Assert
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('vehicles', [
            'id' => $vehicle->id,
        ]);
    }

    #[Test]
    public function destroy_returns_404_for_vehicle_belonging_to_another_user(): void
    {
        // Prepare
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);
        $otherVehicle = Vehicle::factory()->create(['client_id' => $otherClient->id]);

        // Execute
        $response = $this->actingAs($user, 'api')->delete("/api/v1/vehicles/{$otherVehicle->id}");

        // Assert
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertDatabaseHas('vehicles', [
            'id' => $otherVehicle->id,
            'deleted_at' => null,
        ]);
    }
}
