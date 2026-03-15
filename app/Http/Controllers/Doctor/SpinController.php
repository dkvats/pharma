<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Services\DoctorTargetService;
use App\Services\NotificationService;
use App\Services\SpinService;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SpinController extends Controller
{
    protected $spinService;

    public function __construct(SpinService $spinService)
    {
        $this->spinService = $spinService;
    }

    /**
     * Show the spin page
     */
    public function index()
    {
        // Check if spin system is enabled
        if (!SystemSettingService::isSpinEnabled()) {
            return redirect()->route('doctor.dashboard')
                ->with('error', 'Spin system is currently disabled by Super Admin.');
        }

        $doctorId = auth()->id();
        $canSpin = $this->spinService->canSpin($doctorId);
        $spinHistory = $this->spinService->getSpinHistory($doctorId);
        
        $doctorTargetService = new DoctorTargetService();
        $remainingSpins = $doctorTargetService->getRemainingSpins($doctorId);

        return view('doctor.spin.index', compact('canSpin', 'spinHistory', 'remainingSpins'));
    }

    /**
     * Perform a spin
     */
    public function spin(Request $request)
    {
        // Check if spin system is enabled
        if (!SystemSettingService::isSpinEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Spin system is disabled by Super Admin.'
            ], 403);
        }

        $doctorId = auth()->id();
        $doctorTargetService = new DoctorTargetService();

        if (!$this->spinService->canSpin($doctorId)) {
            return response()->json([
                'success' => false,
                'message' => 'Spin limit reached for this month.'
            ]);
        }

        $reward = $this->spinService->spin($doctorId);

        if (!$reward) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to process spin. Please try again later.'
            ]);
        }

        // Log spin activity
        logActivity(
            'Spin Claimed',
            'Spin',
            $reward->id,
            "Doctor spun and won: {$reward->name}"
        );

        // Invalidate doctor report cache after spin
        $this->invalidateDoctorCache($doctorId);

        // Check if it's a win or loss (Try Again)
        $isWin = !($reward->value == 0 || (strtolower($reward->type) == 'other' && stripos($reward->name, 'try again') !== false));

        if ($isWin) {
            // Send spin reward notification
            NotificationService::sendSpinReward($reward, auth()->user());
            
            return response()->json([
                'success' => true,
                'is_win' => true,
                'reward_id' => $reward->id,
                'reward_name' => $reward->name,
                'reward_value' => $reward->value,
                'reward_image' => $reward->image,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'is_win' => false,
                'reward_name' => $reward->name,
                'reward_image' => $reward->image,
            ]);
        }
    }

    /**
     * Show spin history
     */
    public function history()
    {
        // Check if spin system is enabled
        if (!SystemSettingService::isSpinEnabled()) {
            return redirect()->route('doctor.dashboard')
                ->with('error', 'Spin system is currently disabled by Super Admin.');
        }

        $doctorId = auth()->id();
        $spinHistory = $this->spinService->getSpinHistory($doctorId, 50);

        return view('doctor.spin.history', compact('spinHistory'));
    }

    /**
     * Claim a reward
     */
    public function claim(Request $request, $spinId)
    {
        // Check if spin system is enabled
        if (!SystemSettingService::isSpinEnabled()) {
            return back()->with('error', 'Spin system is disabled by Super Admin.');
        }

        $doctorId = auth()->id();

        $success = $this->spinService->claimReward($spinId, $doctorId);

        if ($success) {
            return back()->with('success', 'Reward claimed successfully!');
        }

        return back()->with('error', 'Unable to claim reward.');
    }

    /**
     * Invalidate doctor-related caches
     */
    private function invalidateDoctorCache(int $doctorId): void
    {
        // Invalidate doctor report cache
        Cache::forget("doctor_report_{$doctorId}_" . now()->format('Y-m-d'));
        
        // Invalidate any date-range caches for this doctor
        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            Cache::forget("doctor_report_{$doctorId}_{$date}");
        }
    }
}
