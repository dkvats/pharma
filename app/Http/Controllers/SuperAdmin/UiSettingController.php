<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\UiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UiSettingController extends Controller
{
    public function index()
    {
        $settings = UiSetting::orderBy('key')->get();
        return view('super-admin.ui-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            $setting = UiSetting::where('key', $key)->first();
            
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        // Handle file uploads
        foreach ($request->allFiles() as $key => $file) {
            $setting = UiSetting::where('key', $key)->first();
            
            if ($setting) {
                // Delete old file
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                
                $path = $file->store('ui-settings', 'public');
                $setting->update(['value' => $path]);
            }
        }

        UiSetting::clearCache();

        logActivity('UI Settings Updated', null, 'Super Admin updated UI settings');

        return back()->with('success', 'UI settings updated successfully.');
    }

    public function updateSingle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        UiSetting::setValue($request->key, $request->value);

        logActivity('UI Setting Updated', null, "Updated UI setting: {$request->key}");

        return response()->json(['success' => true]);
    }
}
