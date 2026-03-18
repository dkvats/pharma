<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{
    /**
     * Display inventory reports dashboard
     */
    public function index(Request $request)
    {
        return view('admin.inventory-reports.index');
    }

    /**
     * Report: Expiring Products
     */
    public function expiringProducts(Request $request)
    {
        $days = $request->get('days', 30);
        
        $batches = ProductBatch::with('product')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->orderBy('expiry_date')
            ->paginate(20);

        return view('admin.inventory-reports.expiring', compact('batches', 'days'));
    }

    /**
     * Report: Expired Products
     */
    public function expiredProducts(Request $request)
    {
        $batches = ProductBatch::with('product')
            ->whereDate('expiry_date', '<', now())
            ->orderBy('expiry_date', 'desc')
            ->paginate(20);

        return view('admin.inventory-reports.expired', compact('batches'));
    }

    /**
     * Report: Low Stock Products
     */
    public function lowStockProducts(Request $request)
    {
        $products = Product::whereColumn('stock', '<=', 'low_stock_alert')
            ->orWhere('stock', '<=', 10)
            ->orderBy('stock')
            ->paginate(20);

        return view('admin.inventory-reports.low-stock', compact('products'));
    }

    /**
     * Report: Batch Inventory
     */
    public function batchInventory(Request $request)
    {
        $productId = $request->get('product_id');
        
        $query = ProductBatch::with('product');
        
        if ($productId) {
            $query->where('product_id', $productId);
        }
        
        $batches = $query->orderBy('expiry_date')->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('admin.inventory-reports.batch-inventory', compact('batches', 'products', 'productId'));
    }

    /**
     * Report: Stock Valuation
     */
    public function stockValuation(Request $request)
    {
        $products = Product::with(['batches' => function ($query) {
            $query->whereDate('expiry_date', '>=', now());
        }])->get();

        $totalValue = 0;
        $productValues = [];

        foreach ($products as $product) {
            $productValue = 0;
            foreach ($product->batches as $batch) {
                $productValue += $batch->quantity * $batch->mrp;
            }
            $productValues[$product->id] = $productValue;
            $totalValue += $productValue;
        }

        return view('admin.inventory-reports.valuation', compact('products', 'productValues', 'totalValue'));
    }

    /**
     * Get dashboard widget data
     */
    public static function getDashboardStats(): array
    {
        return [
            'expiring_soon' => ProductBatch::expiringSoon(30)->count(),
            'expired' => ProductBatch::expired()->count(),
            'low_stock' => Product::whereColumn('stock', '<=', 'low_stock_alert')->count(),
            'total_value' => ProductBatch::whereDate('expiry_date', '>=', now())
                ->sum(DB::raw('quantity * mrp')),
        ];
    }
}
