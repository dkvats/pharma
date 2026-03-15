<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::orderBy('key')->get();
        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            
            if ($setting) {
                // Convert value based on type
                if ($setting->type === 'boolean') {
                    $value = $request->has($key) ? '1' : '0';
                }
                
                $setting->update(['value' => $value]);
            }
        }

        // Clear cache
        SystemSetting::clearCache();

        logActivity('System Settings Updated', null, 'Super Admin updated system settings');

        return back()->with('success', 'System settings updated successfully.');
    }

    public function updateSingle(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
            'type' => 'required|in:string,boolean,integer,json',
        ]);

        SystemSetting::setValue(
            $request->key,
            $request->value,
            $request->type
        );

        logActivity('System Setting Updated', null, "Super Admin updated setting: {$request->key}");

        return response()->json(['success' => true]);
    }
}
