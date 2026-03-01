<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PincodeController extends Controller
{
    private string $jsonPath;
    
    public function __construct()
    {
        // Use file inside Laravel storage
        $this->jsonPath = storage_path('app/pincodes/processed_zip_codes.json');
    }
    
    /**
     * Get PIN data from JSON file (cached)
     */
    private function getPinData(): array
    {
        // Use unique cache key to avoid stale data issues
        $cacheKey = 'pincode_data_v2_' . md5($this->jsonPath);
        
        return Cache::remember($cacheKey, 3600, function () {
            if (!file_exists($this->jsonPath)) {
                return [];
            }
            
            $content = file_get_contents($this->jsonPath);
            $content = trim($content);
            
            // Remove BOM if present
            $bom = pack('H*', 'EFBBBF');
            $content = preg_replace("/^$bom/", '', $content);
            
            $data = json_decode($content, true);
            
            return is_array($data) ? $data : [];
        });
    }

    /**
     * Lookup PIN code and return location data
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

        $pinData = $this->getPinData();
        
        if (empty($pinData)) {
            return response()->json([
                'success' => false,
                'message' => 'PIN code database not available.',
            ], 500);
        }

        // Search in nested structure: State → PIN → Post Office
        $foundState = null;
        $postOffice = null;
        
        foreach ($pinData as $stateName => $statePins) {
            if (!is_array($statePins)) {
                continue;
            }
            
            // Check if PIN exists in this state (PIN as string key)
            if (isset($statePins[$pin])) {
                $foundState = $stateName;
                $postOffice = $statePins[$pin];
                break;
            }
        }

        if (!$foundState || !$postOffice) {
            return response()->json([
                'success' => false,
                'message' => 'PIN code not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pincode' => $pin,
                'state' => $foundState,
                'post_office' => $postOffice,
                'area' => $postOffice, // Alias for compatibility
            ],
        ]);
    }

    /**
     * Search PIN codes by state
     */
    public function searchByState(Request $request): JsonResponse
    {
        $state = $request->get('state');
        
        if (!$state) {
            return response()->json([
                'success' => false,
                'message' => 'State parameter required.',
            ], 422);
        }

        $pinData = $this->getPinData();
        
        if (!isset($pinData[$state]) || !is_array($pinData[$state])) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $pincodes = [];
        $count = 0;
        foreach ($pinData[$state] as $pin => $office) {
            if ($count >= 100) break;
            $pincodes[] = [
                'pincode' => $pin,
                'post_office' => $office,
            ];
            $count++;
        }

        return response()->json([
            'success' => true,
            'data' => $pincodes,
        ]);
    }
}
