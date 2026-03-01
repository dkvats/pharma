<?php

namespace App\Services;

use App\Models\Order;
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

                $storeStock->addStock($item->quantity);
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
    public function recordSale(int $storeId, int $productId, int $quantity): bool
    {
        $storeStock = StoreStock::where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();

        if (!$storeStock) {
            return false;
        }

        return $storeStock->recordSale($quantity);
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
