<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="VehicleResource",
 *     description="Vehicle resource",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="brand", type="string", example="Toyota"),
 *     @OA\Property(property="model", type="string", example="Corolla"),
 *     @OA\Property(property="year", type="integer", nullable=true, example=2024),
 *     @OA\Property(property="color", type="string", nullable=true, example="Blanco"),
 *     @OA\Property(property="license_plate", type="string", example="AA123BB"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-28T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-28T10:30:00Z")
 * )
 */
class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
