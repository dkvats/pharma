<?php

namespace App\Http\Controllers;

use App\Models\HomepageFeature;
use App\Models\HomepageNavItem;
use App\Models\HomepageSection;
use App\Models\HomepageSlide;
use App\Models\MR\State;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display the public veterinary pharma homepage.
     * Loads active sections ordered by sort_order, each with their content fields.
     * Uses caching for performance.
     */
    public function index()
    {
        // Statistics are cached separately with a shorter TTL (60 seconds)
        // so platform counts update frequently without busting the full homepage cache.
        $stats = Cache::remember('platform_stats', 60, function () {
            return $this->getPlatformStatistics();
        });

        // Main homepage content cached for 10 minutes (layout, sections, settings, etc.)
        $data = Cache::remember('homepage_content', 600, function () {
            $settings = SiteSetting::instance();

            $sections = HomepageSection::with('contents')
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->get()
                ->keyBy('section_key');

            $content = [];
            foreach ($sections as $key => $section) {
                $content[$key] = $section->contentMap();
            }

            $features = HomepageFeature::where('status', 'active')
                ->orderBy('sort_order')
                ->get();

            $navItems = HomepageNavItem::where('status', 'active')
                ->orderBy('sort_order')
                ->get();

            // Load products featured on homepage
            $featuredProducts = Product::where('status', 'active')
                ->where('featured_on_homepage', true)
                ->orderBy('name')
                ->take(8)
                ->get();

            // Load active homepage slides for the top slider
            $slides = HomepageSlide::where('status', 'active')
                ->orderBy('sort_order')
                ->get();

            return compact('settings', 'sections', 'content', 'features', 'navItems', 'featuredProducts', 'slides');
        });

        // Merge the live statistics into the view data (not part of the long-lived cache)
        $data['stats'] = $stats;

        return view('homepage.index', $data);
    }

    /**
     * Get platform statistics for the homepage stats section.
     * Uses efficient queries with caching for performance.
     */
    private function getPlatformStatistics(): array
    {
        // Doctors count - users with Doctor role
        $statsDoctors = User::role('Doctor')->count();

        // Stores count - users with Store role
        $statsStores = User::role('Store')->count();

        // Products count - active products
        $statsProducts = Product::where('status', 'active')->count();

        // Orders count - total orders
        $statsOrders = Order::count();

        // States count - from states table
        $statsStates = State::count();

        // Years of experience (static - company established year)
        $establishedYear = 2009;
        $statsYears = max(1, date('Y') - $establishedYear);

        return compact(
            'statsDoctors',
            'statsStores',
            'statsProducts',
            'statsOrders',
            'statsStates',
            'statsYears'
        );
    }
}