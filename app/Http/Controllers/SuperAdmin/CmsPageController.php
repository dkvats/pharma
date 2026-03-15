<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::orderBy('title')->paginate(20);
        return view('super-admin.cms-pages.index', compact('pages'));
    }

    public function create()
    {
        return view('super-admin.cms-pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published,active,archived',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page = CmsPage::create($validated);
        CmsPage::clearCache($page->slug);

        logActivity('CMS Page Created', $page, "Created page: {$page->title}");

        return redirect()->route('super-admin.cms-pages.index')
            ->with('success', "Page '{$page->title}' created successfully.");
    }

    public function edit(CmsPage $cms_page)
    {
        $page = $cms_page;
        return view('super-admin.cms-pages.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $cms_page)
    {
        $page = $cms_page;
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published,active,archived',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $oldSlug = $page->slug;
        $page->update($validated);
        
        CmsPage::clearCache($oldSlug);
        CmsPage::clearCache($page->slug);

        logActivity('CMS Page Updated', $page, "Updated page: {$page->title}");

        return redirect()->route('super-admin.cms-pages.index')
            ->with('success', "Page '{$page->title}' updated successfully.");
    }

    public function destroy(CmsPage $cms_page)
    {
        $page = $cms_page;
        $title = $page->title;
        $slug = $page->slug;
        
        $page->delete();
        CmsPage::clearCache($slug);

        logActivity('CMS Page Deleted', null, "Deleted page: {$title}");

        return back()->with('success', "Page '{$title}' deleted successfully.");
    }

    /**
     * Toggle CMS page status between published and draft.
     */
    public function toggle(CmsPage $cms_page)
    {
        $page = $cms_page;
        
        // Toggle between 'published' and 'draft'
        $page->status = $page->status === 'published' ? 'draft' : 'published';
        $page->save();
        
        CmsPage::clearCache($page->slug);

        logActivity('CMS Page Toggled', $page, "Toggled page status: {$page->title}");

        return back()->with('success', "Page '{$page->title}' status updated to {$page->status}.");
    }
}
