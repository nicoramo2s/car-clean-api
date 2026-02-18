<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function sale()
    {
        return $this->belongsTo(Sales::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
