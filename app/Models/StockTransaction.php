<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_id',
        'transaction_type',
        'quantity',
        'opening_balance',
        'closing_balance',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'opening_balance' => 'integer',
        'closing_balance' => 'integer',
    ];

    /**
     * Get the store that owns this transaction
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_id');
    }

    /**
     * Get the product for this transaction
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for filtering by store
     */
    public function scopeForStore($query, int $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Scope for filtering by product
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Get transaction type badge color
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->transaction_type) {
            'purchase' => 'green',
            'sale' => 'blue',
            'adjustment' => 'amber',
            default => 'gray',
        };
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->transaction_type) {
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'adjustment' => 'Adjustment',
            default => ucfirst($this->transaction_type),
        };
    }

    /**
     * STEP 3: Get the change value (positive for purchase, negative for sale)
     */
    public function getChangeAttribute(): int
    {
        return match($this->transaction_type) {
            'purchase' => $this->quantity,
            'sale' => -$this->quantity,
            'adjustment' => $this->quantity, // Adjustments can be positive or negative based on context
            default => $this->quantity,
        };
    }
}
