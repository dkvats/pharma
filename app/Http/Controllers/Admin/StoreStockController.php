<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StoreStock;
use App\Models\User;
use App\Services\StoreStockService;
use Illuminate\Http\Request;

class StoreStockController extends Controller
{
    protected $storeStockService;

    public function __construct(StoreStockService $storeStockService)
    {
        $this->storeStockService = $storeStockService;
    }

    /**
     * Display all stores stock for monitoring
     */
    public function index(Request $request)
    {
        $storeId = $request->input('store_id');
        $productId = $request->input('product_id');

        $stocks = $this->storeStockService->getAllStoresStock(
            $storeId ?: null,
            $productId ?: null
        );

        $stores = User::role('Store')->where('status', 'active')->get();
        $products = Product::where('status', 'active')->get();

        return view('admin.store-stock.index', compact('stocks', 'stores', 'products'));
    }

    /**
     * Show specific store stock details
     */
    public function show(User $store)
    {
        $stocks = StoreStock::with('product')
            ->where('store_id', $store->id)
            ->get();

        return view('admin.store-stock.show', compact('store', 'stocks'));
    }
}
