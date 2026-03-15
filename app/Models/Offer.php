<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Offer extends Model
{
    protected $fillable = [
        'title',
        'target_audience',
        'description',
        'offer_type',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
        'featured_image',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'offer_products');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhereDate('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', now());
            });
    }

    public function scopeDaily($query)
    {
        return $query->where('offer_type', 'daily');
    }

    public function scopeForUsers($query)
    {
        return $query->where('target_audience', 'user');
    }

    public function scopeForStores($query)
    {
        return $query->where('target_audience', 'store');
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->start_date && $this->start_date > now()) return false;
        if ($this->end_date && $this->end_date < now()) return false;
        return true;
    }

    public function getDiscountDisplayAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '% OFF';
        }
        return '₹' . number_format($this->discount_value, 0) . ' OFF';
    }
}
