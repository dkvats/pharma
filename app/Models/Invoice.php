<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'subtotal',
        'discount_amount',
        'taxable_amount',
        'gst_amount',
        'grand_total',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastInvoice = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get GST breakdown by rate
     */
    public function getGstBreakdown(): array
    {
        $breakdown = [];
        
        foreach ($this->items as $item) {
            $rate = $item->gst_percent;
            if (!isset($breakdown[$rate])) {
                $breakdown[$rate] = [
                    'rate' => $rate,
                    'taxable_value' => 0,
                    'gst_amount' => 0,
                ];
            }
            $breakdown[$rate]['taxable_value'] += $item->taxable_value;
            $breakdown[$rate]['gst_amount'] += $item->gst_amount;
        }
        
        return $breakdown;
    }

    /**
     * Get amount in words
     */
    public function getAmountInWords(): string
    {
        $amount = $this->grand_total;
        // Simple implementation - can be enhanced with a proper number-to-words library
        return 'Rupees ' . number_format($amount, 2) . ' only';
    }

    /**
     * Scope for invoices by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('invoice_date', [$from, $to]);
    }
}
