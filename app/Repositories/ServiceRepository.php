<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Sales\ServiceData;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Service::where('user_id', auth()->id())
            ->when(! empty($filters['name']), function ($query) use ($filters) {
                $query->where('name', 'like', '%'.$filters['name'].'%');
            })
            ->when(! empty($filters['price']), function ($query) use ($filters) {
                $query->where('price', $filters['price']);
            })
            ->when(! empty($filters['description']), function ($query) use ($filters) {
                $query->where('description', 'like', '%'.$filters['description'].'%');
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->appends($filters);
    }

    public function getById(int $id): ?Service
    {
        return Service::where('user_id', auth()->id())
            ->findOrFail($id);
    }

    public function create(ServiceData $data): Service
    {
        return DB::transaction(function () use ($data): Service {
            $payload = $data->toArray();
            $payload['user_id'] = auth()->id();

            return Service::create($payload);
        });
    }

    public function update(int $id, ServiceData $data): Service
    {
        return DB::transaction(function () use ($id, $data): Service {
            $service = $this->getById($id);
            $service->update($data->toArray());

            return $service->fresh();
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id): bool {
            $service = $this->getById($id);

            return $service->delete();
        });
    }
}
