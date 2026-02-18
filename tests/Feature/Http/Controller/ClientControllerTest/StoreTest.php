<?php

namespace Tests\app\Http\Controller\ClientControllerTest;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->withoutExceptionHandling();
    }

    #[Test]
    public function store_response_json_structure_and_status_created(): void
    {
        // Arrange
        $user = User::factory()->create();
        $client = Client::factory()->make();

        // Act
        $response = $this->actingAs($user, 'api')->post('/api/v1/clients', $client->toArray());

        // Assert
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [],
        ]);
    }

    #[Test]
    public function store_response_validation_errors_when_fields_are_empty_and_status_bad_request(): void
    {
        // Arrange
        $user = User::factory()->create();
        $client = Client::factory()->make([
            'name' => '',
            'email' => '',
            'phone' => '',
        ]);

        // Act
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/clients', $client->toArray());

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'phone',
            ]);
    }
}
