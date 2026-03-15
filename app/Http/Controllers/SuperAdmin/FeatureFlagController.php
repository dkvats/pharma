<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function index()
    {
        $flags = FeatureFlag::orderBy('name')->get();
        return view('super-admin.feature-flags.index', compact('flags'));
    }

    public function toggle(FeatureFlag $feature_flag)
    {
        $flag = $feature_flag;
        
        if ($flag->enabled) {
            $flag->disable();
            $status = 'disabled';
        } else {
            $flag->enable();
            $status = 'enabled';
        }

        logActivity('Feature Flag Toggled', $flag, "Feature '{$flag->name}' {$status}");

        return back()->with('success', "Feature '{$flag->name}' is now {$status}.");
    }

    public function update(Request $request, FeatureFlag $feature_flag)
    {
        $flag = $feature_flag;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rollout_percentage' => 'nullable|integer|min:0|max:100',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'string',
        ]);

        $flag->update($validated);
        FeatureFlag::clearCache();

        logActivity('Feature Flag Updated', $flag, "Feature '{$flag->name}' updated");

        return back()->with('success', "Feature '{$flag->name}' updated successfully.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'flag_key' => 'required|string|max:255|unique:feature_flags,flag_key',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $flag = FeatureFlag::create($validated);
        FeatureFlag::clearCache();

        logActivity('Feature Flag Created', $flag, "Created feature: {$flag->name}");

        return back()->with('success', "Feature '{$flag->name}' created successfully.");
    }
}
