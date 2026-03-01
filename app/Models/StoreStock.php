<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_id',
        'quantity',
        'sold_quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sold_quantity' => 'integer',
    ];

    /**
     * Get the store that owns this stock
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_id');
    }

    /**
     * Get the product for this stock
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get available stock (quantity - sold_quantity)
     */
    public function getAvailableStockAttribute(): int
    {
        return $this->quantity - $this->sold_quantity;
    }

    /**
     * Add stock quantity
     */
    public function addStock(int $amount): void
    {
        $this->increment('quantity', $amount);
    }

    /**
     * Record sold quantity
     */
    public function recordSale(int $amount): bool
    {
        if ($this->available_stock < $amount) {
            return false;
        }

        $this->increment('sold_quantity', $amount);
        return true;
    }
}
