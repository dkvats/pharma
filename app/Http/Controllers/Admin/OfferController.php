<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Product;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        // Check if offers system is enabled
        if (!SystemSettingService::isOffersEnabled()) {
            return redirect()->route('admin.dashboard')
                ->with('warning', 'Offers system is currently disabled by Super Admin. You can still manage offers, but they won\'t be visible to users.');
        }

        $offers = Offer::with('products', 'creator')->latest()->paginate(15);
        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->get();
        return view('admin.offers.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_audience' => 'required|in:user,store',
            'description' => 'nullable|string',
            'offer_type' => 'required|in:daily,ongoing',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'is_active' => 'boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('offers', 'public');
        }

        $offer = Offer::create($validated);
        
        if ($request->has('products')) {
            $offer->products()->attach($request->products);
        }

        return redirect()->route('admin.offers.index')->with('success', 'Offer created successfully.');
    }

    public function edit(Offer $offer)
    {
        $products = Product::where('status', 'active')->get();
        return view('admin.offers.edit', compact('offer', 'products'));
    }

    public function update(Request $request, Offer $offer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_audience' => 'required|in:user,store',
            'description' => 'nullable|string',
            'offer_type' => 'required|in:daily,ongoing',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'is_active' => 'boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('offers', 'public');
        }

        $offer->update($validated);
        
        if ($request->has('products')) {
            $offer->products()->sync($request->products);
        }

        return redirect()->route('admin.offers.index')->with('success', 'Offer updated successfully.');
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();
        return redirect()->route('admin.offers.index')->with('success', 'Offer deleted successfully.');
    }

    public function toggle(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);
        return back()->with('success', 'Offer status updated.');
    }
}
