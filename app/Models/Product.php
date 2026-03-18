<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'brand',
        'company',
        'sku',
        'unit_type',
        'price',
        'mrp',
        'discount_amount',
        'gst_percent',
        'commission',
        'stock',
        'batch_number',
        'expiry_date',
        'low_stock_alert',
        'requires_prescription',
        'is_special_spin_product',
        'featured_on_homepage',
        'image',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'commission' => 'decimal:2',
        'expiry_date' => 'date',
        'requires_prescription' => 'boolean',
        'is_special_spin_product' => 'boolean',
        'featured_on_homepage' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function doctorPrices(): HasMany
    {
        return $this->hasMany(DoctorProductPrice::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProductLog::class);
    }

    /**
     * Get total stock from all active (non-expired) batches
     */
    public function getBatchStockAttribute(): int
    {
        return $this->batches()
            ->whereDate('expiry_date', '>=', now())
            ->sum('quantity');
    }

    /**
     * Recalculate and sync products.stock from batch totals
     */
    public function syncStockFromBatches(): void
    {
        $total = $this->batches()
            ->whereDate('expiry_date', '>=', now())
            ->sum('quantity');

        $this->updateQuietly(['stock' => $total]);
    }

    /**
     * Alias used in FEFO order deduction: reduce batch quantity FEFO style
     * Returns false if insufficient stock across all active batches
     */
    public function deductBatchStockFefo(int $qty): bool
    {
        $remaining = $qty;
        $batches = $this->batches()
            ->whereDate('expiry_date', '>=', now())
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date') // FEFO: First Expiry First Out
            ->lockForUpdate()
            ->get();

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $deduct = min($batch->quantity, $remaining);
            $batch->decrement('quantity', $deduct);
            $remaining -= $deduct;
        }

        if ($remaining > 0) {
            return false; // insufficient stock
        }

        $this->syncStockFromBatches();
        return true;
    }

    /**
     * Get price for a specific doctor (with special pricing support)
     */
    public function getPriceForDoctor(?int $doctorId): float
    {
        if (!$doctorId) {
            return $this->price;
        }

        $specialPrice = $this->doctorPrices()
            ->where('doctor_id', $doctorId)
            ->first();

        return $specialPrice ? $specialPrice->special_price : $this->price;
    }

    /**
     * Calculate price with GST
     */
    public function getPriceWithGstAttribute(): float
    {
        return $this->price * (1 + $this->gst_percent / 100);
    }

    /**
     * Check if product has any expired batches
     */
    public function hasExpiredBatches(): bool
    {
        return $this->batches()
            ->whereDate('expiry_date', '<', now())
            ->exists();
    }

    /**
     * Check if product has batches expiring soon
     */
    public function hasExpiringBatches(int $days = 30): bool
    {
        return $this->batches()
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->exists();
    }

    /**
     * Get nearest expiry date from active batches
     */
    public function getNearestExpiryDateAttribute(): ?string
    {
        $nearest = $this->batches()
            ->whereDate('expiry_date', '>=', now())
            ->orderBy('expiry_date')
            ->first();

        return $nearest ? $nearest->expiry_date->format('Y-m-d') : null;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= $this->low_stock_alert) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Calculate discount percentage based on MRP and final price
     */
    public function getDiscountPercentageAttribute(): int
    {
        if ($this->mrp <= 0 || $this->mrp <= $this->price) {
            return 0;
        }

        return round((($this->mrp - $this->price) / $this->mrp) * 100);
    }
}
