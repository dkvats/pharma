<?php

namespace App\Services;

use App\Models\DoctorTarget;
use App\Models\GrandSpin;
use App\Models\GrandSpinReward;
use Illuminate\Support\Facades\DB;

class GrandSpinService
{
    /**
     * Number of consecutive months required for grand spin
     * Can be overridden via config for testing
     */
    public function getRequiredMonths(): int
    {
        return config('spin.grand_spin_test_mode', false) ? 2 : 12;
    }

    /**
     * Check if doctor has completed required consecutive months
     */
    public function hasCompletedConsecutiveMonths($doctorId): bool
    {
        $requiredMonths = $this->getRequiredMonths();
        
        // Get last N completed months, ordered by month descending
        $completedMonths = DoctorTarget::where('doctor_id', $doctorId)
            ->where('is_completed', true)
            ->orderByDesc('month')
            ->take($requiredMonths)
            ->pluck('month')
            ->toArray();

        // Check if we have enough months
        if (count($completedMonths) < $requiredMonths) {
            return false;
        }

        // Check if months are consecutive
        // Convert month strings (YYYY-MM) to timestamps for comparison
        $timestamps = array_map(function ($month) {
            return strtotime($month . '-01');
        }, $completedMonths);

        sort($timestamps); // Sort ascending to check consecutive

        for ($i = 1; $i < count($timestamps); $i++) {
            $prevMonth = date('Y-m', $timestamps[$i - 1]);
            $currMonth = date('Y-m', $timestamps[$i]);
            
            // Check if current month is exactly 1 month after previous
            $expectedMonth = date('Y-m', strtotime($prevMonth . '-01 +1 month'));
            if ($currMonth !== $expectedMonth) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if doctor is eligible for grand spin
     */
    public function isEligibleForGrandSpin($doctorId): bool
    {
        // Check if already has an unused grand spin
        if (GrandSpin::hasAvailable($doctorId)) {
            return true;
        }

        // Check if completed consecutive months
        if (!$this->hasCompletedConsecutiveMonths($doctorId)) {
            return false;
        }

        // Check if grand spin already awarded for this achievement
        $alreadyAwarded = GrandSpin::where('doctor_id', $doctorId)
            ->where('created_at', '>=', now()->subDays(30)) // Prevent duplicate within 30 days
            ->exists();

        return !$alreadyAwarded;
    }

    /**
     * Award grand spin to doctor
     */
    public function awardGrandSpin($doctorId): ?GrandSpin
    {
        if (!$this->isEligibleForGrandSpin($doctorId)) {
            return null;
        }

        // Check again to prevent race conditions
        if (GrandSpin::hasAvailable($doctorId)) {
            return GrandSpin::getAvailableForDoctor($doctorId);
        }

        return DB::transaction(function () use ($doctorId) {
            return GrandSpin::create([
                'doctor_id' => $doctorId,
                'is_used' => false,
            ]);
        });
    }

    /**
     * Perform grand spin
     */
    public function spin($doctorId): ?array
    {
        $grandSpin = GrandSpin::getAvailableForDoctor($doctorId);

        if (!$grandSpin) {
            return null;
        }

        return DB::transaction(function () use ($grandSpin, $doctorId) {
            // Lock the grand spin row
            $lockedSpin = GrandSpin::where('id', $grandSpin->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedSpin || $lockedSpin->is_used) {
                return null;
            }

            // Select reward
            $reward = GrandSpinReward::selectReward();

            if (!$reward) {
                return null;
            }

            // Decrement stock
            $reward->decrementStock();

            // Mark grand spin as used
            $lockedSpin->markAsUsed($reward->id);

            return [
                'grand_spin' => $lockedSpin,
                'reward' => $reward,
            ];
        });
    }

    /**
     * Get grand spin progress for doctor
     */
    public function getProgress($doctorId): array
    {
        $requiredMonths = $this->getRequiredMonths();
        
        $completedMonths = DoctorTarget::where('doctor_id', $doctorId)
            ->where('is_completed', true)
            ->count();

        $hasAvailableGrandSpin = GrandSpin::hasAvailable($doctorId);

        return [
            'completed_months' => $completedMonths,
            'required_months' => $requiredMonths,
            'progress_percentage' => min(100, ($completedMonths / $requiredMonths) * 100),
            'has_available_grand_spin' => $hasAvailableGrandSpin,
            'is_test_mode' => config('spin.grand_spin_test_mode', false),
        ];
    }

    /**
     * Mark doctor target as completed when spin is used
     * Call this from DoctorTargetService when spin is awarded
     */
    public function markTargetCompleted($doctorId, $month): void
    {
        DoctorTarget::where('doctor_id', $doctorId)
            ->where('month', $month)
            ->update(['is_completed' => true]);

        // Check if eligible for grand spin and award if so
        $this->awardGrandSpin($doctorId);
    }
}
