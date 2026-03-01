<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrescriptionController extends Controller
{
    /**
     * Download prescription file with authorization check
     */
    public function download(Order $order): StreamedResponse
    {
        $user = auth()->user();
        
        // Authorization check - only authorized users can download
        $canDownload = $this->canDownloadPrescription($user, $order);
        
        if (!$canDownload) {
            // Log failed access attempt for security audit
            \App\Services\ActivityLogService::logPrescriptionAccessDenied($order, 'download', 'User not authorized to access this prescription');
            abort(403, 'Unauthorized access to prescription.');
        }
        
        // Check if prescription exists
        if (!$order->hasPrescription()) {
            abort(404, 'Prescription not found.');
        }
        
        $filePath = $order->prescription;
        
        // Check file exists in PRIVATE disk
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'Prescription file not found.');
        }
        
        // Log access for audit trail
        \App\Services\ActivityLogService::logPrescriptionDownload($order);
        
        // Return file download response from PRIVATE disk
        return Storage::disk('private')->download($filePath, 'prescription_' . $order->order_number . '_' . now()->format('Ymd') . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }
    
    /**
     * View prescription file with authorization check (for browser display)
     */
    public function view(Order $order)
    {
        $user = auth()->user();
        
        // Authorization check
        $canView = $this->canDownloadPrescription($user, $order);
        
        if (!$canView) {
            // Log failed access attempt for security audit
            \App\Services\ActivityLogService::logPrescriptionAccessDenied($order, 'view', 'User not authorized to access this prescription');
            abort(403, 'Unauthorized access to prescription.');
        }
        
        // Check if prescription exists
        if (!$order->hasPrescription()) {
            abort(404, 'Prescription not found.');
        }
        
        $filePath = $order->prescription;
        
        // Check file exists in PRIVATE disk
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'Prescription file not found.');
        }
        
        // Log access
        \App\Services\ActivityLogService::logPrescriptionView($order);
        
        // Return file for browser display from PRIVATE disk
        return response()->file(Storage::disk('private')->path($filePath));
    }
    
    /**
     * Check if user can download/view prescription
     */
    private function canDownloadPrescription($user, Order $order): bool
    {
        // Admin can view all prescriptions
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // End User can view their own prescriptions
        if ($order->user_id === $user->id) {
            return true;
        }
        
        // Store can view prescriptions for orders linked to them
        if ($user->hasRole('Store') && $order->store_id === $user->id) {
            return true;
        }
        
        // Doctor can view prescriptions for orders they referred
        if ($user->hasRole('Doctor') && $order->doctor_id === $user->id) {
            return true;
        }
        
        return false;
    }
}
