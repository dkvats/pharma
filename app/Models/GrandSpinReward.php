<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrandSpinReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'stock',
        'image',
        'probability',
        'is_active',
        'force_equal_distribution',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'force_equal_distribution' => 'boolean',
    ];

    /**
     * Get active rewards for grand spin
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('stock')->orWhere('stock', '>', 0);
            })
            ->get();
    }

    /**
     * Select a reward based on probability (or equal distribution if enabled)
     */
    public static function selectReward(): ?self
    {
        $rewards = self::getActive();

        if ($rewards->isEmpty()) {
            return null;
        }

        // Check if any reward has force_equal_distribution enabled
        $forceEqual = $rewards->contains('force_equal_distribution', true);

        if ($forceEqual) {
            // Sequential distribution - pick the first available
            return $rewards->first();
        }

        // Weighted random selection
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
     * Decrement stock if applicable
     */
    public function decrementStock(): void
    {
        if ($this->stock !== null && $this->stock > 0) {
            $this->decrement('stock');
        }
    }
}
