<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class GoodsReceivedNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'grn_number',
        'received_by',
        'received_date',
        'notes',
    ];

    protected $casts = [
        'received_date' => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Generate unique GRN number
     */
    public static function generateGrnNumber(): string
    {
        $prefix = 'GRN';
        $date = now()->format('Ymd');
        $lastGrn = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastGrn ? (int) substr($lastGrn->grn_number, -4) + 1 : 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Process GRN - Create batches and update inventory
     * This is called after GRN is saved
     */
    public function processReceipt(array $items): void
    {
        DB::transaction(function () use ($items) {
            $purchaseOrder = $this->purchaseOrder;
            $supplierId = $purchaseOrder->supplier_id;

            foreach ($items as $itemData) {
                $poItem = PurchaseOrderItem::findOrFail($itemData['purchase_order_item_id']);
                $receivedQty = (int) $itemData['received_quantity'];

                if ($receivedQty <= 0) {
                    continue;
                }

                // Update received quantity on PO item
                $poItem->received_quantity += $receivedQty;
                $poItem->save();

                // Create product batch from received goods
                $batch = ProductBatch::create([
                    'product_id' => $poItem->product_id,
                    'supplier_id' => $supplierId,
                    'batch_number' => $poItem->batch_number ?? $this->grn_number . '-' . $poItem->id,
                    'expiry_date' => $poItem->expiry_date ?? now()->addYear(),
                    'quantity' => $receivedQty,
                    'mrp' => $poItem->mrp,
                ]);

                // Log to stock ledger
                StockLedger::create([
                    'product_id' => $poItem->product_id,
                    'batch_no' => $batch->batch_number,
                    'type' => 'BATCH_CREATED',
                    'quantity' => $receivedQty,
                    'reference_id' => $this->id,
                    'reference_type' => self::class,
                    'remarks' => "GRN {$this->grn_number} - Purchase {$purchaseOrder->order_number}",
                    'created_by' => $this->received_by,
                ]);

                StockLedger::create([
                    'product_id' => $poItem->product_id,
                    'batch_no' => $batch->batch_number,
                    'type' => 'PURCHASE_RECEIVED',
                    'quantity' => $receivedQty,
                    'reference_id' => $this->id,
                    'reference_type' => self::class,
                    'remarks' => "Received from {$purchaseOrder->supplier->name}",
                    'created_by' => $this->received_by,
                ]);
            }

            // Update purchase order status
            $purchaseOrder->updateStatus();
        });
    }
}
