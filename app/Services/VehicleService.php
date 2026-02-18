<?php

namespace App\Services;

use App\DTOs\Vehicle\VehicleData;
use App\Models\Vehicle;
use App\Repositories\Contracts\VehiclesRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VehicleService
{
    private const CACHE_VERSION_KEY = 'vehicles:cache_version';

    private const LIST_TTL_MINUTES = 5;

    private const ITEM_TTL_MINUTES = 10;

    public function __construct(
        private VehiclesRepositoryInterface $vehicleRepository,
    ) {}

    public function getAll(Request $request): LengthAwarePaginator
    {
        $filters = $request->only(['search']);
        $perPage = $request->integer('per_page', 15);
        $page = $request->integer('page', 1);
        $cacheKey = $this->buildListCacheKey($filters, $perPage, $page);

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(self::LIST_TTL_MINUTES),
            fn (): LengthAwarePaginator => $this->vehicleRepository->getAll($filters, $perPage, $page),
        );
    }

    public function getById(int $id): Vehicle
    {
        return Cache::remember(
            $this->buildItemCacheKey($id),
            now()->addMinutes(self::ITEM_TTL_MINUTES),
            fn (): Vehicle => $this->vehicleRepository->getById($id),
        );
    }

    public function create(VehicleData $data): Vehicle
    {
        $vehicle = $this->vehicleRepository->create($data);
        $this->forgetItemCache($vehicle->client_id, $vehicle->id);
        $this->bumpCacheVersion();

        return $vehicle;
    }

    public function update(int $id, VehicleData $data): Vehicle
    {
        $vehicle = $this->vehicleRepository->update($id, $data);
        $this->forgetItemCache($vehicle->client_id, $id);
        $this->bumpCacheVersion();

        return $vehicle;
    }

    public function delete(int $id): void
    {
        $this->vehicleRepository->delete($id);
        $this->forgetItemCache(null, $id);
        $this->bumpCacheVersion();
    }

    private function buildListCacheKey(array $filters, int $perPage, int $page): string
    {
        ksort($filters);
        $filtersHash = md5(json_encode($filters));
        $version = $this->getCacheVersion();
        $userId = auth()->id();

        return "vehicles:list:user:{$userId}:v{$version}:{$filtersHash}:{$perPage}:{$page}";
    }

    private function buildItemCacheKey(int $id): string
    {
        $version = $this->getCacheVersion();
        $userId = auth()->id();

        return "vehicles:item:user:{$userId}:v{$version}:{$id}";
    }

    private function forgetItemCache(?int $client_id, int $vehicle_id): void
    {
        $version = $this->getCacheVersion();
        $userId = auth()->id();
        if ($client_id) {
            Cache::forget("clients:item:user:{$userId}:v{$version}:{$client_id}");
        }
        Cache::forget($this->buildItemCacheKey($vehicle_id));
    }

    private function getCacheVersion(): int
    {
        return (int) Cache::get(self::CACHE_VERSION_KEY, 1);
    }

    private function bumpCacheVersion(): void
    {
        $nextVersion = $this->getCacheVersion() + 1;
        Cache::forever(self::CACHE_VERSION_KEY, $nextVersion);
    }
}
