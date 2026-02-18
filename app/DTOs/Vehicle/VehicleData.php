<?php

namespace App\DTOs\Vehicle;

use App\Models\Vehicle;

class VehicleData
{
    public function __construct(
        public readonly string $brand,
        public readonly string $model,
        public readonly ?int $year,
        public readonly ?string $color,
        public readonly ?string $licensePlate,
        public readonly int $client_id,
    ) {}

    public static function fromArray(array $data, ?Vehicle $vehicle = null): self
    {
        return new self(
            brand: $data['brand'] ?? $vehicle?->brand,
            model: $data['model'] ?? $vehicle?->model,
            year: $data['year'] ?? $vehicle?->year,
            color: $data['color'] ?? $vehicle?->color,
            licensePlate: $data['license_plate'] ?? $vehicle?->license_plate,
            client_id: $data['client_id'] ?? $vehicle?->client_id,
        );
    }

    public function toArray(): array
    {
        return [
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'color' => $this->color,
            'license_plate' => $this->licensePlate,
            'client_id' => $this->client_id,
        ];
    }
}
