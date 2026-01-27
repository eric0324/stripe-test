<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'email',
        'payment_type',
        'tier_id',
        'tier_name',
        'original_amount',
        'discount_amount',
        'final_amount',
        'coupon_code',
        'currency',
        'status',
    ];

    protected $casts = [
        'original_amount' => 'integer',
        'discount_amount' => 'integer',
        'final_amount' => 'integer',
    ];

    public function tier(): BelongsTo
    {
        return $this->belongsTo(PricingTier::class, 'tier_id');
    }
}
