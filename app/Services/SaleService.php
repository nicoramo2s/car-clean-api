<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Sales\SaleData;
use App\Models\Sale;
use App\Repositories\Contracts\SaleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SaleService
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
    ) {}

    public function getAll(array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $this->saleRepository->getAll($filters, $perPage, $page);
    }

    public function getById(int $id): Sale
    {
        return $this->saleRepository->getById($id);
    }

    public function create(SaleData $data): Sale
    {
        return $this->saleRepository->create($data);
    }

    public function update(int $id, SaleData $data): Sale
    {
        return $this->saleRepository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->saleRepository->delete($id);
    }
}
