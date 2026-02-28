<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     title="ServiceResource",
 *     description="Service resource",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Lavado Premium"),
 *     @OA\Property(property="description", type="string", example="Lavado interior y exterior"),
 *     @OA\Property(property="price", type="number", format="float", example=25000),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-02-28T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-02-28T10:30:00Z")
 * )
 */
class ServiceResource extends JsonResource
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
