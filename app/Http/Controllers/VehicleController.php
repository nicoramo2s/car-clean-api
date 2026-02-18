<?php

namespace App\Http\Controllers;

use App\DTOs\Vehicle\VehicleData;
use App\Http\Requests\Vehicle\ListVehicleRequest;
use App\Http\Requests\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Vehicle\UpdateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Services\VehicleService;
use Symfony\Component\HttpFoundation\Response;

class VehicleController extends Controller
{
    public function __construct(private VehicleService $vehicleService) {}

    public function index(ListVehicleRequest $request)
    {
        $vehicles = $this->vehicleService->getAll($request);

        return $this->successResponse(VehicleResource::collection($vehicles), 'Vehicles retrieved successfully', Response::HTTP_OK, [
            'meta' => [
                'total' => $vehicles->total(),
                'per_page' => $vehicles->perPage(),
                'current_page' => $vehicles->currentPage(),
                'last_page' => $vehicles->lastPage(),
                'from' => $vehicles->firstItem(),
                'to' => $vehicles->lastItem(),
            ],
            'links' => [
                'next' => $vehicles->nextPageUrl(),
                'prev' => $vehicles->previousPageUrl(),
            ],
        ]);
    }

    public function show($id)
    {
        try {
            return $this->successResponse(new VehicleResource($this->vehicleService->getById($id)), 'Vehicle retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Vehicle not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreVehicleRequest $request)
    {
        $dto = VehicleData::fromArray($request->validated());

        return $this->successResponse(new VehicleResource($this->vehicleService->create($dto)), 'Vehicle created successfully', Response::HTTP_CREATED);
    }

    public function update(UpdateVehicleRequest $request, $id)
    {
        try {
            $vehicle = $this->vehicleService->getById($id);
            $dto = VehicleData::fromArray($request->validated(), $vehicle);

            return $this->successResponse(new VehicleResource($this->vehicleService->update($id, $dto)), 'Vehicle updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Vehicle not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy($id)
    {
        try {
            $this->vehicleService->delete($id);

            return $this->successResponse(null, 'Vehicle deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Vehicle not found', null, Response::HTTP_NOT_FOUND);
        }
    }
}
