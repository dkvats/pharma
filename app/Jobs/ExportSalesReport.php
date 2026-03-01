<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportSalesReport implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 300; // 5 minutes

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    protected $filters;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $filters, int $userId)
    {
        $this->filters = $filters;
        $this->userId = $userId;
    }

    /**
     * Execute the job with memory-safe chunking.
     */
    public function handle(): void
    {
        $filename = 'sales_report_' . now()->format('Y-m-d_His') . '.csv';
        $path = 'exports/' . $filename;
        
        // Create exports directory if needed
        if (!Storage::exists('exports')) {
            Storage::makeDirectory('exports');
        }

        // Build query with filters
        $query = Order::query();
        
        if (!empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }
        if (!empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Write CSV header
        $csvContent = "Order Number,Customer,Status,Sale Type,Total Amount,Date\n";
        Storage::put($path, $csvContent);

        // Process in chunks of 500 to prevent memory exhaustion
        $query->chunkById(500, function ($orders) use ($path) {
            $chunk = '';
            foreach ($orders as $order) {
                $chunk .= sprintf(
                    "%s,%s,%s,%s,%s,%s\n",
                    $order->order_number,
                    $order->user?->name ?? 'N/A',
                    $order->status,
                    $order->sale_type ?? 'N/A',
                    $order->total_amount,
                    $order->created_at->format('Y-m-d H:i:s')
                );
            }
            // Append chunk to file
            Storage::append($path, $chunk);
        });

        // Log completion
        ActivityLogService::log(
            'export_completed',
            null,
            "Sales report exported: {$filename} by user {$this->userId}"
        );

        // Store reference for user download (could be stored in database table)
        // For now, file is available at: storage/app/exports/{$filename}
    }
}
