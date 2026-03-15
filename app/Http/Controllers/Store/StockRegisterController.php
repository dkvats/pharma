<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class StockRegisterController extends Controller
{
    /**
     * Display the stock register with filters
     */
    public function index(Request $request)
    {
        $storeId = auth()->id();

        // Build query with filters
        $query = StockTransaction::forStore($storeId)
            ->with(['product'])
            ->latest();

        // Filter by product
        if ($request->filled('product_id')) {
            $query->forProduct($request->product_id);
        }

        // Filter by date range
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        // Filter by transaction type
        if ($request->filled('transaction_type')) {
            $query->ofType($request->transaction_type);
        }

        $transactions = $query->paginate(20)->withQueryString();

        // Get products for filter dropdown (only products this store has transacted)
        $productIds = StockTransaction::forStore($storeId)
            ->distinct()
            ->pluck('product_id');
        
        $products = Product::whereIn('id', $productIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Calculate summary statistics
        $summary = [
            'total_purchases' => StockTransaction::forStore($storeId)->ofType('purchase')->sum('quantity'),
            'total_sales' => StockTransaction::forStore($storeId)->ofType('sale')->sum('quantity'),
            'total_adjustments' => StockTransaction::forStore($storeId)->ofType('adjustment')->sum('quantity'),
        ];

        return view('store.stock-register', compact(
            'transactions',
            'products',
            'summary'
        ));
    }
}
