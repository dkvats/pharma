<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Area;
use App\Models\MR\City;
use App\Models\MR\District;
use App\Models\MR\State;
use App\Models\MR\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MRStoreController extends Controller
{
    /**
     * Display stores registered by this MR
     */
    public function index()
    {
        $stores = Store::where('mr_id', auth()->id())
            ->with(['user', 'area', 'city', 'district', 'state'])
            ->latest()
            ->paginate(15);

        return view('mr.stores.index', compact('stores'));
    }

    /**
     * Show store registration form
     */
    public function create()
    {
        $states = State::where('is_active', true)->get();
        return view('mr.stores.create', compact('states'));
    }

    /**
     * Store a newly registered store
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone'      => 'required|string|max:20|unique:users,phone',
            'email'      => 'nullable|email|max:255|unique:users,email',
            'address'    => 'required|string',
            'pincode'    => 'required|string|size:6',
            'state_id'   => 'required|exists:mr_states,id',
            'district_id'=> 'required|exists:mr_districts,id',
            'city_id'    => 'required|exists:mr_cities,id',
            'area_id'    => 'required|exists:mr_areas,id',
        ], [
            'phone.unique' => 'This phone number is already registered. Please use a different phone number.',
            'email.unique' => 'This email address is already registered. Please use a different email address.',
        ]);

        // Get territory names for display
        $state    = State::find($validated['state_id']);
        $district = District::find($validated['district_id']);
        $city     = City::find($validated['city_id']);
        $area     = Area::find($validated['area_id']);

        // Generate unique store code
        $storeCode = 'STR-' . strtoupper(Str::random(8));

        // Create user account for the store
        $user = $this->createStoreUser($validated, $storeCode);

        // Determine mr_id: if Admin, use first MR; if MR, use self
        $mrId = auth()->user()->hasRole('Admin')
            ? User::role('MR')->first()->id
            : auth()->id();

        // Determine status: Admin-created stores are auto-approved
        $status = auth()->user()->hasRole('Admin') ? 'approved' : 'pending';

        // Create store record
        $store = Store::create([
            'mr_id' => $mrId,
            'user_id' => $user->id,
            'store_name' => $validated['store_name'],
            'owner_name' => $validated['owner_name'],
            'store_code' => $storeCode,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'],
            'pincode' => $validated['pincode'],
            'state_id' => $validated['state_id'],
            'district_id' => $validated['district_id'],
            'city_id' => $validated['city_id'],
            'area_id' => $validated['area_id'],
            'state' => $state?->name,
            'district' => $district?->name,
            'city' => $city?->name,
            'area' => $area?->name,
            'status' => $status,
        ]);

        // Sync store_code to user's unique_code for referral matching
        $user->update(['unique_code' => $storeCode]);

        // If Admin created, activate immediately; otherwise deactivate until approval
        $user->update(['status' => auth()->user()->hasRole('Admin') ? 'active' : 'inactive']);

        // Redirect based on role
        if (auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.stores.approval.index')
                ->with('success', 'Store created and approved successfully. Login account created with email: ' . $user->email);
        }

        return redirect()->route('mr.stores.index')
            ->with('success', 'Store registered successfully. Awaiting admin approval. Login account created with email: ' . $user->email);
    }

    /**
     * Display the specified store
     */
    public function show(Store $store)
    {
        $this->authorizeAccess($store);

        $store->load('user', 'approver');

        return view('mr.stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified store
     */
    public function edit(Store $store)
    {
        $this->authorizeAccess($store);

        // Only allow editing if store is pending or rejected
        if (!$store->isPending() && !$store->isRejected()) {
            return redirect()->route('mr.stores.index')
                ->with('error', 'Cannot edit store that is already approved.');
        }

        $states = State::where('is_active', true)->get();
        return view('mr.stores.edit', compact('store', 'states'));
    }

    /**
     * Update the specified store
     */
    public function update(Request $request, Store $store)
    {
        $this->authorizeAccess($store);

        // Only allow editing if store is pending or rejected
        if (!$store->isPending() && !$store->isRejected()) {
            return redirect()->route('mr.stores.index')
                ->with('error', 'Cannot edit store that is already approved.');
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'pincode' => 'required|string|size:6',
            'state_id' => 'required|exists:mr_states,id',
            'district_id' => 'required|exists:mr_districts,id',
            'city_id' => 'required|exists:mr_cities,id',
            'area_id' => 'required|exists:mr_areas,id',
        ]);

        // Get territory names for display
        $state = State::find($validated['state_id']);
        $district = District::find($validated['district_id']);
        $city = City::find($validated['city_id']);
        $area = Area::find($validated['area_id']);

        $store->update([
            ...$validated,
            'state' => $state?->name,
            'district' => $district?->name,
            'city' => $city?->name,
            'area' => $area?->name,
        ]);

        // Update user account
        if ($store->user) {
            $store->user->update([
                'name' => $validated['store_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? $store->user->email,
            ]);
        }

        return redirect()->route('mr.stores.index')
            ->with('success', 'Store updated successfully.');
    }

    /**
     * Create a user account for the store
     */
    private function createStoreUser(array $storeData, string $storeCode): User
    {
        // Generate system email if not provided
        $email = $this->generateSystemEmail($storeData, $storeCode);

        // Safety net: if a user with this phone already exists, return them
        // (normally caught earlier by the unique:users,phone validation rule)
        $existingByPhone = User::where('phone', $storeData['phone'])->first();
        if ($existingByPhone) {
            return $existingByPhone;
        }

        // Check if user with this email already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return $existingUser;
        }

        // Generate username from store name
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $storeData['store_name']));
        $username = $baseUsername;
        $counter = 1;

        // Ensure unique username
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        // Generate random password
        $password = Str::random(12);

        // Create user
        $user = User::create([
            'name' => $storeData['store_name'],
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'phone' => $storeData['phone'],
            'status' => 'inactive', // Inactive until approved
            'created_by' => auth()->id(),
        ]);

        // Assign Store role
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Store');
        }

        return $user;
    }

    /**
     * Generate system email for store
     */
    private function generateSystemEmail(array $storeData, string $storeCode): string
    {
        // Use provided email if available
        if (!empty($storeData['email'])) {
            return $storeData['email'];
        }
        
        // Generate from store_code
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $storeCode));
        $email = $base . '@pharma.local';
        
        // Ensure unique
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $base . $counter . '@pharma.local';
            $counter++;
        }
        
        return $email;
    }

    /**
     * Authorize access to store (only owner MR can access)
     */
    private function authorizeAccess(Store $store): void
    {
        if ($store->mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access to store.');
        }
    }

    /**
     * Get districts for a state (for cascading dropdowns)
     */
    public function getDistricts($stateId)
    {
        return District::where('state_id', $stateId)->where('is_active', true)->get();
    }

    /**
     * Get cities for a district (for cascading dropdowns)
     */
    public function getCities($districtId)
    {
        return City::where('district_id', $districtId)->where('is_active', true)->get();
    }

    /**
     * Get areas for a city (for cascading dropdowns)
     */
    public function getAreas($cityId)
    {
        return Area::where('city_id', $cityId)->where('is_active', true)->get();
    }
}
