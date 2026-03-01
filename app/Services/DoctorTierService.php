<?php

namespace App\Services;

use App\Models\Order;

class DoctorTierService
{
    /**
     * Tier thresholds based on lifetime delivered sales
     */
    private const TIERS = [
        'elite' => [
            'threshold' => 300,
            'name' => 'Elite',
            'badge' => '💎',
            'color' => 'purple',
            'bg_class' => 'bg-purple-100 text-purple-800',
        ],
        'platinum' => [
            'threshold' => 150,
            'name' => 'Platinum',
            'badge' => '🥇',
            'color' => 'gray',
            'bg_class' => 'bg-gray-100 text-gray-800',
        ],
        'gold' => [
            'threshold' => 75,
            'name' => 'Gold',
            'badge' => '🥈',
            'color' => 'yellow',
            'bg_class' => 'bg-yellow-100 text-yellow-800',
        ],
        'silver' => [
            'threshold' => 30,
            'name' => 'Silver',
            'badge' => '🥉',
            'color' => 'gray',
            'bg_class' => 'bg-gray-100 text-gray-600',
        ],
    ];

    /**
     * Get tier information for a doctor based on lifetime delivered sales
     *
     * @param int $doctorId
     * @return array
     */
    public function getTier(int $doctorId): array
    {
        $deliveredCount = $this->getDeliveredCount($doctorId);

        foreach (self::TIERS as $key => $tier) {
            if ($deliveredCount >= $tier['threshold']) {
                return [
                    'key' => $key,
                    'name' => $tier['name'],
                    'badge' => $tier['badge'],
                    'color' => $tier['color'],
                    'bg_class' => $tier['bg_class'],
                    'threshold' => $tier['threshold'],
                    'current_sales' => $deliveredCount,
                    'next_tier' => $this->getNextTierInfo($key, $deliveredCount),
                ];
            }
        }

        // No tier achieved yet
        return [
            'key' => 'none',
            'name' => 'Bronze',
            'badge' => '🏅',
            'color' => 'brown',
            'bg_class' => 'bg-orange-100 text-orange-800',
            'threshold' => 0,
            'current_sales' => $deliveredCount,
            'next_tier' => [
                'name' => 'Silver',
                'badge' => '🥉',
                'sales_needed' => 30 - $deliveredCount,
            ],
        ];
    }

    /**
     * Get delivered order count for a doctor
     *
     * @param int $doctorId
     * @return int
     */
    public function getDeliveredCount(int $doctorId): int
    {
        return Order::where('doctor_id', $doctorId)
            ->where('status', 'delivered')
            ->count();
    }

    /**
     * Get next tier information
     *
     * @param string $currentTierKey
     * @param int $currentSales
     * @return array|null
     */
    private function getNextTierInfo(string $currentTierKey, int $currentSales): ?array
    {
        $tiers = array_keys(self::TIERS);
        $currentIndex = array_search($currentTierKey, $tiers);

        // If already at highest tier
        if ($currentIndex === false || $currentIndex === count($tiers) - 1) {
            return null;
        }

        $nextTierKey = $tiers[$currentIndex + 1];
        $nextTier = self::TIERS[$nextTierKey];

        return [
            'name' => $nextTier['name'],
            'badge' => $nextTier['badge'],
            'sales_needed' => $nextTier['threshold'] - $currentSales,
        ];
    }

    /**
     * Get tier for multiple doctors efficiently
     *
     * @param array $doctorIds
     * @return array
     */
    public function getTiersForDoctors(array $doctorIds): array
    {
        // Get counts for all doctors in one query
        $counts = Order::selectRaw('doctor_id, COUNT(*) as total')
            ->whereIn('doctor_id', $doctorIds)
            ->where('status', 'delivered')
            ->groupBy('doctor_id')
            ->pluck('total', 'doctor_id')
            ->toArray();

        $tiers = [];
        foreach ($doctorIds as $doctorId) {
            $count = $counts[$doctorId] ?? 0;
            $tiers[$doctorId] = $this->getTierFromCount($count);
        }

        return $tiers;
    }

    /**
     * Get tier info from count only (no DB query)
     *
     * @param int $count
     * @return array
     */
    private function getTierFromCount(int $count): array
    {
        foreach (self::TIERS as $key => $tier) {
            if ($count >= $tier['threshold']) {
                return [
                    'key' => $key,
                    'name' => $tier['name'],
                    'badge' => $tier['badge'],
                    'bg_class' => $tier['bg_class'],
                ];
            }
        }

        return [
            'key' => 'none',
            'name' => 'Bronze',
            'badge' => '🏅',
            'bg_class' => 'bg-orange-100 text-orange-800',
        ];
    }
}
