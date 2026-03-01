<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinOverride extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'reward_id', 'is_used', 'expires_at'];

    protected $casts = [
        'is_used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get active (unused and not expired) override for a doctor
     */
    public static function getActiveForDoctor($doctorId): ?self
    {
        return self::where('doctor_id', $doctorId)
            ->where('is_used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->with('reward')
            ->first();
    }

    /**
     * Check if doctor already has a pending override
     */
    public static function hasPendingForDoctor($doctorId): bool
    {
        return self::where('doctor_id', $doctorId)
            ->where('is_used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Mark this override as used
     */
    public function markAsUsed(): void
    {
        $this->update(['is_used' => true]);
    }
}
