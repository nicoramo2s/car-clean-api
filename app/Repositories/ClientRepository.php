<?php

namespace App\Repositories;

use App\DTOs\Client\ClientData;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClientRepository implements ClientRepositoryInterface
{
    public function getAll(array $filters = [], int $perPage = 10, int $page = 1)
    {
        return Client::query()
            ->search($filters['search'] ?? '')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->appends($filters);
    }

    public function getById(int $id)
    {
        return Client::where('user_id', auth()->id())
            ->with([
                'vehicles' => function ($query) {
                    $query->select(
                        'id',
                        'client_id',
                        'brand',
                        'model',
                        'license_plate',
                        'color'
                    );
                },
            ])
            ->findOrFail($id, ['id', 'name', 'email', 'phone']);
    }

    public function create(ClientData $data)
    {
        return DB::transaction(function () use ($data) {

            $payload = $data->toArray();
            $payload['user_id'] = auth()->id();

            return Client::create($payload);
        });
    }

    public function update(int $id, ClientData $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $client = Client::where('user_id', auth()->id())
                ->findOrFail($id);
            $client->update($data->toArray());

            return $client;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $client = Client::where('user_id', auth()->id())
                ->findOrFail($id);
            $client->delete();
        });
    }
}
