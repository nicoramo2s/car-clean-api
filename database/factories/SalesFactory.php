<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SalesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 5000);

        return [
            'user_id' => User::factory(),
            'client_id' => function (array $attributes): int {
                return Client::factory()->create([
                    'user_id' => $attributes['user_id'],
                ])->id;
            },
            'vehicle_id' => function (array $attributes): int {
                return Vehicle::factory()->create([
                    'user_id' => $attributes['user_id'],
                    'client_id' => $attributes['client_id'],
                ])->id;
            },
            'subtotal' => $subtotal,
            'total' => $subtotal,
            'payment_method' => $this->faker->randomElement(['cash', 'transfer', 'card']),
            'paid_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'should_invoice' => $this->faker->boolean(),
        ];
    }
}
