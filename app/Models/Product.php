<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'mrp',
        'discount_amount',
        'commission',
        'stock',
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
        'commission' => 'decimal:2',
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

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->stock < 10) {
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
