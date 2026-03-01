<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\SpinCampaign;
use Illuminate\Http\Request;

class SpinCampaignController extends Controller
{
    /**
     * Display the campaign management page
     */
    public function index()
    {
        $rewards = Reward::where('is_active', true)->get();
        $activeCampaign = SpinCampaign::getActive();
        $upcomingCampaigns = SpinCampaign::where('is_active', true)
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->get();
        $pastCampaigns = SpinCampaign::where('ends_at', '<', now())
            ->orWhere('is_active', false)
            ->orderByDesc('ends_at')
            ->paginate(10);

        return view('admin.spin-campaigns.index', compact(
            'rewards',
            'activeCampaign',
            'upcomingCampaigns',
            'pastCampaigns'
        ));
    }

    /**
     * Store a new campaign
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reward_id' => ['required', 'exists:rewards,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);

        // Check for overlapping campaigns
        $overlap = SpinCampaign::where('is_active', true)
            ->where(function ($q) use ($validated) {
                $q->whereBetween('starts_at', [$validated['starts_at'], $validated['ends_at']])
                    ->orWhereBetween('ends_at', [$validated['starts_at'], $validated['ends_at']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('starts_at', '<=', $validated['starts_at'])
                            ->where('ends_at', '>=', $validated['ends_at']);
                    });
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Campaign dates overlap with an existing active campaign.');
        }

        SpinCampaign::create($validated);

        return back()->with('success', 'Campaign created successfully.');
    }

    /**
     * Update a campaign
     */
    public function update(Request $request, SpinCampaign $campaign)
    {
        if ($campaign->ends_at < now()) {
            return back()->with('error', 'Cannot edit past campaigns.');
        }

        $validated = $request->validate([
            'reward_id' => ['required', 'exists:rewards,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);

        $campaign->update($validated);

        return back()->with('success', 'Campaign updated successfully.');
    }

    /**
     * Deactivate a campaign
     */
    public function destroy(SpinCampaign $campaign)
    {
        $campaign->update(['is_active' => false]);

        return back()->with('success', 'Campaign deactivated.');
    }
}
