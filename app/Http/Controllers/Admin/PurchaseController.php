<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display list of purchase orders
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        $purchaseOrders = $query->latest()->paginate(15)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.purchases.index', compact('purchaseOrders', 'suppliers'));
    }

    /**
     * Show form to create purchase order
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Store new purchase order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'order_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.mrp' => ['required', 'numeric', 'min:0'],
            'items.*.batch_number' => ['nullable', 'string', 'max:100'],
            'items.*.expiry_date' => ['nullable', 'date', 'after:today'],
        ]);

        DB::transaction(function () use ($validated) {
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'order_number' => PurchaseOrder::generateOrderNumber(),
                'order_date' => $validated['order_date'],
                'status' => 'ordered',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'mrp' => $item['mrp'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'received_quantity' => 0,
                ]);
            }
        });

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase order created successfully.');
    }

    /**
     * Show purchase order details
     */
    public function show(PurchaseOrder $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'grns.receiver']);
        return view('admin.purchases.show', compact('purchase'));
    }

    /**
     * Show form to receive goods (create GRN)
     */
    public function receiveForm(PurchaseOrder $purchase)
    {
        // Only allow receiving if order is not fully received
        if ($purchase->status === 'fully_received') {
            return redirect()->route('admin.purchases.show', $purchase)
                ->with('error', 'This purchase order has been fully received.');
        }

        if ($purchase->status === 'cancelled') {
            return redirect()->route('admin.purchases.show', $purchase)
                ->with('error', 'This purchase order has been cancelled.');
        }

        $purchase->load(['supplier', 'items.product']);

        return view('admin.purchases.receive', compact('purchase'));
    }

    /**
     * Process goods receipt (create GRN and batches)
     */
    public function receive(Request $request, PurchaseOrder $purchase)
    {
        $validated = $request->validate([
            'received_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.purchase_order_item_id' => ['required', 'exists:purchase_order_items,id'],
            'items.*.received_quantity' => ['required', 'integer', 'min:0'],
        ]);

        // Validate that received quantities don't exceed remaining
        foreach ($validated['items'] as $item) {
            $poItem = PurchaseOrderItem::find($item['purchase_order_item_id']);
            if ($item['received_quantity'] > $poItem->remaining_quantity) {
                return back()->withErrors([
                    'items' => "Received quantity exceeds remaining quantity for {$poItem->product->name}"
                ])->withInput();
            }
        }

        // Filter out items with 0 received quantity
        $itemsToReceive = array_filter($validated['items'], function ($item) {
            return $item['received_quantity'] > 0;
        });

        if (empty($itemsToReceive)) {
            return back()->withErrors(['items' => 'Please enter at least one item to receive.']);
        }

        DB::transaction(function () use ($purchase, $validated, $itemsToReceive) {
            // Create GRN
            $grn = GoodsReceivedNote::create([
                'purchase_order_id' => $purchase->id,
                'grn_number' => GoodsReceivedNote::generateGrnNumber(),
                'received_by' => auth()->id(),
                'received_date' => $validated['received_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Process receipt - creates batches and logs
            $grn->processReceipt($itemsToReceive);
        });

        return redirect()->route('admin.purchases.show', $purchase)
            ->with('success', 'Goods received successfully. Batches have been created automatically.');
    }

    /**
     * Cancel purchase order
     */
    public function cancel(PurchaseOrder $purchase)
    {
        if ($purchase->status === 'fully_received') {
            return back()->with('error', 'Cannot cancel a fully received order.');
        }

        $purchase->status = 'cancelled';
        $purchase->save();

        return back()->with('success', 'Purchase order cancelled successfully.');
    }
}
