<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Sales\ServiceData;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceService
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function getAll(array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $this->serviceRepository->getAll($filters, $perPage, $page);
    }

    public function getById(int $id): ?Service
    {
        return $this->serviceRepository->getById($id);
    }

    public function create(ServiceData $data): Service
    {
        return $this->serviceRepository->create($data);
    }

    public function update(int $id, ServiceData $data): Service
    {
        return $this->serviceRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->serviceRepository->delete($id);
    }
}
