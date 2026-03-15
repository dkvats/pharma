<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::orderBy('name')->paginate(20);
        $types = ['email', 'sms', 'push', 'in_app'];
        
        return view('super-admin.notifications.index', compact('templates', 'types'));
    }

    public function edit(NotificationTemplate $notification_template)
    {
        $template = $notification_template;
        return view('super-admin.notifications.edit', compact('template'));
    }

    public function update(Request $request, NotificationTemplate $notification_template)
    {
        $template = $notification_template;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:email,sms,push,in_app',
            'status' => 'required|in:active,inactive',
            'variables' => 'nullable|array',
        ]);

        $template->update($validated);
        NotificationTemplate::clearCache($template->template_key);

        logActivity('Notification Template Updated', $template, "Updated template: {$template->name}");

        return redirect()->route('super-admin.notifications.index')
            ->with('success', "Template '{$template->name}' updated successfully.");
    }

    public function toggle(NotificationTemplate $notification_template)
    {
        $template = $notification_template;
        $template->status = $template->status === 'active' ? 'inactive' : 'active';
        $template->save();
        
        NotificationTemplate::clearCache($template->template_key);

        logActivity('Notification Template Toggled', $template, "Template '{$template->name}' set to {$template->status}");

        return back()->with('success', "Template '{$template->name}' is now {$template->status}.");
    }

    public function preview(Request $request, NotificationTemplate $notification_template)
    {
        $template = $notification_template;
        $variables = $request->input('variables', []);
        
        $rendered = $template->render($variables);

        return response()->json($rendered);
    }
}
