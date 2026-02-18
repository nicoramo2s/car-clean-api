<?php

namespace App\Repositories\Contracts;

use App\DTOs\Client\ClientData;

interface ClientRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10, int $page = 1);

    public function getById(int $id);

    public function create(ClientData $data);

    public function update(int $id, ClientData $data);

    public function delete(int $id);
}
