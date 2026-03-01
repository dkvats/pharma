<?php

namespace App\Services;

use App\Models\DoctorTarget;
use App\Models\Order;
use App\Models\Product;
use App\Models\SpinHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DoctorTargetService
{
    /**
     * Increment target when order is approved
     * CRITICAL: Uses row locking to prevent race conditions
     */
    public function incrementTarget($doctorId, $quantity = 1)
    {
        return DB::transaction(function () use ($doctorId, $quantity) {
            // Verify doctor role
            $doctor = User::find($doctorId);
            if (!$doctor || !$doctor->hasRole('Doctor')) {
                return null;
            }

            // Lock the target row for update - prevents concurrent modifications
            $target = DoctorTarget::where([
                'doctor_id' => $doctorId,
                'year' => now()->year,
                'month' => now()->month,
            ])->lockForUpdate()->first();

            // If not exists, create inside transaction with lock protection
            if (!$target) {
                try {
                    $target = DoctorTarget::create([
                        'doctor_id' => $doctorId,
                        'year' => now()->year,
                        'month' => now()->month,
                        'target_quantity' => 30,
                        'achieved_quantity' => $quantity,
                    ]);
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    // Another transaction created it - fetch with lock
                    $target = DoctorTarget::where([
                        'doctor_id' => $doctorId,
                        'year' => now()->year,
                        'month' => now()->month,
                    ])->lockForUpdate()->first();
                    $target->achieved_quantity += $quantity;
                }
            } else {
                // Update existing locked record
                $target->achieved_quantity += $quantity;
            }

            // Check if target completed
            if ($target->achieved_quantity >= $target->target_quantity) {
                $target->target_completed = true;
                // Only make spin eligible if not already spun this month
                if (!$target->spin_completed_at) {
                    $target->spin_eligible = true;
                }
            }

            $target->save();
            return $target;
        });
    }

    /**
     * Decrement target when order is rejected/unapproved
     */
    public function decrementTarget($doctorId, $quantity = 1)
    {
        $target = DoctorTarget::where('doctor_id', $doctorId)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        if (!$target) {
            return null;
        }

        $target->achieved_quantity = max(0, $target->achieved_quantity - $quantity);

        // Recalculate completion status
        if ($target->achieved_quantity < $target->target_quantity) {
            $target->target_completed = false;
            $target->spin_eligible = false;
        }

        $target->save();
        return $target;
    }

    /**
     * Mark spin as completed (prevents multiple spins)
     */
    public function markSpinCompleted($doctorId)
    {
        $target = DoctorTarget::where('doctor_id', $doctorId)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        if ($target) {
            $target->spin_completed_at = now();
            $target->spin_eligible = false;
            $target->save();
        }

        return $target;
    }

    /**
     * Get number of eligible spins based on 30 products + 1 special product per spin
     */
    public function getEligibleSpins($doctorId): int
    {
        $doctor = User::find($doctorId);
        if (!$doctor || !$doctor->hasRole('Doctor')) {
            return 0;
        }

        // Get total products sold this month
        $totalProducts = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.doctor_id', $doctorId)
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->sum('order_items.quantity') ?? 0;

        // Get special product
        $specialProduct = Product::where('is_special_spin_product', true)->first();
        if (!$specialProduct) {
            return 0;
        }

        // Count special products sold this month
        $specialProductCount = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.doctor_id', $doctorId)
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->where('order_items.product_id', $specialProduct->id)
            ->sum('order_items.quantity') ?? 0;

        // Calculate spins: min(floor(totalProducts / 30), specialProductCount)
        $productSpins = floor($totalProducts / 30);

        return min($productSpins, (int) $specialProductCount);
    }

    /**
     * Get remaining spins for this month
     */
    public function getRemainingSpins($doctorId): int
    {
        $eligible = $this->getEligibleSpins($doctorId);

        $used = SpinHistory::where('doctor_id', $doctorId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return max(0, $eligible - $used);
    }

    /**
     * Check if doctor can spin (has remaining spins)
     */
    public function canSpin($doctorId): bool
    {
        return $this->getRemainingSpins($doctorId) > 0;
    }

    /**
     * Check if doctor has sold the special spin product (at least 1 unit)
     */
    private function hasDoctorSoldSpecialProduct($doctorId, $productId): bool
    {
        $quantity = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.doctor_id', $doctorId)
            ->where('orders.status', 'delivered')
            ->where('order_items.product_id', $productId)
            ->sum('order_items.quantity');

        return $quantity >= 1;
    }

    public function getProgress($doctorId)
    {
        // Calculate current month's delivered product quantity dynamically
        $currentMonthQuantity = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.doctor_id', $doctorId)
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->sum('order_items.quantity') ?? 0;

        $target = DoctorTarget::where('doctor_id', $doctorId)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        // Check special spin product requirement
        $specialProduct = Product::where('is_special_spin_product', true)->first();
        $specialProductId = $specialProduct ? $specialProduct->id : null;
        $specialProductName = $specialProduct ? $specialProduct->name : null;
        $hasSoldSpecialProduct = $specialProduct ? $this->hasDoctorSoldSpecialProduct($doctorId, $specialProduct->id) : false;

        // Calculate eligible spins: floor(products / 30) capped by special product count
        $eligibleSpins = $this->getEligibleSpins($doctorId);
        $remainingSpins = $this->getRemainingSpins($doctorId);

        $progressData = [
            'current' => $currentMonthQuantity,
            'target' => 30, // Product-based target (30 products)
            'percentage' => min(100, round(($currentMonthQuantity / 30) * 100)),
            'completed' => $currentMonthQuantity >= 30 && $hasSoldSpecialProduct,
            'spin_eligible' => $remainingSpins > 0,
            'eligible_spins' => $eligibleSpins,
            'remaining_spins' => $remainingSpins,
            'already_spun' => $target ? (bool) $target->spin_completed_at : false,
            'special_product_id' => $specialProductId,
            'special_product_name' => $specialProductName,
            'has_sold_special_product' => $hasSoldSpecialProduct,
            'special_product_configured' => $specialProduct !== null,
        ];

        return $progressData;
    }

    /**
     * Get target history for a doctor
     */
    public function getTargetHistory($doctorId, $months = 12)
    {
        return DoctorTarget::where('doctor_id', $doctorId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit($months)
            ->get();
    }
}
