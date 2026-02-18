<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory, SoftDeletes;

    use \OwenIt\Auditing\Auditable;

    protected static function booted()
    {
        static::addGlobalScope('user_id', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id',
    ];

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower($value),
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower($value),
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (! $value) {
                    return null;
                }

                // quitar todo lo que no sea número
                $phone = preg_replace('/\D/', '', $value);

                // // si no empieza con 54 (Argentina), lo agregamos
                // if (!str_starts_with($phone, '54')) {
                //     $phone = '54' . $phone;
                // }

                return '+'.$phone;
            }
        );
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%$search%");
    }
}
