<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Vehicle extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory, SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    protected static function booted(): void
    {
        static::addGlobalScope('user_id', function ($query) {
            if (auth()->check()) {
                $query->whereHas('client', function ($query) {
                    $query->where('user_id', auth()->id());
                });
            }
        });
    }

    protected $fillable = [
        'brand',
        'model',
        'year',
        'color',
        'license_plate',
        'client_id',
        'user_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
