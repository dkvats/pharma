<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'order_number',
        'order_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns(): HasMany
    {
        return $this->hasMany(GoodsReceivedNote::class);
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'PO';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if order is fully received
     */
    public function isFullyReceived(): bool
    {
        foreach ($this->items as $item) {
            if ($item->received_quantity < $item->quantity) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get total quantity ordered
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get total quantity received
     */
    public function getTotalReceivedQuantityAttribute(): int
    {
        return $this->items->sum('received_quantity');
    }

    /**
     * Update status based on received quantities
     */
    public function updateStatus(): void
    {
        if ($this->isFullyReceived()) {
            $this->status = 'fully_received';
        } elseif ($this->total_received_quantity > 0) {
            $this->status = 'partially_received';
        }
        $this->save();
    }

    /**
     * Scope for pending orders (not fully received)
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'ordered', 'partially_received']);
    }

    /**
     * Scope for orders ready to receive
     */
    public function scopeReadyToReceive($query)
    {
        return $query->whereIn('status', ['ordered', 'partially_received']);
    }
}
