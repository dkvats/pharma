<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\StoreStockService;
use Illuminate\Console\Command;

class BackfillStoreStock extends Command
{
    protected $signature = 'stock:backfill';
    protected $description = 'Backfill store stock for existing delivered orders';

    public function handle(StoreStockService $storeStockService)
    {
        $this->info('Starting store stock backfill...');

        $orders = Order::whereNotNull('store_id')
            ->where('status', 'delivered')
            ->with('items')
            ->get();

        $this->info("Found {$orders->count()} delivered orders with store_id");

        foreach ($orders as $order) {
            $this->info("Processing order: {$order->order_number} (Store: {$order->store_id})");
            
            try {
                $storeStockService->addOrderToStoreStock($order);
                $this->info("  ✓ Stock added successfully");
            } catch (\Exception $e) {
                $this->error("  ✗ Error: {$e->getMessage()}");
            }
        }

        $this->info('Backfill complete!');
        return 0;
    }
}
