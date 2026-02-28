<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\Sales\ServiceData;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceService $serviceService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/services",
     *     summary="List all services with pagination",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'price', 'description']);
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        return $this->successResponse(
            ServiceResource::collection($this->serviceService->getAll($filters, $perPage, $page)), 'Services retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/services",
     *     summary="Create a new service",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "price"},
     *
     *             @OA\Property(property="name", type="string", example="Basic Wash"),
     *             @OA\Property(property="price", type="number", format="float", example=25.00),
     *             @OA\Property(property="description", type="string", example="Exterior wash and dry")
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Service created successfully")
     * )
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $dto = ServiceData::fromArray($request->validated());

        return $this->successResponse(new ServiceResource($this->serviceService->create($dto)), 'Service created successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/services/{service}",
     *     summary="Get service by ID",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="service", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Service not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new ServiceResource($this->serviceService->getById($id)), 'Service retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/services/{service}",
     *     summary="Update an existing service",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="service", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="Premium Wash"),
     *             @OA\Property(property="price", type="number", format="float", example=45.00),
     *             @OA\Property(property="description", type="string", example="Full interior and exterior cleaning")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Service updated successfully"),
     *     @OA\Response(response=404, description="Service not found")
     * )
     */
    public function update(UpdateServiceRequest $request, int $id): JsonResponse
    {
        $dto = ServiceData::fromArray($request->validated());

        return $this->successResponse(new ServiceResource($this->serviceService->update($id, $dto)), 'Service updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/services/{service}",
     *     summary="Delete a service",
     *     tags={"Services"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="service", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Service deleted successfully"),
     *     @OA\Response(response=404, description="Service not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->serviceService->delete($id);

        return $this->successResponse(null, 'Service deleted successfully');
    }
}
