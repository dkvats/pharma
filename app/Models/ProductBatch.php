<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_id',
        'batch_number',
        'manufacture_date',
        'expiry_date',
        'quantity',
        'mrp',
    ];

    protected $casts = [
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'mrp' => 'decimal:2',
    ];

    /**
     * Auto-sync parent product stock whenever a batch is saved or deleted
     */
    protected static function booted(): void
    {
        static::saved(function (ProductBatch $batch) {
            $batch->product->syncStockFromBatches();
        });

        static::deleted(function (ProductBatch $batch) {
            $batch->product->syncStockFromBatches();
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function storeInventories(): HasMany
    {
        return $this->hasMany(StoreInventory::class, 'product_batch_id');
    }

    /**
     * Check if batch is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date->isPast();
    }

    /**
     * Check if batch is expiring soon (within given days)
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if ($this->isExpired()) {
            return false;
        }
        return $this->expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Get expiry status badge class
     */
    public function getExpiryBadgeClass(): string
    {
        if ($this->isExpired()) {
            return 'bg-red-100 text-red-800';
        }
        if ($this->isExpiringSoon(30)) {
            return 'bg-yellow-100 text-yellow-800';
        }
        if ($this->isExpiringSoon(60)) {
            return 'bg-blue-100 text-blue-800';
        }
        return 'bg-green-100 text-green-800';
    }

    /**
     * Get expiry status text
     */
    public function getExpiryStatusText(): string
    {
        if ($this->isExpired()) {
            return 'Expired';
        }
        if ($this->isExpiringSoon(30)) {
            return 'Expiring Soon';
        }
        if ($this->isExpiringSoon(60)) {
            return 'Expiring in 60 days';
        }
        return 'Good';
    }

    /**
     * Scope for expired batches
     */
    public function scopeExpired($query)
    {
        return $query->whereDate('expiry_date', '<', now());
    }

    /**
     * Scope for expiring soon batches
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereDate('expiry_date', '>=', now())
                     ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    /**
     * Scope for active (non-expired) batches
     */
    public function scopeActive($query)
    {
        return $query->whereDate('expiry_date', '>=', now());
    }
}
