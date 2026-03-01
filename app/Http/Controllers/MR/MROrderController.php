<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\Order;
use App\Models\MR\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MROrderController extends Controller
{
    public function index()
    {
        $orders = Order::forMR(auth()->id())
            ->with('doctor')
            ->latest()
            ->paginate(15);

        return view('mr.orders.index', compact('orders'));
    }

    public function create()
    {
        $doctors = Doctor::forMR(auth()->id())->active()->get();
        $products = Product::where('status', 'active')->get();

        return view('mr.orders.create', compact('doctors', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:mr_doctors,id',
            'remarks' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                // Calculate total
                $totalAmount = 0;
                foreach ($validated['products'] as $product) {
                    $totalAmount += $product['quantity'] * $product['unit_price'];
                }

                // Create order
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'doctor_id' => $validated['doctor_id'],
                    'mr_id' => auth()->id(),
                    'total_amount' => $totalAmount,
                    'remarks' => $validated['remarks'],
                    'status' => 'pending',
                    'ordered_at' => now(),
                ]);

                // Create order items
                foreach ($validated['products'] as $product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['quantity'] * $product['unit_price'],
                    ]);
                }
            });

            return redirect()->route('mr.orders.index')
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $this->authorizeAccess($order);

        $order->load(['doctor', 'items.product']);

        return view('mr.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $this->authorizeAccess($order);

        // Only allow editing pending orders
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be edited.');
        }

        $doctors = Doctor::forMR(auth()->id())->active()->get();
        $products = Product::where('status', 'active')->get();

        return view('mr.orders.edit', compact('order', 'doctors', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorizeAccess($order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be edited.');
        }

        $validated = $request->validate([
            'doctor_id' => 'required|exists:mr_doctors,id',
            'remarks' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated, $order) {
                // Calculate total
                $totalAmount = 0;
                foreach ($validated['products'] as $product) {
                    $totalAmount += $product['quantity'] * $product['unit_price'];
                }

                // Update order
                $order->update([
                    'doctor_id' => $validated['doctor_id'],
                    'total_amount' => $totalAmount,
                    'remarks' => $validated['remarks'],
                ]);

                // Delete old items
                $order->items()->delete();

                // Create new items
                foreach ($validated['products'] as $product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['quantity'] * $product['unit_price'],
                    ]);
                }
            });

            return redirect()->route('mr.orders.index')
                ->with('success', 'Order updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order)
    {
        $this->authorizeAccess($order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('mr.orders.index')
            ->with('success', 'Order cancelled successfully.');
    }

    public function print(Order $order)
    {
        $this->authorizeAccess($order);

        $order->load(['doctor', 'items.product', 'mr']);

        return view('mr.orders.print', compact('order'));
    }

    private function authorizeAccess(Order $order)
    {
        if ($order->mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
