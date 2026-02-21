<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Sales\SaleData;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;

interface SaleRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator;

    public function getById(int $id): Sale;

    public function create(SaleData $saleData): Sale;

    public function update(int $id, SaleData $saleData): Sale;

    public function delete(int $id): void;
}
