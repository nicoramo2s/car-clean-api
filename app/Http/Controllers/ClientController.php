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

    /**
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="List all clients with pagination",
     *     tags={"Clients"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ClientResource")),
     *             @OA\Property(property="message", type="string", example="Clients retrieved successfully"),
     *             @OA\Property(property="meta", type="object"),
     *             @OA\Property(property="links", type="object")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/clients/{client}",
     *     summary="Get client by ID",
     *     tags={"Clients"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function show($id)
    {
        return $this->successResponse(new ClientResource($this->clientService->getById($id)), 'Client retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/clients",
     *     summary="Create a new client",
     *     tags={"Clients"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *
     *             @OA\Property(property="name", type="string", example="Juan Perez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="phone", type="string", example="+5491122334455")
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Client created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreClientRequest $request)
    {
        $dto = ClientData::fromArray($request->validated());

        return $this->successResponse(new ClientResource($this->clientService->create($dto)), 'Client created successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/clients/{client}",
     *     summary="Update an existing client",
     *     tags={"Clients"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="Juan Perez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan@example.com"),
     *             @OA\Property(property="phone", type="string", example="+5491122334455")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Client updated successfully"),
     *     @OA\Response(response=404, description="Client not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateClientRequest $request, $id)
    {
        $dto = ClientData::fromArray($request->validated());

        return $this->successResponse(new ClientResource($this->clientService->update($id, $dto)), 'Client updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/clients/{client}",
     *     summary="Delete a client",
     *     tags={"Clients"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="client", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=204, description="Client deleted successfully"),
     *     @OA\Response(response=404, description="Client not found")
     * )
     */
    public function destroy($id)
    {
        $this->clientService->delete($id);

        return $this->successResponse(null, 'Client deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
