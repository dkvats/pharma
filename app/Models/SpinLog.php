<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'action',
        'ip_address',
        'user_agent',
        'result',
        'details',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Log a spin attempt
     */
    public static function logAttempt($doctorId, $details = null): self
    {
        return self::create([
            'doctor_id' => $doctorId,
            'action' => 'attempt',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details' => $details,
        ]);
    }

    /**
     * Log a successful spin
     */
    public static function logSuccess($doctorId, $result, $details = null): self
    {
        return self::create([
            'doctor_id' => $doctorId,
            'action' => 'success',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'result' => $result,
            'details' => $details,
        ]);
    }

    /**
     * Log a failed/blocked spin
     */
    public static function logFailure($doctorId, $reason, $details = null): self
    {
        return self::create([
            'doctor_id' => $doctorId,
            'action' => 'failure',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'result' => 'blocked',
            'details' => $reason . ($details ? ' | ' . $details : ''),
        ]);
    }

    /**
     * Check for suspicious activity (multiple attempts in short time)
     */
    public static function hasSuspiciousActivity($doctorId, $minutes = 5, $threshold = 10): bool
    {
        $attempts = self::where('doctor_id', $doctorId)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();

        return $attempts >= $threshold;
    }

    /**
     * Get recent attempts by IP address
     */
    public static function getRecentAttemptsByIp($ip, $minutes = 60)
    {
        return self::where('ip_address', $ip)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->orderByDesc('created_at')
            ->get();
    }
}
