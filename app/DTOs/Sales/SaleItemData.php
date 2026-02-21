<?php

declare(strict_types=1);

namespace App\DTOs\Sales;

class SaleItemData
{
    public function __construct(
        public readonly int $serviceId,
    ) {}

    /**
     * @param  array{
     *     service_id: int
     * }  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            serviceId: (int) $data['service_id'],
        );
    }

    /**
     * @return array{
     *     service_id: int
     * }
     */
    public function toArray(): array
    {
        return [
            'service_id' => $this->serviceId,
        ];
    }
}
