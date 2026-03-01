<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pincode;
use App\Services\TerritorySyncService;
use Illuminate\Http\JsonResponse;

class PincodeController extends Controller
{
    protected TerritorySyncService $territorySync;

    public function __construct(TerritorySyncService $territorySync)
    {
        $this->territorySync = $territorySync;
    }

    /**
     * Lookup PIN code and return location data
     * Auto-syncs territory hierarchy if PIN exists but territory doesn't
     */
    public function lookup(string $pin): JsonResponse
    {
        // Validate PIN format (6-digit numeric string)
        if (!preg_match('/^\d{6}$/', $pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN code. Must be 6 digits.',
            ], 422);
        }

        $pincode = Pincode::where('pincode', $pin)->first();

        if (!$pincode) {
            return response()->json([
                'success' => false,
                'message' => 'PIN code not found.',
            ], 404);
        }

        // Auto-sync territory hierarchy from PIN data
        $territoryIds = $this->territorySync->syncFromPincode($pin);

        // Determine the best area name to display (town-level location)
        // Priority: relatedSuboffice (S.O) > relatedHeadoffice (H.O) > post_office (B.O)
        $areaName = $this->getPreferredLocationName($pincode);

        return response()->json([
            'success' => true,
            'data' => [
                'pincode' => $pincode->pincode,
                'state' => $pincode->state,
                'district' => $pincode->district,
                'post_office' => $pincode->post_office,
                'area' => $areaName, // Town-level location for display
                'office_type' => $pincode->office_type,
                'related_suboffice' => $pincode->related_suboffice,
                'related_headoffice' => $pincode->related_headoffice,
                'territory' => $territoryIds, // Include territory IDs for auto-fill
            ],
        ]);
    }

    /**
     * Get preferred location name for display
     * Priority: relatedSuboffice (S.O) > relatedHeadoffice (H.O) > post_office (B.O)
     */
    private function getPreferredLocationName(Pincode $pincode): string
    {
        // Priority 1: Use relatedSuboffice (Sub Office) if available
        if (!empty($pincode->related_suboffice)) {
            return $this->cleanOfficeSuffix($pincode->related_suboffice);
        }

        // Priority 2: Use relatedHeadoffice (Head Office) if available
        if (!empty($pincode->related_headoffice)) {
            return $this->cleanOfficeSuffix($pincode->related_headoffice);
        }

        // Priority 3: Fallback to post_office (Branch Office)
        return $this->cleanOfficeSuffix($pincode->post_office);
    }

    /**
     * Clean office suffixes (S.O, H.O, B.O) from location name
     */
    private function cleanOfficeSuffix(string $name): string
    {
        return preg_replace('/\s+(S\.O|H\.O|B\.O|SO|HO|BO)\.?$/i', '', trim($name));
    }
}
