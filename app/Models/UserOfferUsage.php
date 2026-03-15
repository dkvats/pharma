<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOfferUsage extends Model
{
    protected $fillable = [
        'user_id',
        'offer_id',
        'order_id',
        'discount_amount',
        'used_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if user has already used this offer
     */
    public static function hasUserUsedOffer(int $userId, int $offerId): bool
    {
        return self::where('user_id', $userId)
            ->where('offer_id', $offerId)
            ->exists();
    }

    /**
     * Record offer usage
     */
    public static function recordUsage(int $userId, int $offerId, int $orderId, float $discountAmount): self
    {
        return self::create([
            'user_id' => $userId,
            'offer_id' => $offerId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
        ]);
    }
}
