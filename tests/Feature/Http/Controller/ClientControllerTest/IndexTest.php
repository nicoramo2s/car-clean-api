<?php

namespace Tests\app\Http\Controller\ClientControllerTest;

use App\Models\Client;
use App\Models\User;
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
        $this->withoutExceptionHandling();
    }

    #[Test]
    public function index_returns_only_authenticated_user_clients(): void
    {
        // Prepare
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        Client::factory(3)->create([
            'user_id' => $user2->id,
        ]);

        // Execute
        $response = $this->actingAs($user, 'api')->get('/api/v1/clients');

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [],
            'meta',
            'links',
        ]);
    }

    #[Test]
    public function index_response_clients_paginated_and_status_200(): void
    {
        $this->withoutExceptionHandling();
        // Prepare
        $user = User::factory()->create();
        Client::factory(3)->create([
            'user_id' => $user->id,
        ]);

        // Execute
        $response = $this->actingAs($user, 'api')->get('/api/v1/clients');

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ],
            'meta',
            'links',
        ]);
    }

    #[Test]
    public function index_response_clients_find_by_search_param_and_response_200_and_paginated()
    {
        // prepare
        $user = User::factory()->create();
        Client::factory(3)->create([
            'user_id' => $user->id,
        ]);
        Client::factory(1)->create([
            'user_id' => $user->id,
            'name' => 'test',
        ]);

        // execute
        $response = $this->actingAs($user, 'api')->get('/api/v1/clients?search=test');

        // assert
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment([
            'name' => 'test',
        ]);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'created_at',
                    'updated_at',
                ],
            ],
            'meta',
            'links',
        ]);
    }
}
