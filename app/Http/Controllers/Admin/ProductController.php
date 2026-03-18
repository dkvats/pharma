<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductImage;
use App\Models\ProductLog;
use App\Models\StockLedger;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->with(['creator', 'batches'])->latest()->paginate(10);
        $categories = Product::distinct()->pluck('category');

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products'],
            'unit_type' => ['nullable', 'string', 'max:50'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'gst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'commission' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'batch_number' => ['nullable', 'string', 'max:255'],
            'manufacture_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'low_stock_alert' => ['nullable', 'integer', 'min:0'],
            'requires_prescription' => ['boolean'],
            'is_special_spin_product' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'brand' => $validated['brand'],
            'company' => $validated['company'],
            'sku' => $validated['sku'],
            'unit_type' => $validated['unit_type'],
            'mrp' => $validated['mrp'],
            'price' => $validated['price'],
            'discount_amount' => $validated['discount_amount'],
            'gst_percent' => $validated['gst_percent'] ?? 0,
            'commission' => $validated['commission'],
            'stock' => $validated['stock'],
            'batch_number' => $validated['batch_number'],
            'expiry_date' => $validated['expiry_date'],
            'low_stock_alert' => $validated['low_stock_alert'] ?? 10,
            'requires_prescription' => $validated['requires_prescription'] ?? false,
            'is_special_spin_product' => $validated['is_special_spin_product'] ?? false,
            'status' => $validated['status'],
            'description' => $validated['description'],
            'created_by' => auth()->id(),
        ];

        // Handle special spin product - ensure only one active at a time
        if ($data['is_special_spin_product']) {
            Product::where('is_special_spin_product', true)->update(['is_special_spin_product' => false]);
        }

        // Handle main image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $path = $img->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                ]);
            }
        }

        // Auto-create a batch if batch_number provided at product creation
        if (!empty($validated['batch_number']) && !empty($validated['expiry_date'])) {
            ProductBatch::create([
                'product_id'      => $product->id,
                'batch_number'    => $validated['batch_number'],
                'manufacture_date' => $validated['manufacture_date'] ?? null,
                'expiry_date'     => $validated['expiry_date'],
                'quantity'        => $validated['stock'],
                'mrp'             => $validated['mrp'],
            ]);
            // syncStockFromBatches() is auto-triggered by ProductBatch::booted()
        }

        // Audit log
        ProductLog::logCreated($product, auth()->id());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load(['batches.storeInventories.store', 'batches.supplier']);
        $stores = \App\Models\User::role('Store')->orderBy('name')->get(['id', 'name']);
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        return view('admin.products.edit', compact('product', 'stores', 'suppliers'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'unit_type' => ['nullable', 'string', 'max:50'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'gst_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'commission' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'batch_number' => ['nullable', 'string', 'max:255'],
            'expiry_date' => ['nullable', 'date'],
            'low_stock_alert' => ['nullable', 'integer', 'min:0'],
            'is_special_spin_product' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'brand' => $validated['brand'],
            'company' => $validated['company'],
            'sku' => $validated['sku'],
            'unit_type' => $validated['unit_type'],
            'mrp' => $validated['mrp'],
            'price' => $validated['price'],
            'discount_amount' => $validated['discount_amount'],
            'gst_percent' => $validated['gst_percent'] ?? 0,
            'commission' => $validated['commission'],
            'stock' => $validated['stock'],
            'batch_number' => $validated['batch_number'],
            'expiry_date' => $validated['expiry_date'],
            'low_stock_alert' => $validated['low_stock_alert'] ?? 10,
            'is_special_spin_product' => $validated['is_special_spin_product'] ?? false,
            'status' => $validated['status'],
            'description' => $validated['description'],
        ];

        $oldImagePath = $product->image;
        $newImagePath = null;

        // Handle main image upload - store new image first
        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $newImagePath;
        }

        try {
            DB::beginTransaction();

            // Capture old values for audit log
            $oldValues = $product->only(['price', 'stock', 'name', 'status']);
            $oldPrice  = (float) $product->price;
            $oldStock  = (int) $product->stock;
            
            // Handle special spin product - ensure only one active at a time
            if ($data['is_special_spin_product'] && !$product->is_special_spin_product) {
                Product::where('is_special_spin_product', true)->update(['is_special_spin_product' => false]);
            }
            
            // Update product in database
            $product->update($data);
            
            // Handle gallery images
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $img) {
                    $path = $img->store('products/gallery', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $path,
                    ]);
                }
            }

            // Audit: log price change separately if price changed
            if ((float) $data['price'] !== $oldPrice) {
                ProductLog::logPriceChanged($product, $oldPrice, (float) $data['price'], auth()->id());
            }

            // Audit: log stock change separately if stock changed
            if ((int) $data['stock'] !== $oldStock) {
                ProductLog::logStockUpdated($product, $oldStock, (int) $data['stock'], auth()->id());
            }

            // Audit: general update log
            ProductLog::logUpdated($product, $oldValues, auth()->id());
            
            DB::commit();
            
            // Only delete old image after successful database update
            if ($newImagePath && $oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete new image if database update failed
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus(Product $product)
    {
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        return back()->with('success', 'Product status updated to ' . $product->status);
    }

    /**
     * Store a new batch for the given product.
     */
    public function storeBatch(Request $request, Product $product)
    {
        $validated = $request->validate([
            'batch_number'    => ['required', 'string', 'max:100'],
            'manufacture_date' => ['nullable', 'date'],
            'expiry_date'     => ['required', 'date', 'after:today'],
            'quantity'        => ['required', 'integer', 'min:1'],
            'mrp'             => ['required', 'numeric', 'min:0'],
            'supplier_id'     => ['nullable', 'exists:suppliers,id'],
        ]);

        $batch = $product->batches()->create($validated);

        // Log to stock ledger (for audit only)
        StockLedger::logBatchCreated($batch, "Batch created via product edit");

        ProductLog::create([
            'product_id' => $product->id,
            'action'     => 'batch_added',
            'old_value'  => null,
            'new_value'  => json_encode([
                'batch_number' => $batch->batch_number,
                'quantity'     => $batch->quantity,
                'expiry_date'  => $batch->expiry_date->toDateString(),
                'supplier_id'  => $batch->supplier_id,
            ]),
            'changed_by' => auth()->id(),
        ]);

        return back()->with('success', 'Batch "' . $batch->batch_number . '" added successfully.');
    }

    /**
     * Delete a batch from the given product.
     */
    public function destroyBatch(Product $product, ProductBatch $batch)
    {
        abort_if($batch->product_id !== $product->id, 403);

        $batchNumber = $batch->batch_number;
        $batch->delete();

        return back()->with('success', 'Batch "' . $batchNumber . '" removed successfully.');
    }
}
