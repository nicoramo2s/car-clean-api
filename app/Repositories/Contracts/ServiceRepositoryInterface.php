<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Sales\ServiceData;
use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator;

    public function getById(int $id): ?Service;

    public function create(ServiceData $data): Service;

    public function update(int $id, ServiceData $data): Service;

    public function delete(int $id): bool;
}
