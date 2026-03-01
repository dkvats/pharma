<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show spin settings page
     */
    public function spinSettings()
    {
        $products = Product::where('status', 'active')->get();
        $currentProductId = Setting::get('spin_target_product_id');
        
        return view('admin.settings.spin', compact('products', 'currentProductId'));
    }

    /**
     * Update spin settings
     */
    public function updateSpinSettings(Request $request)
    {
        $validated = $request->validate([
            'spin_target_product_id' => 'nullable|exists:products,id',
        ]);

        $product = null;
        if ($validated['spin_target_product_id']) {
            Setting::set('spin_target_product_id', $validated['spin_target_product_id'], 'integer', 'spin', 'Spin Target Product');
            $product = Product::find($validated['spin_target_product_id']);
        } else {
            // Remove the setting if null
            Setting::where('key', 'spin_target_product_id')->delete();
        }

        // Log settings update
        logActivity(
            'Settings Updated',
            'Setting',
            null,
            $product ? "Spin target product set to: {$product->name}" : 'Spin target product removed'
        );

        return back()->with('success', 'Spin settings updated successfully.');
    }
}
