<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrandSpin extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'is_used', 'used_at', 'reward_id'];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(GrandSpinReward::class, 'reward_id');
    }

    /**
     * Get available (unused) grand spin for doctor
     */
    public static function getAvailableForDoctor($doctorId): ?self
    {
        return self::where('doctor_id', $doctorId)
            ->where('is_used', false)
            ->first();
    }

    /**
     * Check if doctor has an available grand spin
     */
    public static function hasAvailable($doctorId): bool
    {
        return self::where('doctor_id', $doctorId)
            ->where('is_used', false)
            ->exists();
    }

    /**
     * Mark grand spin as used
     */
    public function markAsUsed($rewardId): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
            'reward_id' => $rewardId,
        ]);
    }
}
