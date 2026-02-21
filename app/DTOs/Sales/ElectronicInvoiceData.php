<?php

declare(strict_types=1);

namespace App\DTOs\Sales;

class ElectronicInvoiceData
{
    /**
     * @param  array<string, mixed>|null  $arcaResponse
     */
    public function __construct(
        public readonly int $saleId,
        public readonly int $userId,
        public readonly int $pointOfSale,
        public readonly string $status = 'pending',
        public readonly ?string $cae = null,
        public readonly ?string $caeDueDate = null,
        public readonly ?int $voucherNumber = null,
        public readonly ?array $arcaResponse = null,
    ) {}

    /**
     * @return array{
     *     sale_id: int,
     *     user_id: int,
     *     point_of_sale: int,
     *     status: string,
     *     cae: string|null,
     *     cae_due_date: string|null,
     *     voucher_number: int|null,
     *     arca_response: array<string, mixed>|null
     * }
     */
    public function toArray(): array
    {
        return [
            'sale_id' => $this->saleId,
            'user_id' => $this->userId,
            'point_of_sale' => $this->pointOfSale,
            'status' => $this->status,
            'cae' => $this->cae,
            'cae_due_date' => $this->caeDueDate,
            'voucher_number' => $this->voucherNumber,
            'arca_response' => $this->arcaResponse,
        ];
    }
}
