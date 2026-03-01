<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinCampaign extends Model
{
    use HasFactory;

    protected $fillable = ['reward_id', 'starts_at', 'ends_at', 'is_active'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get currently active campaign
     */
    public static function getActive(): ?self
    {
        return self::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->with('reward')
            ->first();
    }

    /**
     * Check if campaign is currently active
     */
    public function isCurrentlyActive(): bool
    {
        return $this->is_active 
            && $this->starts_at <= now() 
            && $this->ends_at >= now();
    }
}
