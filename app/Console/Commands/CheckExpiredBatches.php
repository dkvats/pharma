<?php

namespace App\Console\Commands;

use App\Models\ExpiredBatch;
use App\Models\ProductBatch;
use Illuminate\Console\Command;

class CheckExpiredBatches extends Command
{
    protected $signature = 'inventory:check-expired
                            {--dry-run : Preview what would be inserted without writing to database}';

    protected $description = 'Find expired product batches and record them in the expired_batches table for return/disposal tracking.';

    public function handle(): int
    {
        $this->info('Checking for expired batches...');

        // Find all expired batches not yet in expired_batches table
        $alreadyTracked = ExpiredBatch::pluck('product_batch_id')->toArray();

        $expiredBatches = ProductBatch::with('product')
            ->whereDate('expiry_date', '<', now())
            ->where('quantity', '>', 0)
            ->whereNotIn('id', $alreadyTracked)
            ->get();

        if ($expiredBatches->isEmpty()) {
            $this->info('No new expired batches found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredBatches->count()} new expired batch(es).");

        if ($this->option('dry-run')) {
            $this->table(
                ['Batch ID', 'Product', 'Batch #', 'Expiry Date', 'Quantity'],
                $expiredBatches->map(fn ($b) => [
                    $b->id,
                    $b->product->name,
                    $b->batch_number,
                    $b->expiry_date->toDateString(),
                    $b->quantity,
                ])
            );
            $this->warn('Dry-run mode: No records inserted.');
            return Command::SUCCESS;
        }

        $inserted = 0;
        foreach ($expiredBatches as $batch) {
            ExpiredBatch::create([
                'product_batch_id' => $batch->id,
                'product_id'       => $batch->product_id,
                'batch_number'     => $batch->batch_number,
                'expiry_date'      => $batch->expiry_date,
                'quantity'         => $batch->quantity,
                'status'           => 'pending_return',
            ]);
            $inserted++;
            $this->line("  + Recorded: {$batch->product->name} — Batch {$batch->batch_number} (exp. {$batch->expiry_date->toDateString()}, qty: {$batch->quantity})");
        }

        $this->info("Done. {$inserted} expired batch(es) recorded for return/disposal.");
        return Command::SUCCESS;
    }
}
