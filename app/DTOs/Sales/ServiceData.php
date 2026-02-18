<?php

declare(strict_types=1);

namespace App\DTOs\Sales;

class ServiceData
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly string $description,
        public readonly ?bool $active = true,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
            'active' => $this->active,
        ];
    }

    /**
     * Summary of fromArray
     *
     * @param  array{name: string, price: float, description: string, active: bool}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['price'],
            $data['description'],
            $data['active'] ?? true,
        );
    }
}
