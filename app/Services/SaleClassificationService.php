<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class SaleClassificationService
{
    /**
     * Classify the sale type based on order data
     */
    public function classifySaleType(Order $order): string
    {
        // Check if doctor is placing order for themselves (Doctor Direct Sale)
        if ($order->doctor_id && $order->user_id === $order->doctor_id) {
            return 'doctor_direct';
        }

        // Check if referral code is present (Referral Sale)
        if ($order->referral_code) {
            return 'referral';
        }

        // Check if store is placing order (Store Linked Sale)
        if ($order->store_id) {
            return 'store_linked';
        }

        // Default: Company Direct Sale (End User direct order)
        return 'company_direct';
    }

    /**
     * Validate referral code
     */
    public function validateReferralCode(string $code): ?User
    {
        return User::where('code', $code)
            ->where('status', 'active')
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Doctor', 'Store']);
            })
            ->first();
    }

    /**
     * Get sale type label
     */
    public function getSaleTypeLabel(string $type): string
    {
        return match($type) {
            'doctor_direct' => 'Doctor Direct Sale',
            'referral' => 'Referral Sale',
            'store_linked' => 'Store Linked Sale',
            'company_direct' => 'Company Direct Sale',
            default => 'Unknown',
        };
    }

    /**
     * Get commission rate based on sale type
     */
    public function getCommissionRate(string $saleType): float
    {
        return match($saleType) {
            'doctor_direct' => 0.10,  // 10% commission
            'referral' => 0.05,       // 5% commission
            'store_linked' => 0.08,   // 8% commission
            'company_direct' => 0.00, // No commission
            default => 0.00,
        };
    }
}
