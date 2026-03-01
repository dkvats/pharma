<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MR\State;
use App\Models\MR\District;
use App\Models\MR\City;
use App\Models\MR\Area;
use App\Models\MR\Zone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TerritoryController extends Controller
{
    /**
     * Display states list
     */
    public function states()
    {
        $states = State::with('zone')->orderBy('name')->paginate(20);
        $zones = Zone::where('is_active', true)->orderBy('name')->get();
        return view('admin.territory.states', compact('states', 'zones'));
    }

    /**
     * Store new state
     */
    public function storeState(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:mr_states,code',
            'zone_id' => 'nullable|exists:mr_zones,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        State::create($validated);

        return redirect()->route('admin.territory.states')
            ->with('success', 'State created successfully');
    }

    /**
     * Update state
     */
    public function updateState(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', Rule::unique('mr_states', 'code')->ignore($state->id)],
            'zone_id' => 'nullable|exists:mr_zones,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $state->update($validated);

        return redirect()->route('admin.territory.states')
            ->with('success', 'State updated successfully');
    }

    /**
     * Delete state
     */
    public function destroyState(State $state)
    {
        // Check if state has districts
        if ($state->districts()->count() > 0) {
            return redirect()->route('admin.territory.states')
                ->with('error', 'Cannot delete state with existing districts');
        }

        $state->delete();
        return redirect()->route('admin.territory.states')
            ->with('success', 'State deleted successfully');
    }

    /**
     * Display districts list
     */
    public function districts()
    {
        $districts = District::with('state')->orderBy('name')->paginate(20);
        $states = State::where('is_active', true)->orderBy('name')->get();
        return view('admin.territory.districts', compact('districts', 'states'));
    }

    /**
     * Store new district
     */
    public function storeDistrict(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:mr_districts,code',
            'state_id' => 'required|exists:mr_states,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        District::create($validated);

        return redirect()->route('admin.territory.districts')
            ->with('success', 'District created successfully');
    }

    /**
     * Update district
     */
    public function updateDistrict(Request $request, District $district)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', Rule::unique('mr_districts', 'code')->ignore($district->id)],
            'state_id' => 'required|exists:mr_states,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $district->update($validated);

        return redirect()->route('admin.territory.districts')
            ->with('success', 'District updated successfully');
    }

    /**
     * Delete district
     */
    public function destroyDistrict(District $district)
    {
        // Check if district has cities
        if ($district->cities()->count() > 0) {
            return redirect()->route('admin.territory.districts')
                ->with('error', 'Cannot delete district with existing cities');
        }

        $district->delete();
        return redirect()->route('admin.territory.districts')
            ->with('success', 'District deleted successfully');
    }

    /**
     * Display cities list
     */
    public function cities()
    {
        $cities = City::with('district.state')->orderBy('name')->paginate(20);
        $states = State::where('is_active', true)->orderBy('name')->get();
        return view('admin.territory.cities', compact('cities', 'states'));
    }

    /**
     * Get districts by state (for AJAX)
     */
    public function getDistrictsByState(State $state)
    {
        return $state->districts()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Store new city
     */
    public function storeCity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:mr_cities,code',
            'district_id' => 'required|exists:mr_districts,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        City::create($validated);

        return redirect()->route('admin.territory.cities')
            ->with('success', 'City created successfully');
    }

    /**
     * Update city
     */
    public function updateCity(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', Rule::unique('mr_cities', 'code')->ignore($city->id)],
            'district_id' => 'required|exists:mr_districts,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $city->update($validated);

        return redirect()->route('admin.territory.cities')
            ->with('success', 'City updated successfully');
    }

    /**
     * Delete city
     */
    public function destroyCity(City $city)
    {
        // Check if city has areas
        if ($city->areas()->count() > 0) {
            return redirect()->route('admin.territory.cities')
                ->with('error', 'Cannot delete city with existing areas');
        }

        $city->delete();
        return redirect()->route('admin.territory.cities')
            ->with('success', 'City deleted successfully');
    }

    /**
     * Display areas list
     */
    public function areas()
    {
        $areas = Area::with('city.district.state')->orderBy('name')->paginate(20);
        $states = State::where('is_active', true)->orderBy('name')->get();
        return view('admin.territory.areas', compact('areas', 'states'));
    }

    /**
     * Get cities by district (for AJAX)
     */
    public function getCitiesByDistrict(District $district)
    {
        return $district->cities()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Store new area
     */
    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:mr_areas,code',
            'city_id' => 'required|exists:mr_cities,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        Area::create($validated);

        return redirect()->route('admin.territory.areas')
            ->with('success', 'Area created successfully');
    }

    /**
     * Update area
     */
    public function updateArea(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', Rule::unique('mr_areas', 'code')->ignore($area->id)],
            'city_id' => 'required|exists:mr_cities,id',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $area->update($validated);

        return redirect()->route('admin.territory.areas')
            ->with('success', 'Area updated successfully');
    }

    /**
     * Delete area
     */
    public function destroyArea(Area $area)
    {
        // Check if area has doctors
        if ($area->doctors()->count() > 0) {
            return redirect()->route('admin.territory.areas')
                ->with('error', 'Cannot delete area with existing doctors');
        }

        $area->delete();
        return redirect()->route('admin.territory.areas')
            ->with('success', 'Area deleted successfully');
    }
}