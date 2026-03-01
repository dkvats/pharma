<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Services\DoctorTargetService;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    protected $doctorTargetService;

    public function __construct(DoctorTargetService $doctorTargetService)
    {
        $this->doctorTargetService = $doctorTargetService;
    }

    /**
     * Display doctor targets
     */
    public function index()
    {
        $doctorId = auth()->id();
        
        // Current month progress
        $currentProgress = $this->doctorTargetService->getProgress($doctorId);
        
        // Target history (last 12 months)
        $targetHistory = $this->doctorTargetService->getTargetHistory($doctorId, 12);
        
        return view('doctor.targets.index', compact('currentProgress', 'targetHistory'));
    }
}
