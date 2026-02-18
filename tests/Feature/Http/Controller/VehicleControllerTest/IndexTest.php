<?php

namespace Tests\app\Http\Controller\VehicleControllerTest;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function index_returns_only_authenticated_user_vehicles(): void
    {
        // Prepare
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $client = Client::factory()->create(['user_id' => $user->id]);
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);

        Vehicle::factory(3)->create(['client_id' => $client->id]);
        Vehicle::factory(2)->create(['client_id' => $otherClient->id]);

        // Execute
        $response = $this->actingAs($user, 'api')->get('/api/v1/vehicles');

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(3, 'data');
    }

    #[Test]
    public function index_returns_paginated_vehicles(): void
    {
        // Prepare
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        Vehicle::factory(20)->create(['client_id' => $client->id]);

        // Execute
        $response = $this->actingAs($user, 'api')->get('/api/v1/vehicles?per_page=10');

        // Assert
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
            ],
        ]);
    }

    #[Test]
    public function index_can_search_by_brand_model_or_license_plate(): void
    {
        // Prepare
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Vehicle::factory()->create([
            'client_id' => $client->id,
            'brand' => 'UniqueBrandX',
            'model' => 'OtherModel',
            'license_plate' => 'PLATE1',
        ]);

        Vehicle::factory()->create([
            'client_id' => $client->id,
            'brand' => 'OtherBrand',
            'model' => 'UniqueModelY',
            'license_plate' => 'PLATE2',
        ]);

        Vehicle::factory()->create([
            'client_id' => $client->id,
            'brand' => 'AnotherBrand',
            'model' => 'AnotherModel',
            'license_plate' => 'UNIQUEZ',
        ]);

        // Execute & Assert for Brand
        $this->actingAs($user, 'api')
            ->get('/api/v1/vehicles?search=UniqueBrandX')
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['brand' => 'UniqueBrandX']);

        // Execute & Assert for Model
        $this->actingAs($user, 'api')
            ->get('/api/v1/vehicles?search=UniqueModelY')
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['model' => 'UniqueModelY']);

        // Execute & Assert for License Plate
        $this->actingAs($user, 'api')
            ->get('/api/v1/vehicles?search=UNIQUEZ')
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['license_plate' => 'UNIQUEZ']);
    }

    #[Test]
    public function index_returns_401_for_unauthenticated_user(): void
    {
        $this->getJson('/api/v1/vehicles')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
