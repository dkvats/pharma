<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::orderBy('sort_order')->get();
        return view('super-admin.modules.index', compact('modules'));
    }

    public function toggle(Module $module)
    {
        if ($module->status === 'active') {
            $module->disable();
            $status = 'inactive';
        } else {
            $module->enable();
            $status = 'active';
        }

        logActivity('Module Toggled', $module, "Module '{$module->module_name}' set to {$status}");

        return back()->with('success', "Module '{$module->module_name}' is now {$status}.");
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'module_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $module->update($validated);
        Module::clearCache();

        logActivity('Module Updated', $module, "Module '{$module->module_name}' updated");

        return back()->with('success', "Module '{$module->module_name}' updated successfully.");
    }

    public function reorder(Request $request)
    {
        $items = $request->input('order', []);
        
        foreach ($items as $item) {
            Module::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        Module::clearCache();

        return response()->json(['success' => true]);
    }
}
