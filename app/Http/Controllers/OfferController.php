<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display all active offers for users (End User only)
     */
    public function index()
    {
        // Check if offers system is enabled
        if (!SystemSettingService::isOffersEnabled()) {
            return redirect()->route('dashboard')
                ->with('info', 'Offers are currently unavailable.');
        }

        $dailyOffer = Offer::active()->forUsers()->daily()->first();
        $ongoingOffers = Offer::active()->forUsers()->where('offer_type', 'ongoing')->get();
        
        return view('offers.index', compact('dailyOffer', 'ongoingOffers'));
    }
}
