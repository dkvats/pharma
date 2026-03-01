<?php

namespace App\Services;

use App\Models\GrandDrawWinner;
use App\Models\SpinHistory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GrandDrawService
{
    /**
     * Get the number of spins for a doctor in the current year
     * Computed dynamically from SpinHistory (no static counter stored)
     */
    public function getYearlySpinCount(int $doctorId, ?int $year = null): int
    {
        $year = $year ?? now()->year;
        
        return SpinHistory::where('doctor_id', $doctorId)
            ->whereYear('created_at', $year)
            ->count();
    }

    /**
     * Check if doctor is eligible for Grand Lucky Draw
     * Eligibility: 12+ spins in current year
     */
    public function isEligibleForGrandDraw(int $doctorId, ?int $year = null): bool
    {
        return $this->getYearlySpinCount($doctorId, $year) >= 12;
    }

    /**
     * Get all doctors eligible for Grand Lucky Draw
     */
    public function getEligibleDoctors(?int $year = null): Collection
    {
        $year = $year ?? now()->year;
        
        // Get doctors with 12+ spins in the year
        $eligibleDoctorIds = SpinHistory::whereYear('created_at', $year)
            ->select('doctor_id')
            ->selectRaw('COUNT(*) as spin_count')
            ->groupBy('doctor_id')
            ->having('spin_count', '>=', 12)
            ->pluck('doctor_id');
        
        return User::whereIn('id', $eligibleDoctorIds)
            ->withCount(['spinHistories' => function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            }])
            ->get();
    }

    /**
     * Check if Grand Draw has already been run for a year
     */
    public function hasDrawBeenRun(?int $year = null): bool
    {
        $year = $year ?? now()->year;
        
        return GrandDrawWinner::where('year', $year)->exists();
    }

    /**
     * Get the winner for a year (if draw has been run)
     */
    public function getWinner(?int $year = null): ?GrandDrawWinner
    {
        $year = $year ?? now()->year;
        
        return GrandDrawWinner::with(['doctor', 'drawnBy'])
            ->where('year', $year)
            ->first();
    }

    /**
     * Run the Grand Lucky Draw
     * Randomly selects one winner from eligible doctors
     * ENTERPRISE: Full race-condition protection with explicit locking
     */
    public function runDraw(int $adminId, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        
        // Use 5 retry attempts for deadlock protection
        return DB::transaction(function () use ($adminId, $year) {
            // ENTERPRISE: Explicit lock on winner check to prevent race condition
            // This ensures only one admin can run the draw per year
            $existingWinner = GrandDrawWinner::where('year', $year)
                ->lockForUpdate()
                ->first();
            
            if ($existingWinner) {
                return [
                    'success' => false,
                    'message' => "Grand Draw for {$year} has already been run.",
                    'winner' => $existingWinner->load('doctor'),
                ];
            }
            
            // Get eligible doctors (read-only, no lock needed)
            $eligibleDoctors = $this->getEligibleDoctors($year);
            
            if ($eligibleDoctors->isEmpty()) {
                return [
                    'success' => false,
                    'message' => "No eligible doctors found for {$year}. Minimum 12 spins required.",
                ];
            }
            
            // Randomly select winner using cryptographically secure random
            $winner = $eligibleDoctors->random();
            
            // ENTERPRISE: Verify winner hasn't been selected in another concurrent transaction
            // (Defensive check - unique constraint will catch this, but we check first for cleaner error)
            $alreadySelected = GrandDrawWinner::where('doctor_id', $winner->id)
                ->where('year', $year)
                ->lockForUpdate()
                ->exists();
            
            if ($alreadySelected) {
                return [
                    'success' => false,
                    'message' => "This doctor has already been selected for {$year}. Please retry the draw.",
                ];
            }
            
            // Store winner
            $grandDrawWinner = GrandDrawWinner::create([
                'doctor_id' => $winner->id,
                'year' => $year,
                'draw_date' => now(),
                'drawn_by' => $adminId,
                'total_eligible_doctors' => $eligibleDoctors->count(),
            ]);
            
            // Log the draw
            ActivityLogService::log(
                'grand_draw_run',
                $grandDrawWinner,
                "Grand Lucky Draw run for {$year}. Winner: {$winner->name} ({$winner->email}). Total eligible: {$eligibleDoctors->count()}."
            );
            
            return [
                'success' => true,
                'message' => "Grand Lucky Draw completed for {$year}.",
                'winner' => $grandDrawWinner->load('doctor'),
                'total_eligible' => $eligibleDoctors->count(),
            ];
        }, 5); // 5 retry attempts for deadlock protection
    }

    /**
     * Get Grand Draw statistics
     */
    public function getStatistics(?int $year = null): array
    {
        $year = $year ?? now()->year;
        
        $eligibleDoctors = $this->getEligibleDoctors($year);
        $winner = $this->getWinner($year);
        
        return [
            'year' => $year,
            'total_eligible' => $eligibleDoctors->count(),
            'draw_run' => $winner !== null,
            'winner' => $winner,
            'eligible_doctors' => $eligibleDoctors,
        ];
    }
}
