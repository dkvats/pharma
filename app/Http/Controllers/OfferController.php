<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Display all active offers for users
     */
    public function index()
    {
        $dailyOffer = Offer::active()->daily()->first();
        $ongoingOffers = Offer::active()->where('offer_type', 'ongoing')->get();
        
        return view('offers.index', compact('dailyOffer', 'ongoingOffers'));
    }
}
