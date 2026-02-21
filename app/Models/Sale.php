<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SalesFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'vehicle_id',
        'subtotal',
        'total',
        'payment_method',
        'paid_at',
        'should_invoice',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'total' => 'float',
        'paid_at' => 'datetime',
        'should_invoice' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesItem::class);
    }

    public function electronicInvoice(): HasOne
    {
        return $this->hasOne(ElectronicInvoice::class);
    }
}
