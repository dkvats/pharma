<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomepageSlideController extends Controller
{
    /**
     * List all slides.
     */
    public function index()
    {
        $slides = HomepageSlide::orderBy('sort_order')->get();
        return view('admin.homepage-slides.index', compact('slides'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.homepage-slides.create');
    }

    /**
     * Store a new slide.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:150',
            'subtitle'    => 'nullable|string|max:500',
            'image'       => 'required|image|max:4096',
            'button_text' => 'nullable|string|max:80',
            'button_link' => 'nullable|string|max:255',
            'status'      => 'required|in:active,inactive',
        ]);

        $path = $request->file('image')->store('homepage/slides', 'public');

        $maxOrder = HomepageSlide::max('sort_order') ?? 0;

        $slide = HomepageSlide::create([
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'image'       => $path,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'sort_order'  => $maxOrder + 1,
            'status'      => $request->status,
        ]);

        $this->clearCache();

        logActivity('Slide Created', 'HomepageSlide', $slide->id,
            'Admin created homepage slide: ' . ($slide->title ?? 'Untitled'));

        return redirect()->route('admin.homepage-slides.index')
            ->with('success', 'Slide created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(HomepageSlide $homepage_slide)
    {
        return view('admin.homepage-slides.edit', ['slide' => $homepage_slide]);
    }

    /**
     * Update a slide.
     */
    public function update(Request $request, HomepageSlide $homepage_slide)
    {
        $slide = $homepage_slide;

        $request->validate([
            'title'       => 'nullable|string|max:150',
            'subtitle'    => 'nullable|string|max:500',
            'image'       => 'nullable|image|max:4096',
            'button_text' => 'nullable|string|max:80',
            'button_link' => 'nullable|string|max:255',
            'status'      => 'required|in:active,inactive',
        ]);

        $data = [
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'button_text' => $request->button_text,
            'button_link' => $request->button_link,
            'status'      => $request->status,
        ];

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }
            $data['image'] = $request->file('image')->store('homepage/slides', 'public');
        }

        $slide->update($data);

        $this->clearCache();

        logActivity('Slide Updated', 'HomepageSlide', $slide->id,
            'Admin updated homepage slide: ' . ($slide->title ?? 'Untitled'));

        return redirect()->route('admin.homepage-slides.index')
            ->with('success', 'Slide updated successfully.');
    }

    /**
     * Delete a slide.
     */
    public function destroy(HomepageSlide $homepage_slide)
    {
        $slide = $homepage_slide;

        // Delete image from storage
        if ($slide->image) {
            Storage::disk('public')->delete($slide->image);
        }

        logActivity('Slide Deleted', 'HomepageSlide', $slide->id,
            'Admin deleted homepage slide: ' . ($slide->title ?? 'Untitled'));

        $slide->delete();

        $this->clearCache();

        return redirect()->route('admin.homepage-slides.index')
            ->with('success', 'Slide deleted successfully.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggle(HomepageSlide $homepage_slide)
    {
        $slide = $homepage_slide;
        $slide->status = $slide->status === 'active' ? 'inactive' : 'active';
        $slide->save();

        $this->clearCache();

        logActivity('Slide Toggled', 'HomepageSlide', $slide->id,
            "Slide '{$slide->title}' set to {$slide->status}");

        return back()->with('success',
            "Slide '" . ($slide->title ?? 'Untitled') . "' is now {$slide->status}.");
    }

    /**
     * Update sort order via AJAX (drag & drop).
     */
    public function reorder(Request $request)
    {
        $items = $request->input('order', []);
        foreach ($items as $item) {
            HomepageSlide::where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        $this->clearCache();

        return response()->json(['success' => true]);
    }

    /**
     * Clear homepage cache so slider changes appear immediately.
     */
    private function clearCache(): void
    {
        Cache::forget('homepage_content');
        Cache::forget('homepage_slides');
    }
}
