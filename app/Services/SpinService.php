<?php

namespace App\Services;

use App\Models\Reward;
use App\Models\SpinCampaign;
use App\Models\SpinHistory;
use App\Models\SpinLog;
use App\Models\SpinOverride;
use App\Models\User;
use App\Services\DoctorTargetService;
use Illuminate\Support\Facades\DB;

class SpinService
{
    /**
     * Check if doctor is eligible to spin
     */
    public function canSpin($doctorId): bool
    {
        $doctorTargetService = new DoctorTargetService();
        return $doctorTargetService->canSpin($doctorId);
    }

    /**
     * Perform a spin and return the reward
     * CRITICAL: Full race condition protection with row locking
     * Priority: 1. Override, 2. Campaign, 3. Probability
     */
    public function spin($doctorId): ?Reward
    {
        // Log attempt for fraud detection
        SpinLog::logAttempt($doctorId, 'Monthly spin attempt');

        // Check for suspicious activity
        if (SpinLog::hasSuspiciousActivity($doctorId, 5, 10)) {
            SpinLog::logFailure($doctorId, 'Suspicious activity detected - too many attempts');
            throw new \Exception('Too many spin attempts. Please try again later.');
        }

        // Initial eligibility check (fast fail)
        if (!$this->canSpin($doctorId)) {
            SpinLog::logFailure($doctorId, 'Not eligible to spin');
            return null;
        }

        return DB::transaction(function () use ($doctorId) {
            $doctorTargetService = new DoctorTargetService();
            
            // Re-check eligibility inside transaction (race condition protection)
            $usedSpins = SpinHistory::where('doctor_id', $doctorId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->lockForUpdate()
                ->count();
            
            $eligibleSpins = $doctorTargetService->getEligibleSpins($doctorId);
            
            if ($usedSpins >= $eligibleSpins) {
                SpinLog::logFailure($doctorId, 'Spin limit reached');
                throw new \Exception('Spin limit reached for this month');
            }

            $selectionSource = '';

            // PRIORITY 1: Check for admin override
            $override = SpinOverride::getActiveForDoctor($doctorId);
            
            if ($override) {
                $selectedReward = $override->reward;
                $override->markAsUsed();
                $selectionSource = 'override';
            } else {
                // PRIORITY 2: Check for active global campaign
                $campaign = SpinCampaign::getActive();
                
                if ($campaign && $campaign->reward) {
                    // Check if campaign reward has stock available
                    $campaignReward = $campaign->reward;
                    $hasStock = $campaignReward->stock === null || $campaignReward->stock > 0;
                    
                    if ($hasStock) {
                        $selectedReward = $campaignReward;
                        $selectionSource = 'campaign';
                    } else {
                        // Campaign reward out of stock - log and fallback to probability
                        SpinLog::logFailure($doctorId, 'Campaign reward out of stock: ' . $campaignReward->name);
                        // Fall through to probability selection below
                    }
                }
                
                // If no campaign or campaign reward out of stock, use probability
                if (!isset($selectedReward)) {
                    // PRIORITY 3: Normal probability-based selection
                    $rewards = Reward::where('is_active', true)
                        ->where(function ($q) {
                            $q->whereNull('stock')->orWhere('stock', '>', 0);
                        })
                        ->get();

                    if ($rewards->isEmpty()) {
                        SpinLog::logFailure($doctorId, 'No rewards available');
                        throw new \Exception('No rewards available');
                    }

                    $selectedReward = $this->selectReward($rewards);
                    $selectionSource = 'probability';
                }
            }

            if (!$selectedReward) {
                SpinLog::logFailure($doctorId, 'Failed to select reward');
                throw new \Exception('Failed to select reward');
            }

            // CRITICAL: Lock reward row for stock update
            $reward = Reward::where('id', $selectedReward->id)
                ->lockForUpdate()
                ->first();

            if (!$reward) {
                SpinLog::logFailure($doctorId, 'Reward no longer available');
                throw new \Exception('Reward no longer available');
            }

            // Validate stock AFTER locking
            if ($reward->stock !== null && $reward->stock <= 0) {
                SpinLog::logFailure($doctorId, 'Reward out of stock: ' . $reward->name);
                throw new \Exception('Reward out of stock: ' . $reward->name);
            }

            // Decrement stock
            if ($reward->stock !== null) {
                $reward->decrement('stock');
            }

            // Create spin history with month for unique constraint
            $spinHistory = SpinHistory::create([
                'doctor_id' => $doctorId,
                'reward_id' => $reward->id,
                'spin_date' => now(),
                'month' => now()->format('Y-m'),
                'claimed' => false,
            ]);

            // Log success
            SpinLog::logSuccess($doctorId, 'win', "Source: {$selectionSource}, Reward: {$reward->name}");
            ActivityLogService::logSpin($spinHistory, $reward);

            return $reward;
        });
    }

    /**
     * Select a reward based on probability weights
     */
    private function selectReward($rewards): ?Reward
    {
        $totalProbability = $rewards->sum('probability');
        $random = mt_rand() / mt_getrandmax() * $totalProbability;

        $cumulativeProbability = 0;
        foreach ($rewards as $reward) {
            $cumulativeProbability += $reward->probability;
            if ($random <= $cumulativeProbability) {
                return $reward;
            }
        }

        return $rewards->last();
    }

    /**
     * Get spin history for a doctor
     */
    public function getSpinHistory($doctorId, $limit = 10)
    {
        return SpinHistory::with('reward')
            ->where('doctor_id', $doctorId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Claim a reward with race condition protection
     */
    public function claimReward($spinHistoryId, $doctorId): bool
    {
        return DB::transaction(function () use ($spinHistoryId, $doctorId) {
            // Lock the spin history row to prevent concurrent claims
            $spin = SpinHistory::where('id', $spinHistoryId)
                ->where('doctor_id', $doctorId)
                ->where('claimed', false)
                ->lockForUpdate()
                ->first();

            if (!$spin) {
                return false;
            }

            $spin->update([
                'claimed' => true,
                'claimed_at' => now(),
            ]);

            // Log the claim
            ActivityLogService::log(
                'reward_claimed',
                $spin,
                "Doctor {$doctorId} claimed reward for spin #{$spinHistoryId}"
            );

            return true;
        });
    }
}
