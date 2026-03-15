<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DashboardWidget;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    public function index()
    {
        $widgets = DashboardWidget::orderBy('role')->orderBy('sort_order')->get();
        $roles = ['Admin', 'Doctor', 'Store', 'MR', 'End User'];
        
        return view('super-admin.widgets.index', compact('widgets', 'roles'));
    }

    public function toggle(DashboardWidget $widget)
    {
        $widget->status = $widget->status === 'active' ? 'inactive' : 'active';
        $widget->save();
        
        DashboardWidget::clearCache();

        logActivity('Widget Toggled', $widget, "Widget '{$widget->widget_name}' set to {$widget->status}");

        return back()->with('success', "Widget '{$widget->widget_name}' is now {$widget->status}.");
    }

    public function update(Request $request, DashboardWidget $widget)
    {
        $validated = $request->validate([
            'widget_name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'config' => 'nullable|array',
        ]);

        $widget->update($validated);
        DashboardWidget::clearCache();

        logActivity('Widget Updated', $widget, "Widget '{$widget->widget_name}' updated");

        return back()->with('success', "Widget '{$widget->widget_name}' updated successfully.");
    }

    public function reorder(Request $request)
    {
        $items = $request->input('order', []);
        
        foreach ($items as $item) {
            DashboardWidget::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        DashboardWidget::clearCache();

        return response()->json(['success' => true]);
    }

    public function byRole($role)
    {
        $widgets = DashboardWidget::where('role', $role)
            ->orderBy('sort_order')
            ->get();

        return response()->json($widgets);
    }
}
