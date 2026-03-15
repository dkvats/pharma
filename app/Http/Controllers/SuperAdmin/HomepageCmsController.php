<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HomepageContent;
use App\Models\HomepageSection;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomepageCmsController extends Controller
{
    /**
     * Homepage CMS management dashboard.
     */
    public function index()
    {
        $sections = HomepageSection::with('contents')
            ->orderBy('sort_order')
            ->get();

        $settings = SiteSetting::instance();

        $featuredProductsCount = Product::where('featured_on_homepage', true)->count();
        $totalProducts = Product::where('status', 'active')->count();

        return view('super-admin.homepage-cms.index', compact(
            'sections', 'settings', 'featuredProductsCount', 'totalProducts'
        ));
    }

    /**
     * Edit a section's content fields.
     */
    public function editSection(HomepageSection $section)
    {
        $section->load('contents');
        $contentMap = $section->contentMap();

        return view('super-admin.homepage-cms.edit-section', compact('section', 'contentMap'));
    }

    /**
     * Save a section's content fields.
     */
    public function updateSection(Request $request, HomepageSection $section)
    {
        $fields = $request->input('fields', []);

        foreach ($fields as $fieldKey => $fieldValue) {
            HomepageContent::updateOrCreate(
                ['section_id' => $section->id, 'field_key' => $fieldKey],
                ['field_value' => $fieldValue]
            );
        }

        // Handle image uploads for this section
        foreach ($request->allFiles() as $fileKey => $file) {
            if (str_starts_with($fileKey, 'image_')) {
                $fieldKey = substr($fileKey, 6); // Remove 'image_' prefix
                $path = $file->store('homepage/' . $section->section_key, 'public');

                HomepageContent::updateOrCreate(
                    ['section_id' => $section->id, 'field_key' => $fieldKey],
                    ['field_value' => $path]
                );
            }
        }

        $this->clearHomepageCache();

        return back()->with('success', "Section '{$section->section_title}' updated successfully.");
    }

    /**
     * Toggle a section active/inactive.
     */
    public function toggleSection(HomepageSection $section)
    {
        $section->status = $section->status === 'active' ? 'inactive' : 'active';
        $section->save();

        $this->clearHomepageCache();

        return back()->with('success', "Section '{$section->section_title}' status updated to {$section->status}.");
    }

    /**
     * Update section sort order (drag & drop).
     */
    public function reorderSections(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:homepage_sections,id',
        ]);

        foreach ($request->order as $position => $sectionId) {
            HomepageSection::where('id', $sectionId)->update(['sort_order' => $position + 1]);
        }

        $this->clearHomepageCache();

        return response()->json(['success' => true]);
    }

    /**
     * Manage site settings (site name, tagline, logo, contact info, social links).
     */
    public function siteSettings()
    {
        $settings = SiteSetting::instance();
        return view('super-admin.homepage-cms.site-settings', compact('settings'));
    }

    /**
     * Save site settings.
     */
    public function updateSiteSettings(Request $request)
    {
        $request->validate([
            'site_name'        => 'required|string|max:100',
            'tagline'          => 'nullable|string|max:200',
            'contact_phone'    => 'nullable|string|max:30',
            'contact_email'    => 'nullable|email|max:100',
            'address'          => 'nullable|string|max:500',
            'facebook_url'     => 'nullable|url|max:255',
            'twitter_url'      => 'nullable|url|max:255',
            'linkedin_url'     => 'nullable|url|max:255',
            'instagram_url'    => 'nullable|url|max:255',
            'whatsapp_number'  => 'nullable|string|max:20',
            'logo'             => 'nullable|image|max:2048',
            'favicon'          => 'nullable|image|max:512',
            'hero_image'       => 'nullable|image|max:4096',
        ]);

        $settings = SiteSetting::instance();

        $data = $request->only([
            'site_name', 'tagline', 'contact_phone', 'contact_email',
            'address', 'facebook_url', 'twitter_url', 'linkedin_url',
            'instagram_url', 'whatsapp_number',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo) Storage::disk('public')->delete($settings->logo);
            $data['logo'] = $request->file('logo')->store('site', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($settings->favicon) Storage::disk('public')->delete($settings->favicon);
            $data['favicon'] = $request->file('favicon')->store('site', 'public');
        }

        // Handle hero image upload
        if ($request->hasFile('hero_image')) {
            if ($settings->hero_image) Storage::disk('public')->delete($settings->hero_image);
            $data['hero_image'] = $request->file('hero_image')->store('homepage/hero', 'public');

            // Also update hero section CMS content
            $heroSection = HomepageSection::where('section_key', 'hero')->first();
            if ($heroSection) {
                HomepageContent::updateOrCreate(
                    ['section_id' => $heroSection->id, 'field_key' => 'image'],
                    ['field_value' => $data['hero_image']]
                );
            }
        }

        $settings->update($data);

        $this->clearHomepageCache();

        return back()->with('success', 'Site settings updated successfully.');
    }

    /**
     * Manage featured products on homepage.
     */
    public function featuredProducts()
    {
        $featuredProducts = Product::where('featured_on_homepage', true)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $allProducts = Product::where('status', 'active')
            ->where('featured_on_homepage', false)
            ->orderBy('name')
            ->get();

        return view('super-admin.homepage-cms.featured-products', compact('featuredProducts', 'allProducts'));
    }

    /**
     * Toggle a product's featured status.
     */
    public function toggleFeaturedProduct(Product $product)
    {
        $product->featured_on_homepage = !$product->featured_on_homepage;
        $product->save();

        $this->clearHomepageCache();

        $status = $product->featured_on_homepage ? 'featured' : 'unfeatured';
        return back()->with('success', "Product '{$product->name}' is now {$status} on homepage.");
    }

    /**
     * Clear the homepage cache.
     */
    private function clearHomepageCache(): void
    {
        Cache::forget('homepage_content');
    }
}
