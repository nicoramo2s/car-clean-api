<?php

namespace Tests\app\Http\Controller\VehicleControllerTest;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DebugScopeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function scope_is_applied(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherClient = Client::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user, 'api');

        $exists = Client::where('id', $otherClient->id)->exists();

        $this->assertFalse($exists, 'Client belonging to another user should not be visible');
    }
}
