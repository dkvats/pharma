<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display all active offers for Store
     */
    public function index()
    {
        // Check if offers system is enabled
        if (!SystemSettingService::isOffersEnabled()) {
            return redirect()->route('store.dashboard')
                ->with('info', 'Offers are currently unavailable.');
        }

        $offers = Offer::forStores()
            ->active()
            ->get();

        return view('store.offers.index', compact('offers'));
    }
}
