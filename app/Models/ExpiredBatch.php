<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpiredBatch extends Model
{
    protected $fillable = [
        'product_batch_id',
        'product_id',
        'batch_number',
        'expiry_date',
        'quantity',
        'status',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePendingReturn($query)
    {
        return $query->where('status', 'pending_return');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeDisposed($query)
    {
        return $query->where('status', 'disposed');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending_return' => 'bg-yellow-100 text-yellow-800',
            'returned'       => 'bg-green-100 text-green-800',
            'disposed'       => 'bg-gray-100 text-gray-800',
            default          => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_return' => 'Pending Return',
            'returned'       => 'Returned',
            'disposed'       => 'Disposed',
            default          => ucfirst($this->status),
        };
    }
}
