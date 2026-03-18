<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpiredBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExpiredBatchController extends Controller
{
    /**
     * List all expired batches with filter by status.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending_return');

        $query = ExpiredBatch::with('product')
            ->orderBy('expiry_date', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $expiredBatches = $query->paginate(25)->withQueryString();

        $counts = [
            'pending_return' => ExpiredBatch::where('status', 'pending_return')->count(),
            'returned'       => ExpiredBatch::where('status', 'returned')->count(),
            'disposed'       => ExpiredBatch::where('status', 'disposed')->count(),
            'all'            => ExpiredBatch::count(),
        ];

        return view('admin.inventory.expired-batches.index', compact('expiredBatches', 'status', 'counts'));
    }

    /**
     * Mark a single expired batch as returned.
     */
    public function markReturned(ExpiredBatch $expiredBatch)
    {
        $expiredBatch->update(['status' => 'returned']);
        return back()->with('success', "Batch \"{$expiredBatch->batch_number}\" marked as Returned.");
    }

    /**
     * Mark a single expired batch as disposed.
     */
    public function markDisposed(ExpiredBatch $expiredBatch)
    {
        $expiredBatch->update(['status' => 'disposed']);
        return back()->with('success', "Batch \"{$expiredBatch->batch_number}\" marked as Disposed.");
    }

    /**
     * Bulk status update.
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids'    => ['required', 'array'],
            'ids.*'  => ['integer', 'exists:expired_batches,id'],
            'action' => ['required', 'in:returned,disposed'],
        ]);

        ExpiredBatch::whereIn('id', $validated['ids'])
            ->update(['status' => $validated['action']]);

        $count  = count($validated['ids']);
        $label  = $validated['action'] === 'returned' ? 'Returned' : 'Disposed';
        return back()->with('success', "{$count} batch(es) marked as {$label}.");
    }

    /**
     * Download CSV report.
     */
    public function download(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = ExpiredBatch::with('product')->orderBy('expiry_date', 'desc');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $records = $query->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="expired_batches_report.csv"'];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product', 'Batch Number', 'Expiry Date', 'Quantity', 'Status', 'Recorded On']);
            foreach ($records as $row) {
                fputcsv($handle, [
                    $row->product->name ?? 'N/A',
                    $row->batch_number,
                    $row->expiry_date->toDateString(),
                    $row->quantity,
                    $row->status_label,
                    $row->created_at->toDateString(),
                ]);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
