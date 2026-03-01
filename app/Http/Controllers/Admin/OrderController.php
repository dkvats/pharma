<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ActivityLogService;
use App\Services\DoctorTargetService;
use App\Services\StoreStockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::query();

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by sale type
        if ($request->filled('sale_type')) {
            $query->where('sale_type', $request->sale_type);
        }

        // Filter by Doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by Store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by Store Location (City, District, Tehsil, Village)
        if ($request->filled('city') || $request->filled('district') || $request->filled('tehsil') || $request->filled('village')) {
            $query->whereHas('store', function ($q) use ($request) {
                if ($request->filled('city')) {
                    $q->where('city', $request->city);
                }
                if ($request->filled('district')) {
                    $q->where('district', $request->district);
                }
                if ($request->filled('tehsil')) {
                    $q->where('tehsil', $request->tehsil);
                }
                if ($request->filled('village')) {
                    $q->where('village', $request->village);
                }
            });
        }

        $orders = $query->with(['user', 'doctor', 'store', 'items.product'])
            ->latest()
            ->paginate(10);

        // Get filter dropdown data
        $doctors = \App\Models\User::role('Doctor')->select('id', 'name')->get();
        $stores = \App\Models\User::role('Store')->select('id', 'name', 'city', 'district', 'tehsil', 'village')->get();
        
        // Get unique location values for filters
        $cities = $stores->pluck('city')->filter()->unique()->values();
        $districts = $stores->pluck('district')->filter()->unique()->values();
        $tehsils = $stores->pluck('tehsil')->filter()->unique()->values();
        $villages = $stores->pluck('village')->filter()->unique()->values();

        return view('admin.orders.index', compact('orders', 'doctors', 'stores', 'cities', 'districts', 'tehsils', 'villages'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'doctor', 'store', 'items.product', 'approvedBy']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Approve an order.
     */
    public function approve(Order $order)
    {
        if (!$order->isPending()) {
            return back()->with('error', 'Order is not pending approval.');
        }

        $oldStatus = $order->status;
        
        $order->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        ActivityLogService::logOrderStatusChanged($order, $oldStatus, 'approved');

        // Log to new activity log system
        logActivity(
            'Order Approved',
            'Order',
            $order->id,
            "Order #{$order->order_number} approved"
        );

        // NOTE: Doctor target is NOT incremented on approval
        // Per FRD: Target only updates on delivery confirmation

        // Invalidate dashboard caches
        $this->invalidateDashboardCaches();

        return back()->with('success', 'Order approved successfully.');
    }

    /**
     * Reject an order.
     */
    public function reject(Order $order)
    {
        if (!$order->isPending()) {
            return back()->with('error', 'Order is not pending approval.');
        }

        $oldStatus = $order->status;
        
        $order->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        ActivityLogService::logOrderStatusChanged($order, $oldStatus, 'rejected');

        // Log to new activity log system
        logActivity(
            'Order Rejected',
            'Order',
            $order->id,
            "Order #{$order->order_number} rejected"
        );

        // Decrement doctor target if order was previously approved (rejection after approval)
        if ($order->doctor_id && $oldStatus === 'approved') {
            $totalQuantity = $order->items->sum('quantity');
            $doctorTargetService = new DoctorTargetService();
            $doctorTargetService->decrementTarget($order->doctor_id, $totalQuantity);
        }

        // Restore stock for rejected orders
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return back()->with('success', 'Order rejected successfully.');
    }

    /**
     * Mark order as delivered.
     * FRD COMPLIANCE: Doctor target increments ONLY on delivery confirmation
     */
    public function deliver(Order $order, DoctorTargetService $doctorTargetService, StoreStockService $storeStockService)
    {
        \Log::info('DELIVER METHOD TRIGGERED', ['order_id' => $order->id, 'store_id' => $order->store_id]);

        if (!$order->isApproved()) {
            \Log::info('DELIVER BLOCKED: Order not approved', ['status' => $order->status]);
            return back()->with('error', 'Order must be approved before delivery.');
        }

        // Prevent duplicate delivery processing
        if ($order->isDelivered()) {
            \Log::info('DELIVER BLOCKED: Order already delivered');
            return back()->with('error', 'Order is already delivered.');
        }

        return DB::transaction(function () use ($order, $doctorTargetService, $storeStockService) {
            $oldStatus = $order->status;
            
            $order->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            ActivityLogService::logOrderStatusChanged($order, $oldStatus, 'delivered');

            // FRD COMPLIANCE: Increment doctor target ONLY on delivery confirmation
            // This ensures: cancelled/rejected orders don't count, duplicate delivery doesn't double-count
            if ($order->doctor_id) {
                $totalQuantity = $order->items->sum('quantity');
                $doctorTargetService->incrementTarget($order->doctor_id, $totalQuantity);
                
                // Log target increment for audit
                ActivityLogService::log(
                    'target_increment',
                    $order->doctor,
                    "Doctor target incremented by {$totalQuantity} for delivered order {$order->order_number}"
                );
            }

            // Add stock to store when order is delivered (for store-linked orders)
            if ($order->store_id) {
                $storeStockService->addOrderToStoreStock($order);
            }

            // Invalidate caches after delivery
            $this->invalidateDashboardCaches();
            if ($order->doctor_id) {
                Cache::forget("doctor_report_{$order->doctor_id}_" . now()->format('Y-m-d'));
            }
            
            // Invalidate leaderboard caches
            Cache::forget('leaderboard_monthly_' . now()->format('Y_m'));
            Cache::forget('leaderboard_all_time');

            // Log to new activity log system
            logActivity(
                'Order Delivered',
                'Order',
                $order->id,
                "Order #{$order->order_number} marked as delivered"
            );

            return back()->with('success', 'Order marked as delivered successfully.');
        });
    }

    /**
     * Invalidate dashboard caches
     */
    private function invalidateDashboardCaches(): void
    {
        // Admin dashboard cache (hourly key)
        Cache::forget('admin_dashboard_' . now()->format('Y-m-d_H'));
        
        // Also invalidate previous hour just in case
        Cache::forget('admin_dashboard_' . now()->subHour()->format('Y-m-d_H'));
    }

    /**
     * Generate bill for approved/delivered order
     */
    public function generateBill(Order $order)
    {
        if (!$order->isApproved() && !$order->isDelivered()) {
            return back()->with('error', 'Order must be approved or delivered to generate bill.');
        }

        if ($order->bill_generated) {
            return back()->with('error', 'Bill has already been generated for this order.');
        }

        // Load relationships
        $order->load(['user', 'doctor', 'store', 'items.product', 'approvedBy']);

        // Generate PDF
        $pdf = PDF::loadView('admin.orders.bill', compact('order'));
        
        // Generate filename
        $filename = 'bill-' . $order->order_number . '.pdf';
        
        // Store PDF
        $pdfContent = $pdf->output();
        Storage::disk('public')->put('bills/' . $filename, $pdfContent);

        // Update order
        $order->update([
            'bill_generated' => true,
            'bill_path' => 'bills/' . $filename,
        ]);

        // Log activity
        ActivityLogService::log(
            'bill_generated',
            $order,
            "Bill generated for order {$order->order_number}"
        );

        // Log to new activity log system
        logActivity(
            'Bill Generated',
            'Order',
            $order->id,
            "Bill generated for order #{$order->order_number}"
        );

        return redirect()->route('admin.orders.view-bill', $order)
            ->with('success', 'Bill generated successfully.');
    }

    /**
     * View generated bill
     */
    public function viewBill(Order $order)
    {
        if (!$order->bill_generated) {
            return back()->with('error', 'No bill has been generated for this order.');
        }

        return view('admin.orders.view-bill', compact('order'));
    }

    /**
     * Download bill PDF
     */
    public function downloadBill(Order $order)
    {
        if (!$order->bill_generated || !$order->bill_path) {
            return back()->with('error', 'No bill has been generated for this order.');
        }

        $path = storage_path('app/public/' . $order->bill_path);
        
        if (!file_exists($path)) {
            return back()->with('error', 'Bill file not found.');
        }

        return response()->download($path, 'bill-' . $order->order_number . '.pdf');
    }
}
