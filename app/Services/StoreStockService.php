<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StoreSale;
use App\Models\StoreStock;
use Illuminate\Support\Facades\DB;

class StoreStockService
{
    /**
     * Add order items to store stock when order is delivered
     */
    public function addOrderToStoreStock(Order $order): void
    {
        if (!$order->store_id) {
            return;
        }

        // Load items if not already loaded
        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $storeStock = StoreStock::firstOrCreate(
                    [
                        'store_id' => $order->store_id,
                        'product_id' => $item->product_id,
                    ],
                    [
                        'quantity' => 0,
                        'sold_quantity' => 0,
                    ]
                );

                // Calculate opening balance before adding stock
                $openingBalance = $storeStock->available_stock;

                $storeStock->addStock($item->quantity);

                // Fetch product name for historical accuracy
                $product = Product::find($item->product_id);

                // Log stock transaction for purchase
                StockTransaction::create([
                    'store_id' => $order->store_id,
                    'product_id' => $item->product_id,
                    'product_name' => $product ? $product->name : 'Unknown Product',
                    'transaction_type' => 'purchase',
                    'quantity' => $item->quantity,
                    'opening_balance' => $openingBalance,
                    'closing_balance' => $openingBalance + $item->quantity,
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                ]);
            }

            // Log activity
            ActivityLogService::log(
                'store_stock_added',
                $order,
                "Stock added to store {$order->store_id} for delivered order {$order->order_number}"
            );
        });
    }

    /**
     * Record sale from store stock
     */
    public function recordSale(int $storeId, int $productId, int $quantity, ?string $referenceType = null, ?int $referenceId = null): bool
    {
        // STEP 2: Wrap stock update and ledger insert in same transaction
        return DB::transaction(function () use ($storeId, $productId, $quantity, $referenceType, $referenceId) {
            // STEP 3: Lock row for update to prevent race conditions
            $storeStock = StoreStock::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (!$storeStock) {
                return false;
            }

            // Calculate opening balance before sale
            $openingBalance = $storeStock->available_stock;
            $closingBalance = $openingBalance - $quantity;

            // STEP 2: Prevent negative stock
            if ($closingBalance < 0) {
                throw new \RuntimeException(
                    "Insufficient stock for '{$storeStock->product->name}'. "
                    . "Available: {$openingBalance}, Requested: {$quantity}"
                );
            }

            $success = $storeStock->recordSale($quantity);

            if ($success) {
                // Fetch product name for historical accuracy
                $product = Product::find($productId);

                // Log stock transaction for sale
                StockTransaction::create([
                    'store_id' => $storeId,
                    'product_id' => $productId,
                    'product_name' => $product ? $product->name : 'Unknown Product',
                    'transaction_type' => 'sale',
                    'quantity' => $quantity,
                    'opening_balance' => $openingBalance,
                    'closing_balance' => $closingBalance,
                    'reference_type' => $referenceType ?? 'sale',
                    'reference_id' => $referenceId,
                ]);
            }

            return $success;
        });
    }

    /**
     * Get store stock with product details
     * Sold quantity is calculated from store_sales table
     */
    public function getStoreStock(int $storeId, ?int $productId = null)
    {
        $query = StoreStock::with('product')
            ->where('store_id', $storeId);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $stocks = $query->get();

        // Calculate sold quantity from store_sales for each stock
        foreach ($stocks as $stock) {
            $soldFromSales = StoreSale::where('store_id', $storeId)
                ->where('product_id', $stock->product_id)
                ->sum('quantity');

            // Update the sold_quantity to reflect actual sales
            $stock->sold_quantity = $soldFromSales;
        }

        return $stocks;
    }

    /**
     * Get all stores stock for admin monitoring
     */
    public function getAllStoresStock(?int $storeId = null, ?int $productId = null)
    {
        $query = StoreStock::with(['store', 'product']);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->paginate(20);
    }
}
