<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Sales\SaleData;
use App\DTOs\Sales\SaleItemData;
use App\Models\ElectronicInvoice;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SaleRepository implements SaleRepositoryInterface
{
    public function __construct(private ServiceRepositoryInterface $serviceRepository)
    {
    }
    public function getAll(array $filters = [], int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return Sale::query()
            ->where('user_id', auth()->id())
            ->with(['client', 'vehicle', 'items.service', 'electronicInvoice'])
            ->when($filters['client_id'] ?? null, function (Builder $query, int $search): void {
                $query->where('client_id', $search);
            })
            ->when($filters['vehicle_id'] ?? null, function (Builder $query, int $search): void {
                $query->where('vehicle_id', $search);
            })
            ->when($filters['payment_method'] ?? null, function (Builder $query, string $search): void {
                $query->where('payment_method', $search);
            })
            ->when(isset($filters['should_invoice']), function (Builder $query) use ($filters): void {
                $search = filter_var($filters['should_invoice'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

                $query->where('should_invoice', $search);
            })
            ->orderByDesc('paid_at')
            ->paginate($perPage, ['*'], 'page', $page)
            ->appends($filters);
    }

    public function getById(int $id): Sale
    {
        return Sale::query()
            ->where('user_id', auth()->id())
            ->with(['client', 'vehicle', 'items.service', 'electronicInvoice'])
            ->findOrFail($id);
    }

    public function create(SaleData $saleData): Sale
    {
        return DB::transaction(function () use ($saleData): Sale {
            $subtotal = $this->calculateSubtotal($saleData->items);
            $saleAttributes = $saleData->toSaleAttributes();

            $sale = Sale::create([
                ...$saleAttributes,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'paid_at' => $saleAttributes['paid_at'] ?? now(),
            ]);

            foreach ($saleData->items as $item) {
                $this->createSaleItem($sale, $item);
            }

            if ($saleData->shouldInvoice === true) {
                ElectronicInvoice::create([
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'point_of_sale' => $saleData->pointOfSale ?? 1,
                    'status' => 'pending',
                ]);
            }

            return $this->getById($sale->id);
        });
    }

    public function update(int $id, SaleData $saleData): Sale
    {
        return DB::transaction(function () use ($id, $saleData): Sale {
            $sale = $this->getById($id);
            $subtotal = $this->calculateSubtotal($saleData->items);

            $sale->update([
                ...$saleData->toSaleAttributes(),
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'paid_at' => $saleData->paidAt ?? $sale->paid_at,
            ]);

            $sale->items()->delete();

            foreach ($saleData->items as $item) {
                $this->createSaleItem($sale, $item);
            }

            if ($saleData->shouldInvoice === true && $sale->electronicInvoice === null) {
                ElectronicInvoice::create([
                    'sale_id' => $sale->id,
                    'user_id' => auth()->id(),
                    'point_of_sale' => $saleData->pointOfSale ?? 1,
                    'status' => 'pending',
                ]);
            }

            return $this->getById($sale->id);
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $this->getById($id)->delete();
        });
    }

    /**
     * @param  array<SaleItemData>  $items
     */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            $service = $this->serviceRepository->getById($item->serviceId);
            $subtotal += $service->price;
        }

        return round($subtotal, 2);
    }

    private function createSaleItem(Sale $sale, SaleItemData $item): void
    {
        SalesItem::create([
            'sale_id' => $sale->id,
            'service_id' => $item->serviceId,
        ]);
    }
}
