<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectronicInvoice extends Model
{
    protected $fillable = [
        'sale_id',
        'user_id',
        'cae',
        'cae_due_date',
        'voucher_number',
        'point_of_sale',
        'status',
        'arca_response',
    ];

    protected $casts = [
        'cae_due_date' => 'datetime',
        'arca_response' => 'array',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
