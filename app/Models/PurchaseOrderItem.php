<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'quantity',
        'mrp',
        'batch_number',
        'expiry_date',
        'received_quantity',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'mrp' => 'decimal:2',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get remaining quantity to receive
     */
    public function getRemainingQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->received_quantity);
    }

    /**
     * Check if fully received
     */
    public function isFullyReceived(): bool
    {
        return $this->received_quantity >= $this->quantity;
    }

    /**
     * Check if partially received
     */
    public function isPartiallyReceived(): bool
    {
        return $this->received_quantity > 0 && !$this->isFullyReceived();
    }
}
