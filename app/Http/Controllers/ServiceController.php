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
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $dto = ServiceData::fromArray($request->validated());

        return $this->successResponse(new ServiceResource($this->serviceService->create($dto)), 'Service created successfully', Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new ServiceResource($this->serviceService->getById($id)), 'Service retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, int $id): JsonResponse
    {
        $dto = ServiceData::fromArray($request->validated());

        return $this->successResponse(new ServiceResource($this->serviceService->update($id, $dto)), 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->serviceService->delete($id);

        return $this->successResponse(null, 'Service deleted successfully');
    }
}
