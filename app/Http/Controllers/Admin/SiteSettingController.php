<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    /**
     * Show the homepage settings editor.
     */
    public function edit()
    {
        $settings = SiteSetting::instance();

        return view('admin.site-settings.edit', compact('settings'));
    }

    /**
     * Update homepage settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name'         => 'required|string|max:255',
            'hero_title'        => 'required|string|max:255',
            'hero_subtitle'     => 'nullable|string|max:1000',
            'about_title'       => 'nullable|string|max:255',
            'about_description' => 'nullable|string|max:5000',
            'contact_phone'     => 'nullable|string|max:50',
            'contact_email'     => 'nullable|email|max:255',
            'address'           => 'nullable|string|max:1000',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'hero_image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $settings = SiteSetting::instance();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }
            $validated['logo'] = $request->file('logo')->store('site', 'public');
        }

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) {
                Storage::disk('public')->delete($settings->hero_image);
            }
            $validated['hero_image'] = $request->file('hero_image')->store('site', 'public');
        }

        // Remove file fields from validated if not uploaded (don't overwrite with null)
        if (!$request->hasFile('logo')) {
            unset($validated['logo']);
        }
        if (!$request->hasFile('hero_image')) {
            unset($validated['hero_image']);
        }

        $settings->update($validated);

        logActivity('Homepage Settings Updated', 'SiteSetting', $settings->id, 'Admin updated homepage settings');

        return back()->with('success', 'Homepage settings updated successfully.');
    }
}
