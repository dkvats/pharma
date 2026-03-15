<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageContent;
use App\Models\HomepageFeature;
use App\Models\HomepageNavItem;
use App\Models\HomepageSection;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomepageController extends Controller
{
    public function index()
    {
        $sections = HomepageSection::with('contents')->orderBy('sort_order')->get();
        $settings = SiteSetting::instance();
        return view('admin.homepage-manager.index', compact('sections', 'settings'));
    }

    public function edit(HomepageSection $section)
    {
        $section->load('contents');
        $content = $section->contentMap();
        return view('admin.homepage-manager.edit', compact('section', 'content'));
    }

    public function update(Request $request, HomepageSection $section)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $fieldKey => $fieldValue) {
            if ($request->hasFile($fieldKey)) continue;
            HomepageContent::updateOrCreate(
                ['section_id' => $section->id, 'field_key' => $fieldKey],
                ['field_value' => $fieldValue]
            );
        }

        foreach ($request->allFiles() as $fieldKey => $file) {
            $existing = HomepageContent::where('section_id', $section->id)
                ->where('field_key', $fieldKey)->first();
            if ($existing && $existing->field_value) {
                Storage::disk('public')->delete($existing->field_value);
            }
            $path = $file->store('site', 'public');
            HomepageContent::updateOrCreate(
                ['section_id' => $section->id, 'field_key' => $fieldKey],
                ['field_value' => $path]
            );
        }

        $this->clearHomepageCache();

        logActivity('Homepage Section Updated', 'HomepageSection', $section->id,
            "Admin updated '{$section->section_title}' section content");

        return redirect()->route('admin.homepage-manager.index')
            ->with('success', "'{$section->section_title}' section updated successfully.");
    }

    public function toggle(HomepageSection $section)
    {
        $section->status = $section->status === 'active' ? 'inactive' : 'active';
        $section->save();

        $this->clearHomepageCache();

        logActivity('Homepage Section Toggled', 'HomepageSection', $section->id,
            "Section '{$section->section_title}' set to {$section->status}");

        return back()->with('success', "'{$section->section_title}' is now {$section->status}.");
    }

    public function reorder(Request $request)
    {
        $items = $request->input('order', []);
        foreach ($items as $item) {
            HomepageSection::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        $this->clearHomepageCache();

        return response()->json(['success' => true]);
    }

    public function updateBranding(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:100',
            'logo'      => 'nullable|image|max:2048',
            'favicon'   => 'nullable|image|max:512',
        ]);

        $settings = SiteSetting::instance();
        $settings->site_name = $request->site_name;

        if ($request->hasFile('logo')) {
            if ($settings->logo) Storage::disk('public')->delete($settings->logo);
            $settings->logo = $request->file('logo')->store('site', 'public');
        }

        if ($request->hasFile('favicon')) {
            if (!empty($settings->favicon)) Storage::disk('public')->delete($settings->favicon);
            $settings->favicon = $request->file('favicon')->store('site', 'public');
        }

        $settings->save();

        $this->clearHomepageCache();

        logActivity('Branding Settings Updated', 'SiteSetting', $settings->id, 'Admin updated branding settings');

        return back()->with('success', 'Branding updated successfully.');
    }

    /**
     * Preview the homepage as it would appear to visitors.
     */
    public function preview()
    {
        $settings = SiteSetting::instance();

        $sections = HomepageSection::with('contents')
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get()
            ->keyBy('section_key');

        $content = [];
        foreach ($sections as $key => $section) {
            $content[$key] = $section->contentMap();
        }

        $features = HomepageFeature::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        $navItems = HomepageNavItem::where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        return view('homepage.index', compact('settings', 'sections', 'content', 'features', 'navItems'));
    }

    /**
     * Clear the homepage cache.
     */
    private function clearHomepageCache()
    {
        Cache::forget('homepage_content');
    }
}