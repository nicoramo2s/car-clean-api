<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Nicolas User',
            'email' => 'nico@nico.com',
            'password' => 'password',
        ]);
        $clients = Client::factory(3)->create([
            'user_id' => $user->id,
        ]);
        $vehicles = Vehicle::factory(5)->create([
            'user_id' => $user->id,
            'client_id' => $clients->random()->id,
        ]);
    }
}
