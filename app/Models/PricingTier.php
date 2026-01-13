<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    protected $fillable = [
        'name',
        'one_time_price',
        'installment_price',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'one_time_price' => 'integer',
        'installment_price' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public static function getCurrentTier()
    {
        $now = now();

        return static::where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->first();
    }
}
