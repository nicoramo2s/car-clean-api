<?php

declare(strict_types=1);

namespace App\DTOs\Sales;

class SaleData
{
    /**
     * @param  array<SaleItemData>  $items
     */
    public function __construct(
        public readonly int $clientId,
        public readonly int $vehicleId,
        public readonly string $paymentMethod,
        public readonly bool $shouldInvoice,
        public readonly array $items,
        public readonly ?string $paidAt = null,
        public readonly ?int $pointOfSale = null,
    ) {}

    /**
     * @param  array{
     *     client_id: int,
     *     vehicle_id: int,
     *     payment_method: string,
     *     should_invoice?: bool,
     *     paid_at?: string|null,
     *     point_of_sale?: int|null,
     *     items?: array<int, array{
     *         service_id: int,
     *         unit_price: numeric-string|int|float
     *     }>
     * }  $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array<SaleItemData> $items */
        $items = array_map(
            static fn (array $item): SaleItemData => SaleItemData::fromArray($item),
            $data['items'] ?? [],
        );

        return new self(
            clientId: (int) $data['client_id'],
            vehicleId: (int) $data['vehicle_id'],
            paymentMethod: (string) $data['payment_method'],
            shouldInvoice: (bool) ($data['should_invoice'] ?? false),
            items: $items,
            paidAt: isset($data['paid_at']) ? (string) $data['paid_at'] : null,
            pointOfSale: isset($data['point_of_sale']) ? (int) $data['point_of_sale'] : null,
        );
    }

    /**
     * @return array{
     *     client_id: int,
     *     vehicle_id: int,
     *     payment_method: string,
     *     should_invoice: bool,
     *     paid_at: string|null
     * }
     */
    public function toSaleAttributes(): array
    {
        return [
            'client_id' => $this->clientId,
            'vehicle_id' => $this->vehicleId,
            'payment_method' => $this->paymentMethod,
            'should_invoice' => $this->shouldInvoice,
            'paid_at' => $this->paidAt,
        ];
    }
}
