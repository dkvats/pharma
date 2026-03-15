<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageNavItem;
use Illuminate\Http\Request;

class HomepageNavController extends Controller
{
    public function index()
    {
        $navItems = HomepageNavItem::orderBy('sort_order')->get();
        return view('admin.homepage-nav.index', compact('navItems'));
    }

    public function create()
    {
        return view('admin.homepage-nav.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'       => 'required|string|max:100',
            'url'         => 'required|string|max:255',
            'is_external' => 'boolean',
            'status'      => 'required|in:active,inactive',
        ]);

        $maxOrder = HomepageNavItem::max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;
        $validated['is_external'] = $request->boolean('is_external');

        $item = HomepageNavItem::create($validated);

        $this->clearHomepageCache();

        logActivity('Nav Item Created', 'HomepageNavItem', $item->id,
            "Admin created nav item: {$item->label}");

        return redirect()->route('admin.homepage-nav.index')
            ->with('success', "Navigation item '{$item->label}' created successfully.");
    }

    public function edit(HomepageNavItem $homepage_nav)
    {
        $navItem = $homepage_nav;
        return view('admin.homepage-nav.edit', compact('navItem'));
    }

    public function update(Request $request, HomepageNavItem $homepage_nav)
    {
        $navItem = $homepage_nav;

        $validated = $request->validate([
            'label'       => 'required|string|max:100',
            'url'         => 'required|string|max:255',
            'is_external' => 'boolean',
            'status'      => 'required|in:active,inactive',
        ]);

        $validated['is_external'] = $request->boolean('is_external');
        $navItem->update($validated);

        $this->clearHomepageCache();

        logActivity('Nav Item Updated', 'HomepageNavItem', $navItem->id,
            "Admin updated nav item: {$navItem->label}");

        return redirect()->route('admin.homepage-nav.index')
            ->with('success', "Navigation item '{$navItem->label}' updated successfully.");
    }

    public function destroy(HomepageNavItem $homepage_nav)
    {
        $navItem = $homepage_nav;
        $label = $navItem->label;

        $navItem->delete();

        $this->clearHomepageCache();

        logActivity('Nav Item Deleted', 'HomepageNavItem', $navItem->id,
            "Admin deleted nav item: {$label}");

        return redirect()->route('admin.homepage-nav.index')
            ->with('success', "Navigation item '{$label}' deleted successfully.");
    }

    public function toggle(HomepageNavItem $homepage_nav)
    {
        $navItem = $homepage_nav;
        $navItem->status = $navItem->status === 'active' ? 'inactive' : 'active';
        $navItem->save();

        $this->clearHomepageCache();

        logActivity('Nav Item Toggled', 'HomepageNavItem', $navItem->id,
            "Nav item '{$navItem->label}' set to {$navItem->status}");

        return back()->with('success', "'{$navItem->label}' is now {$navItem->status}.");
    }

    public function reorder(Request $request)
    {
        $items = $request->input('order', []);
        foreach ($items as $item) {
            HomepageNavItem::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        $this->clearHomepageCache();

        return response()->json(['success' => true]);
    }

    private function clearHomepageCache()
    {
        \Cache::forget('homepage_content');
    }
}
