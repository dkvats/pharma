<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLog extends Model
{
    protected $fillable = [
        'product_id',
        'action',
        'old_value',
        'new_value',
        'changed_by',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Log a product creation
     */
    public static function logCreated(Product $product, int $userId): void
    {
        self::create([
            'product_id' => $product->id,
            'action' => 'created',
            'new_value' => $product->toArray(),
            'changed_by' => $userId,
        ]);
    }

    /**
     * Log a product update
     */
    public static function logUpdated(Product $product, array $oldValues, int $userId): void
    {
        self::create([
            'product_id' => $product->id,
            'action' => 'updated',
            'old_value' => $oldValues,
            'new_value' => $product->fresh()->toArray(),
            'changed_by' => $userId,
        ]);
    }

    /**
     * Log a price change
     */
    public static function logPriceChanged(Product $product, float $oldPrice, float $newPrice, int $userId): void
    {
        self::create([
            'product_id' => $product->id,
            'action' => 'price_changed',
            'old_value' => ['price' => $oldPrice],
            'new_value' => ['price' => $newPrice],
            'changed_by' => $userId,
        ]);
    }

    /**
     * Log a stock update
     */
    public static function logStockUpdated(Product $product, int $oldStock, int $newStock, int $userId): void
    {
        self::create([
            'product_id' => $product->id,
            'action' => 'stock_updated',
            'old_value' => ['stock' => $oldStock],
            'new_value' => ['stock' => $newStock],
            'changed_by' => $userId,
        ]);
    }
}
