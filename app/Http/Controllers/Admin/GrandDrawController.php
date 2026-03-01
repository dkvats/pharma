<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GrandDrawService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GrandDrawController extends Controller
{
    /**
     * CONVENTION: F12-click Route::resource() to see all methods
     * index() - Show grand draw page
     * runDraw() - POST /admin/grand-draw/run
     * history() - Show past winners
     */
    protected GrandDrawService $grandDrawService;

    public function __construct(GrandDrawService $grandDrawService)
    {
        $this->grandDrawService = $grandDrawService;
    }

    /**
     * Display Grand Lucky Draw management page
     */
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        try {
            $statistics = $this->grandDrawService->getStatistics($year);
            return view('admin.grand-draw.index', compact('statistics', 'year'));
        } catch (\Throwable $e) {
            Log::error('Grand Draw index error: ' . $e->getMessage(), [
                'year' => $year,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Failed to load Grand Draw page. Please try again.');
        }
    }

    /**
     * Run the Grand Lucky Draw
     */
    public function runDraw(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        $result = $this->grandDrawService->runDraw(auth()->id(), $year);
        
        if ($result['success']) {
            return redirect()
                ->route('admin.grand-draw.index', ['year' => $year])
                ->with('success', $result['message'])
                ->with('winner', $result['winner']);
        }
        
        return redirect()
            ->route('admin.grand-draw.index', ['year' => $year])
            ->with('error', $result['message']);
    }

    /**
     * View draw history (all years)
     */
    public function history()
    {
        $winners = \App\Models\GrandDrawWinner::with(['doctor', 'drawnBy'])
            ->orderBy('year', 'desc')
            ->get();
        
        return view('admin.grand-draw.history', compact('winners'));
    }
}
