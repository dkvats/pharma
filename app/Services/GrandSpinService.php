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
     * Check if doctor has completed required consecutive months.
     *
     * Uses integer year + integer month (1-12) from the doctor_targets table.
     * Consecutive check: each record must be exactly one calendar month after the previous.
     */
    public function hasCompletedConsecutiveMonths($doctorId): bool
    {
        $requiredMonths = $this->getRequiredMonths();

        // Fetch the last N completed month records ordered newest first.
        // Select both year and month (both are integers) to allow cross-year checks.
        $records = DoctorTarget::where('doctor_id', $doctorId)
            ->where('is_completed', true)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->take($requiredMonths)
            ->get(['year', 'month']);

        // Check if we have enough completed months
        if ($records->count() < $requiredMonths) {
            return false;
        }

        // Build a sorted ascending list of Carbon dates (first day of each month)
        // using integer year and integer month — no string parsing required.
        $dates = $records
            ->map(fn ($r) => \Carbon\Carbon::create((int) $r->year, (int) $r->month, 1))
            ->sortBy(fn ($d) => $d->timestamp)
            ->values();

        // Verify every adjacent pair is exactly one calendar month apart
        for ($i = 1; $i < $dates->count(); $i++) {
            $expected = $dates[$i - 1]->copy()->addMonthNoOverflow();
            if (!$dates[$i]->isSameMonth($expected)) {
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
     * Mark a specific doctor target month as completed.
     * Call this from DoctorTargetService when a monthly spin is awarded.
     *
     * @param int $doctorId
     * @param int $year  Integer year (e.g. 2026)
     * @param int $month Integer month 1-12 (e.g. 3 for March)
     */
    public function markTargetCompleted($doctorId, int $year, int $month): void
    {
        DoctorTarget::where('doctor_id', $doctorId)
            ->where('year', $year)
            ->where('month', $month)
            ->update(['is_completed' => true]);

        // Check if eligible for grand spin and award if so
        $this->awardGrandSpin($doctorId);
    }
}
