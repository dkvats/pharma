<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:cleanup {--days=365 : Number of days to retain logs} {--chunk=1000 : Number of records to delete per chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete activity logs older than specified days using chunked deletion (default: 365 days, 1000 per chunk)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $chunkSize = $this->option('chunk');
        $cutoffDate = now()->subDays($days);
        
        $this->info("Deleting activity logs older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");
        $this->info("Using chunk size: {$chunkSize} records per batch");
        
        $totalDeleted = 0;
        $batchCount = 0;
        
        // Use chunked deletion to prevent long table locks
        // This is critical for production tables with millions of rows
        while (true) {
            // Get IDs to delete (limited by chunk size)
            $ids = ActivityLog::where('created_at', '<', $cutoffDate)
                ->limit($chunkSize)
                ->pluck('id');
            
            // Clean exit condition: break immediately when no more rows
            if ($ids->isEmpty()) {
                break;
            }
            
            // Delete this batch
            $deleted = ActivityLog::whereIn('id', $ids)->delete();
            $totalDeleted += $deleted;
            $batchCount++;
            
            $this->info("Batch {$batchCount}: Deleted {$deleted} records (Total: {$totalDeleted})");
            
            // Optional: Sleep briefly to reduce database load
            usleep(100000); // 100ms pause between chunks
        }
        
        $this->info("Cleanup complete. Total deleted: {$totalDeleted} activity logs in {$batchCount} batches.");
        
        return Command::SUCCESS;
    }
}
