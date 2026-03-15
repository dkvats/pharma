<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomepageFeatureController extends Controller
{
    /**
     * List all features.
     */
    public function index()
    {
        $features = HomepageFeature::orderBy('sort_order')->get();
        return view('admin.homepage-features.index', compact('features'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.homepage-features.create');
    }

    /**
     * Store a new feature.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon'        => 'required|string|max:50',
            'icon_color'  => 'required|string|max:20',
            'status'      => 'required|in:active,inactive',
        ]);

        // Set sort_order to max + 1
        $maxOrder = HomepageFeature::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        $feature = HomepageFeature::create($validated);

        $this->clearHomepageCache();

        logActivity('Feature Created', 'HomepageFeature', $feature->id,
            "Admin created feature: {$feature->title}");

        return redirect()->route('admin.homepage-features.index')
            ->with('success', "Feature '{$feature->title}' created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit(HomepageFeature $homepage_feature)
    {
        $feature = $homepage_feature;
        return view('admin.homepage-features.edit', compact('feature'));
    }

    /**
     * Update a feature.
     */
    public function update(Request $request, HomepageFeature $homepage_feature)
    {
        $feature = $homepage_feature;

        $validated = $request->validate([
            'title'       => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'icon'        => 'required|string|max:50',
            'icon_color'  => 'required|string|max:20',
            'status'      => 'required|in:active,inactive',
        ]);

        $feature->update($validated);

        $this->clearHomepageCache();

        logActivity('Feature Updated', 'HomepageFeature', $feature->id,
            "Admin updated feature: {$feature->title}");

        return redirect()->route('admin.homepage-features.index')
            ->with('success', "Feature '{$feature->title}' updated successfully.");
    }

    /**
     * Delete a feature.
     */
    public function destroy(HomepageFeature $homepage_feature)
    {
        $feature = $homepage_feature;
        $title = $feature->title;

        logActivity('Feature Deleted', 'HomepageFeature', $feature->id,
            "Admin deleted feature: {$title}");

        $feature->delete();

        $this->clearHomepageCache();

        return redirect()->route('admin.homepage-features.index')
            ->with('success', "Feature '{$title}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggle(HomepageFeature $homepage_feature)
    {
        $feature = $homepage_feature;
        $feature->status = $feature->status === 'active' ? 'inactive' : 'active';
        $feature->save();

        $this->clearHomepageCache();

        logActivity('Feature Toggled', 'HomepageFeature', $feature->id,
            "Feature '{$feature->title}' set to {$feature->status}");

        return back()->with('success',
            "Feature '{$feature->title}' is now {$feature->status}.");
    }

    /**
     * Update sort order via AJAX.
     */
    public function reorder(Request $request)
    {
        $items = $request->input('order', []);
        foreach ($items as $item) {
            HomepageFeature::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        $this->clearHomepageCache();

        return response()->json(['success' => true]);
    }

    /**
     * Clear the homepage cache.
     */
    private function clearHomepageCache()
    {
        Cache::forget('homepage_content');
    }
}
