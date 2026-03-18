<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Stock Ledger - For logging only, NOT source of truth
 * 
 * This model is used purely for audit logging of inventory events.
 * The actual stock quantities are maintained in product_batches table.
 */
class StockLedger extends Model
{
    use HasFactory;

    protected $table = 'stock_ledger';

    protected $fillable = [
        'product_id',
        'batch_no',
        'type',
        'quantity',
        'reference_id',
        'reference_type',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Log a batch creation event
     */
    public static function logBatchCreated(ProductBatch $batch, ?string $remarks = null): self
    {
        return self::create([
            'product_id' => $batch->product_id,
            'batch_no' => $batch->batch_number,
            'type' => 'BATCH_CREATED',
            'quantity' => $batch->quantity,
            'reference_id' => $batch->id,
            'reference_type' => ProductBatch::class,
            'remarks' => $remarks ?? "Batch {$batch->batch_number} created",
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Log a batch allocation to store event
     */
    public static function logBatchAllocated(StoreInventory $allocation, ?string $remarks = null): self
    {
        return self::create([
            'product_id' => $allocation->productBatch->product_id,
            'batch_no' => $allocation->productBatch->batch_number,
            'type' => 'BATCH_ALLOCATED',
            'quantity' => $allocation->quantity,
            'reference_id' => $allocation->id,
            'reference_type' => StoreInventory::class,
            'remarks' => $remarks ?? "Allocated to store",
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Log an order placement event
     */
    public static function logOrderPlaced(Order $order, OrderItem $item, ?string $remarks = null): self
    {
        return self::create([
            'product_id' => $item->product_id,
            'batch_no' => null,
            'type' => 'ORDER_PLACED',
            'quantity' => $item->quantity,
            'reference_id' => $order->id,
            'reference_type' => Order::class,
            'remarks' => $remarks ?? "Order #{$order->id} placed",
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Log stock reduction event
     */
    public static function logStockReduced(ProductBatch $batch, int $quantity, ?string $remarks = null): self
    {
        return self::create([
            'product_id' => $batch->product_id,
            'batch_no' => $batch->batch_number,
            'type' => 'STOCK_REDUCED',
            'quantity' => $quantity,
            'reference_id' => $batch->id,
            'reference_type' => ProductBatch::class,
            'remarks' => $remarks ?? "Stock reduced by {$quantity}",
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Log expired batch event
     */
    public static function logExpired(ProductBatch $batch, ?string $remarks = null): self
    {
        return self::create([
            'product_id' => $batch->product_id,
            'batch_no' => $batch->batch_number,
            'type' => 'EXPIRED',
            'quantity' => $batch->quantity,
            'reference_id' => $batch->id,
            'reference_type' => ProductBatch::class,
            'remarks' => $remarks ?? "Batch expired on {$batch->expiry_date->format('Y-m-d')}",
            'created_by' => auth()->id(),
        ]);
    }
}
