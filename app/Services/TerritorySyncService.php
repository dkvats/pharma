<?php

namespace App\Services;

use App\Models\MR\State;
use App\Models\MR\District;
use App\Models\MR\City;
use App\Models\MR\Area;
use App\Models\Pincode;
use Illuminate\Support\Str;

class TerritorySyncService
{
    /**
     * Sync territory hierarchy from PIN code data
     * Creates state, district, city, area if they don't exist
     * 
     * @param string $pincode
     * @return array|null Returns hierarchy IDs or null if PIN not found
     */
    public function syncFromPincode(string $pincode): ?array
    {
        $pincodeData = Pincode::where('pincode', $pincode)->first();
        
        if (!$pincodeData) {
            return null;
        }
        
        // Map PIN data to territory hierarchy
        // state = pincode.state
        // district = pincode.district
        // area = town-level location (relatedSuboffice > relatedHeadoffice > post_office)
        // city = fallback to district name when city data unavailable
        $stateName = $pincodeData->state;
        $districtName = $pincodeData->district;
        
        // Get town-level location name (priority: relatedSuboffice > relatedHeadoffice > post_office)
        $areaName = $this->getPreferredLocationName($pincodeData);
        
        $state = $this->getOrCreateState($stateName);
        $district = $this->getOrCreateDistrict($state->id, $districtName);
        
        // Use district name as fallback city since PIN data doesn't have separate city field
        // Mark as fallback by using district name
        $city = $this->getOrCreateCity($district->id, $districtName, true);
        
        // Area uses town-level location name (not branch office)
        $area = $this->getOrCreateArea($city->id, $areaName);
        
        return [
            'state_id' => $state->id,
            'district_id' => $district->id,
            'city_id' => $city->id,
            'area_id' => $area->id,
        ];
    }
    
    /**
     * Get existing state or create new one
     */
    private function getOrCreateState(string $stateName): State
    {
        $state = State::where('name', $stateName)->first();
        
        if ($state) {
            return $state;
        }
        
        return State::create([
            'name' => $stateName,
            'code' => $this->generateStateCode($stateName),
            'zone_id' => null,
            'is_active' => true,
        ]);
    }
    
    /**
     * Get existing district or create new one
     */
    private function getOrCreateDistrict(int $stateId, string $districtName): District
    {
        $district = District::where('state_id', $stateId)
            ->where('name', $districtName)
            ->first();
        
        if ($district) {
            return $district;
        }
        
        return District::create([
            'name' => $districtName,
            'code' => $this->generateDistrictCode($stateId, $districtName),
            'state_id' => $stateId,
            'is_active' => true,
        ]);
    }
    
    /**
     * Get existing city or create new one
     * 
     * @param int $districtId
     * @param string $cityName
     * @param bool $isFallback Whether this city is a fallback (district name used when city data unavailable)
     * @return City
     */
    private function getOrCreateCity(int $districtId, string $cityName, bool $isFallback = false): City
    {
        $city = City::where('district_id', $districtId)
            ->where('name', $cityName)
            ->first();
        
        if ($city) {
            return $city;
        }
        
        return City::create([
            'name' => $cityName,
            'code' => $this->generateCityCode($districtId, $cityName),
            'district_id' => $districtId,
            'is_active' => true,
        ]);
    }
    
    /**
     * Get existing area or create new one
     * Always uses actual post office name, ignores fabricated/generic area names
     */
    private function getOrCreateArea(int $cityId, string $postOfficeName): Area
    {
        // Search for area with the actual post office name
        $area = Area::where('city_id', $cityId)
            ->where('name', $postOfficeName)
            ->first();
        
        if ($area) {
            return $area;
        }
        
        // Check if existing areas are generic/fabricated (contain "Area" with number)
        // If so, we ignore them and create a new area with the real post office name
        $existingAreas = Area::where('city_id', $cityId)->get();
        
        foreach ($existingAreas as $existingArea) {
            // If this is a generic name like "Area 1", "Area 33", skip it
            if ($this->isGenericAreaName($existingArea->name)) {
                continue;
            }
            
            // If we find a match with a real name, use it
            if ($existingArea->name === $postOfficeName) {
                return $existingArea;
            }
        }
        
        // Create new area with the actual post office name
        return Area::create([
            'name' => $postOfficeName,
            'code' => $this->generateAreaCode($cityId, $postOfficeName),
            'city_id' => $cityId,
            'description' => null,
            'is_active' => true,
        ]);
    }
    
    /**
     * Get preferred location name for display (town-level)
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

    /**
     * Check if area name is generic/fabricated (e.g., "Area 1", "Area 33")
     */
    private function isGenericAreaName(string $name): bool
    {
        // Pattern: "Area" followed by optional spaces and numbers
        // Matches: "Area 1", "Area 33", "Area  1", etc.
        return preg_match('/^area\s*\d+$/i', trim($name)) === 1;
    }
    
    /**
     * Generate unique state code
     */
    private function generateStateCode(string $stateName): string
    {
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $stateName), 0, 3));
        $counter = 1;
        $originalCode = $code;
        
        while (State::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }
    
    /**
     * Generate unique district code
     */
    private function generateDistrictCode(int $stateId, string $districtName): string
    {
        $state = State::find($stateId);
        $prefix = $state ? $state->code : 'DST';
        $code = $prefix . '-' . strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $districtName), 0, 3));
        $counter = 1;
        $originalCode = $code;
        
        while (District::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }
    
    /**
     * Generate unique city code
     */
    private function generateCityCode(int $districtId, string $cityName): string
    {
        $district = District::find($districtId);
        $prefix = $district ? $district->code : 'CTY';
        $code = $prefix . '-' . strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cityName), 0, 3));
        $counter = 1;
        $originalCode = $code;
        
        while (City::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }
    
    /**
     * Generate unique area code
     */
    private function generateAreaCode(int $cityId, string $areaName): string
    {
        $city = City::find($cityId);
        $prefix = $city ? $city->code : 'ARE';
        $code = $prefix . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $areaName), 0, 5));
        $counter = 1;
        $originalCode = $code;
        
        while (Area::where('code', $code)->exists()) {
            $code = $originalCode . $counter;
            $counter++;
        }
        
        return $code;
    }
}
