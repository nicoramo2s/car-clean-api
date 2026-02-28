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

    /**
     * @OA\Get(
     *     path="/api/v1/vehicles",
     *     summary="List all vehicles with pagination",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/vehicles/{vehicle}",
     *     summary="Get vehicle by ID",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="vehicle", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Vehicle not found")
     * )
     */
    public function show($id)
    {
        try {
            return $this->successResponse(new VehicleResource($this->vehicleService->getById($id)), 'Vehicle retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Vehicle not found', null, Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/vehicles",
     *     summary="Create a new vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"client_id", "license_plate", "brand", "model"},
     *
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="license_plate", type="string", example="AA123BB"),
     *             @OA\Property(property="brand", type="string", example="Toyota"),
     *             @OA\Property(property="model", type="string", example="Corolla"),
     *             @OA\Property(property="year", type="integer", example=2024),
     *             @OA\Property(property="color", type="string", example="White")
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Vehicle created successfully")
     * )
     */
    public function store(StoreVehicleRequest $request)
    {
        $dto = VehicleData::fromArray($request->validated());

        return $this->successResponse(new VehicleResource($this->vehicleService->create($dto)), 'Vehicle created successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/vehicles/{vehicle}",
     *     summary="Update an existing vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="vehicle", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="license_plate", type="string", example="AA123BB"),
     *             @OA\Property(property="brand", type="string", example="Honda"),
     *             @OA\Property(property="model", type="string", example="Civic"),
     *             @OA\Property(property="year", type="integer", example=2022),
     *             @OA\Property(property="color", type="string", example="Black")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Vehicle updated successfully"),
     *     @OA\Response(response=404, description="Vehicle not found")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/v1/vehicles/{vehicle}",
     *     summary="Delete a vehicle",
     *     tags={"Vehicles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="vehicle", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=204, description="Vehicle deleted successfully"),
     *     @OA\Response(response=404, description="Vehicle not found")
     * )
     */
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
