<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Vehicle\VehicleData;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehiclesRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class VehicleRepository implements VehiclesRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Vehicle::where('user_id', auth()->id())
            ->when(
                filled($filters['search'] ?? null),
                function (Builder $query) use ($filters): void {
                    $search = strtolower((string) $filters['search']);

                    $query->where(function (Builder $nestedQuery) use ($search): void {
                        $nestedQuery
                            ->whereRaw('LOWER(brand) like ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(model) like ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(license_plate) like ?', ["%{$search}%"]);
                    });
                }
            )
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->appends($filters);
    }

    public function getById(int $id): Vehicle
    {
        return Vehicle::where('user_id', auth()->id())
            ->findOrFail($id);
    }

    public function create(VehicleData $data): Vehicle
    {
        return DB::transaction(function () use ($data): Vehicle {

            $payload = $data->toArray();
            $payload['user_id'] = auth()->id();

            return Vehicle::create($payload);
        });
    }

    public function update(int $id, VehicleData $data): Vehicle
    {
        return DB::transaction(function () use ($id, $data): Vehicle {

            $vehicle = Vehicle::where('user_id', auth()->id())
                ->findOrFail($id);

            $vehicle->update($data->toArray());

            return $vehicle;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $vehicle = Vehicle::where('user_id', auth()->id())
                ->findOrFail($id);
            $vehicle->delete();
        });
    }
}
