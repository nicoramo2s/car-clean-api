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

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $dto = SaleData::fromArray($request->validated());

        return $this->successResponse(
            new SaleResource($this->saleService->create($dto)),
            'Sale created successfully',
            Response::HTTP_CREATED,
        );
    }

    public function show(int $id): JsonResponse
    {
        return $this->successResponse(
            new SaleResource($this->saleService->getById($id)),
            'Sale retrieved successfully',
        );
    }

    public function update(UpdateSaleRequest $request, int $id): JsonResponse
    {
        $dto = SaleData::fromArray($request->validated());

        return $this->successResponse(
            new SaleResource($this->saleService->update($id, $dto)),
            'Sale updated successfully',
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->saleService->delete($id);

        return $this->successResponse(null, 'Sale deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
