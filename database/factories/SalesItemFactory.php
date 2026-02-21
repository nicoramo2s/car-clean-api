<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesItem>
 */
class SalesItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);

        return [
            'sale_id' => Sale::factory(),
            'service_id' => Service::factory(),
            'unit_price' => $unitPrice,
            'total' => round($unitPrice, 2),
        ];
    }
}
