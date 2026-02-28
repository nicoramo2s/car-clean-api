<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\Sales\SaleData;
use App\Http\Requests\Sale\ListSaleRequest;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    public function __construct(
        private readonly SaleService $saleService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/sales",
     *     summary="List all sales with pagination",
     *     tags={"Sales"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="client_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="vehicle_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="payment_method", in="query", @OA\Schema(type="string", enum={"cash","transfer","card"})),
     *     @OA\Parameter(name="should_invoice", in="query", @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(ListSaleRequest $request): JsonResponse
    {
        $filters = $request->only([
            'client_id',
            'vehicle_id',
            'payment_method',
            'should_invoice',
        ]);
        $perPage = $request->integer('per_page', 10);
        $page = $request->integer('page', 1);

        $sales = $this->saleService->getAll($filters, $perPage, $page);

        return $this->successResponse(
            SaleResource::collection($sales),
            'Sales retrieved successfully',
            Response::HTTP_OK,
            [
                'meta' => [
                    'total' => $sales->total(),
                    'per_page' => $sales->perPage(),
                    'current_page' => $sales->currentPage(),
                    'last_page' => $sales->lastPage(),
                    'from' => $sales->firstItem(),
                    'to' => $sales->lastItem(),
                ],
                'links' => [
                    'next' => $sales->nextPageUrl(),
                    'prev' => $sales->previousPageUrl(),
                ],
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/sales",
     *     summary="Create a new sale",
     *     tags={"Sales"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"client_id", "vehicle_id", "payment_method", "should_invoice", "items"},
     *
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="payment_method", type="string", example="cash"),
     *             @OA\Property(property="paid_at", type="string", format="date-time", nullable=true, example="2026-02-28T10:30:00Z"),
     *             @OA\Property(property="should_invoice", type="boolean", example=true),
     *             @OA\Property(property="point_of_sale", type="integer", nullable=true, example=1),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *                     required={"service_id"},
     *
     *                     @OA\Property(property="service_id", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Sale created successfully")
     * )
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        $dto = SaleData::fromArray($request->validated());

        return $this->successResponse(
            new SaleResource($this->saleService->create($dto)),
            'Sale created successfully',
            Response::HTTP_CREATED,
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/sales/{sale}",
     *     summary="Get sale by ID",
     *     tags={"Sales"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="sale", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Sale not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new SaleResource($this->saleService->getById($id)),
            'Sale retrieved successfully',
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/sales/{sale}",
     *     summary="Update an existing sale",
     *     tags={"Sales"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="sale", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="payment_method", type="string", example="transfer"),
     *             @OA\Property(property="paid_at", type="string", format="date-time", nullable=true, example="2026-02-28T10:30:00Z"),
     *             @OA\Property(property="should_invoice", type="boolean", example=false),
     *             @OA\Property(property="point_of_sale", type="integer", nullable=true, example=1),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *                     required={"service_id"},
     *
     *                     @OA\Property(property="service_id", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Sale updated successfully"),
     *     @OA\Response(response=404, description="Sale not found")
     * )
     */
    public function update(UpdateSaleRequest $request, int $id): JsonResponse
    {
        $dto = SaleData::fromArray($request->validated());

        return $this->successResponse(
            new SaleResource($this->saleService->update($id, $dto)),
            'Sale updated successfully',
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/sales/{sale}",
     *     summary="Delete a sale",
     *     tags={"Sales"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(name="sale", in="path", required=true, @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=204, description="Sale deleted successfully"),
     *     @OA\Response(response=404, description="Sale not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->saleService->delete($id);

        return $this->successResponse(null, 'Sale deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
