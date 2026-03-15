<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
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

        $products = $query->with('creator')->latest()->paginate(10);
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
            'mrp' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'commission' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'requires_prescription' => ['boolean'],
            'is_special_spin_product' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'mrp' => $validated['mrp'],
            'price' => $validated['price'],
            'discount_amount' => $validated['discount_amount'],
            'commission' => $validated['commission'],
            'stock' => $validated['stock'],
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

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'commission' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_special_spin_product' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = $validated;
        $data['is_special_spin_product'] = $validated['is_special_spin_product'] ?? false;
        $oldImagePath = $product->image;
        $newImagePath = null;

        // Handle image upload - store new image first
        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $newImagePath;
        }

        try {
            DB::beginTransaction();
            
            // Handle special spin product - ensure only one active at a time
            if ($data['is_special_spin_product'] && !$product->is_special_spin_product) {
                Product::where('is_special_spin_product', true)->update(['is_special_spin_product' => false]);
            }
            
            // Update product in database
            $product->update($data);
            
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
}
