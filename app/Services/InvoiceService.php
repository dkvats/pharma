<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate invoice from order
     */
    public function generateFromOrder(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            // Check if invoice already exists
            if ($order->invoice) {
                return $order->invoice;
            }

            // Calculate totals
            $subtotal = 0;
            $totalDiscount = 0;
            $totalGst = 0;

            // Create invoice
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_date' => now(),
                'status' => 'generated',
                'created_by' => auth()->id() ?? $order->user_id,
            ]);

            // Create invoice items from order items
            foreach ($order->items as $orderItem) {
                $itemDetails = $this->calculateItemDetails($orderItem);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $orderItem->product_id,
                    'product_name' => $orderItem->product->name,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->price,
                    'discount_amount' => $itemDetails['discount_amount'],
                    'taxable_value' => $itemDetails['taxable_value'],
                    'gst_percent' => $itemDetails['gst_percent'],
                    'gst_amount' => $itemDetails['gst_amount'],
                    'total_amount' => $itemDetails['total_amount'],
                ]);

                $subtotal += $itemDetails['subtotal'];
                $totalDiscount += $itemDetails['discount_amount'];
                $totalGst += $itemDetails['gst_amount'];
            }

            // Update invoice totals
            $taxableAmount = $subtotal - $totalDiscount;
            $grandTotal = $taxableAmount + $totalGst;

            $invoice->update([
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'taxable_amount' => $taxableAmount,
                'gst_amount' => $totalGst,
                'grand_total' => $grandTotal,
            ]);

            // Log to stock ledger
            $this->logInvoiceGeneration($invoice);

            return $invoice->fresh('items');
        });
    }

    /**
     * Calculate item details with GST
     */
    private function calculateItemDetails(OrderItem $orderItem): array
    {
        $product = $orderItem->product;
        $quantity = $orderItem->quantity;
        $unitPrice = $orderItem->price;
        
        // Get GST percent from product or default
        $gstPercent = $product->gst_percent ?? 0;
        
        // Calculate line total
        $lineTotal = $unitPrice * $quantity;
        
        // Calculate discount (if any)
        $discountAmount = 0;
        if ($product->discount_amount > 0) {
            $discountAmount = $product->discount_amount * $quantity;
        }
        
        // Calculate taxable value (after discount)
        $taxableValue = $lineTotal - $discountAmount;
        
        // Calculate GST
        $gstAmount = round($taxableValue * ($gstPercent / 100), 2);
        
        // Calculate total with GST
        $totalAmount = $taxableValue + $gstAmount;

        return [
            'subtotal' => $lineTotal,
            'discount_amount' => $discountAmount,
            'taxable_value' => $taxableValue,
            'gst_percent' => $gstPercent,
            'gst_amount' => $gstAmount,
            'total_amount' => $totalAmount,
        ];
    }

    /**
     * Calculate GST for a given amount and rate
     */
    public static function calculateGst(float $amount, float $gstPercent): float
    {
        return round($amount * ($gstPercent / 100), 2);
    }

    /**
     * Calculate total with GST
     */
    public static function calculateTotalWithGst(float $amount, float $gstPercent): float
    {
        $gst = self::calculateGst($amount, $gstPercent);
        return $amount + $gst;
    }

    /**
     * Get GST breakdown for display
     */
    public function getGstBreakdownForDisplay(Invoice $invoice): array
    {
        $breakdown = $invoice->getGstBreakdown();
        $result = [];

        foreach ($breakdown as $rate => $data) {
            $result[] = [
                'rate' => $rate,
                'rate_display' => $rate . '%',
                'taxable_value' => $data['taxable_value'],
                'cgst' => $data['gst_amount'] / 2, // Assuming equal split
                'sgst' => $data['gst_amount'] / 2,
                'igst' => $data['gst_amount'],
                'total_gst' => $data['gst_amount'],
            ];
        }

        return $result;
    }

    /**
     * Log invoice generation to stock ledger
     */
    private function logInvoiceGeneration(Invoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            \App\Models\StockLedger::create([
                'product_id' => $item->product_id,
                'batch_no' => null,
                'type' => 'INVOICE_GENERATED',
                'quantity' => $item->quantity,
                'reference_id' => $invoice->id,
                'reference_type' => Invoice::class,
                'remarks' => "Invoice {$invoice->invoice_number} generated for {$item->quantity} units",
                'created_by' => $invoice->created_by,
            ]);
        }
    }

    /**
     * Regenerate invoice (for corrections)
     */
    public function regenerateInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            // Delete old items
            $invoice->items()->delete();
            
            // Regenerate from order
            $order = $invoice->order;
            
            $subtotal = 0;
            $totalDiscount = 0;
            $totalGst = 0;

            foreach ($order->items as $orderItem) {
                $itemDetails = $this->calculateItemDetails($orderItem);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $orderItem->product_id,
                    'product_name' => $orderItem->product->name,
                    'quantity' => $orderItem->quantity,
                    'unit_price' => $orderItem->price,
                    'discount_amount' => $itemDetails['discount_amount'],
                    'taxable_value' => $itemDetails['taxable_value'],
                    'gst_percent' => $itemDetails['gst_percent'],
                    'gst_amount' => $itemDetails['gst_amount'],
                    'total_amount' => $itemDetails['total_amount'],
                ]);

                $subtotal += $itemDetails['subtotal'];
                $totalDiscount += $itemDetails['discount_amount'];
                $totalGst += $itemDetails['gst_amount'];
            }

            $taxableAmount = $subtotal - $totalDiscount;
            $grandTotal = $taxableAmount + $totalGst;

            $invoice->update([
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'taxable_amount' => $taxableAmount,
                'gst_amount' => $totalGst,
                'grand_total' => $grandTotal,
            ]);

            return $invoice->fresh('items');
        });
    }
}
