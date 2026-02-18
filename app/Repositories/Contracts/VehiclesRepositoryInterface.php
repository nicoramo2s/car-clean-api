<?php

namespace App\Repositories\Contracts;

use App\DTOs\Vehicle\VehicleData;
use App\Models\Vehicle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface VehiclesRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10, int $page = 1): LengthAwarePaginator;

    public function getById(int $id): Vehicle;

    public function create(VehicleData $data): Vehicle;

    public function update(int $id, VehicleData $data): Vehicle;

    public function delete(int $id): void;
}
