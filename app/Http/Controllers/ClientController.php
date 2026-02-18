<?php

namespace App\Http\Controllers;

use App\DTOs\Client\ClientData;
use App\Http\Requests\Client\ListClientRequest;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService) {}

    public function index(ListClientRequest $request)
    {
        $clients = $this->clientService->getAll($request);

        return $this->successResponse(ClientResource::collection($clients), 'Clients retrieved successfully', Response::HTTP_OK, [
            'meta' => [
                'total' => $clients->total(),
                'per_page' => $clients->perPage(),
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'from' => $clients->firstItem(),
                'to' => $clients->lastItem(),
            ],
            'links' => [
                'next' => $clients->nextPageUrl(),
                'prev' => $clients->previousPageUrl(),
            ],
        ]);
    }

    public function show($id)
    {
        return $this->successResponse(new ClientResource($this->clientService->getById($id)), 'Client retrieved successfully');
    }

    public function store(StoreClientRequest $request)
    {
        $dto = ClientData::fromArray($request->validated());

        return $this->successResponse(new ClientResource($this->clientService->create($dto)), 'Client created successfully', Response::HTTP_CREATED);
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $dto = ClientData::fromArray($request->validated());

        return $this->successResponse(new ClientResource($this->clientService->update($id, $dto)), 'Client updated successfully');
    }

    public function destroy($id)
    {
        $this->clientService->delete($id);

        return $this->successResponse(null, 'Client deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
