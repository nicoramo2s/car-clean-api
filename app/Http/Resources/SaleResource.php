<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="SaleResource",
 *     description="Sale resource",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=1),
 *     @OA\Property(property="vehicle_id", type="integer", example=2),
 *     @OA\Property(property="subtotal", type="number", format="float", example=35000),
 *     @OA\Property(property="total", type="number", format="float", example=35000),
 *     @OA\Property(property="payment_method", type="string", example="cash"),
 *     @OA\Property(property="paid_at", type="string", format="date-time", nullable=true, example="2026-02-28T10:30:00Z"),
 *     @OA\Property(property="should_invoice", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-28T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-28T10:30:00Z")
 * )
 */
class SaleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
