<?php

namespace App\Services;

use App\DTOs\Client\ClientData;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClientService
{
    private const CACHE_VERSION_KEY = 'clients:cache_version';

    private const LIST_TTL_MINUTES = 5;

    private const ITEM_TTL_MINUTES = 10;

    public function __construct(private ClientRepositoryInterface $clientRepository) {}

    public function getAll(Request $request): LengthAwarePaginator
    {
        $filters = $request->only(['search']);
        $perPage = $request->integer('per_page', 15);
        $page = $request->integer('page', 1);
        $cacheKey = $this->buildListCacheKey($filters, $perPage, $page);

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(self::LIST_TTL_MINUTES),
            fn (): LengthAwarePaginator => $this->clientRepository->getAll($filters, $perPage, $page),
        );
    }

    public function getById(int $id): Client
    {
        return Cache::remember(
            $this->buildItemCacheKey($id),
            now()->addMinutes(self::ITEM_TTL_MINUTES),
            fn (): Client => $this->clientRepository->getById($id),
        );
    }

    public function create(ClientData $data): Client
    {
        $client = $this->clientRepository->create($data);
        $this->forgetItemCache($client->id);
        $this->bumpCacheVersion();

        return $client;
    }

    public function update(int $id, ClientData $data): Client
    {
        $client = $this->clientRepository->update($id, $data);
        $this->forgetItemCache($id);
        $this->forgetItemCache($client->id);
        $this->bumpCacheVersion();

        return $client;
    }

    public function delete(int $id): void
    {
        $this->clientRepository->delete($id);
        $this->forgetItemCache($id);
        $this->bumpCacheVersion();
    }

    private function buildListCacheKey(array $filters, int $perPage, int $page): string
    {
        ksort($filters);
        $filtersHash = md5((string) json_encode($filters));
        $version = $this->getCacheVersion();
        $userId = auth()->id();

        return "clients:list:user:{$userId}:v{$version}:{$filtersHash}:{$perPage}:{$page}";
    }

    private function buildItemCacheKey(int $id): string
    {
        $version = $this->getCacheVersion();
        $userId = auth()->id();

        return "clients:item:user:{$userId}:v{$version}:{$id}";
    }

    private function forgetItemCache(int $id): void
    {
        Cache::forget($this->buildItemCacheKey($id));
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
