<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Area;
use App\Models\MR\City;
use App\Models\MR\District;
use App\Models\MR\State;
use App\Models\MR\Store;
use App\Models\MR\StoreUpdateRequest;
use App\Models\User;
use App\Notifications\StoreUpdateRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MRStoreController extends Controller
{
    /**
     * Display stores
     * - Admin: All stores
     * - MR: Only their own stores
     */
    public function index()
    {
        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // Admin views all stores with filters
            $query = Store::with(['user', 'area', 'city', 'district', 'state', 'mr', 'assignedMr', 'approver']);
            
            // Filter by status if requested
            if (request('status')) {
                $query->where('status', request('status'));
            }

            $stores = $query->latest()->paginate(15);
            
            return view('admin.stores.index', compact('stores'));
        }

        // MR views only their registered stores
        $stores = Store::where(function ($query) {
                $query->where('assigned_mr_id', auth()->id())
                    ->orWhere('mr_id', auth()->id());
            })
            ->with(['user', 'area', 'city', 'district', 'state', 'pendingUpdateRequests'])
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
            // A. Basic Store Details
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone'      => 'required|regex:/^\d{10}$/|unique:users,phone',
            'alt_phone'  => 'nullable|regex:/^\d{10}$/',
            'email'      => 'nullable|email|max:255|unique:users,email',
            
            // B. Owner Identity
            'aadhaar'    => 'nullable|regex:/^\d{12}$/|unique:mr_stores,aadhaar',
            'owner_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // C. Store Media
            'store_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // D. Address & Location (Auto-filled)
            'address'    => 'required|string|max:500',
            'pincode'    => 'required|regex:/^\d{6}$/|size:6',
            'state_id'   => 'required|exists:mr_states,id',
            'district_id'=> 'required|exists:mr_districts,id',
            'city_id'    => 'required|exists:mr_cities,id',
            'area_id'    => 'required|exists:mr_areas,id',
            
            // E. Business Details
            'gst_no'        => 'nullable|regex:/^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/|unique:mr_stores,gst_no',
            'drug_license_no' => 'nullable|string|max:50|unique:mr_stores,drug_license_no',
            'license_expiry' => 'nullable|date|after:today',
            'pan_no'        => 'nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/|unique:mr_stores,pan_no',
            
            // F. Store Type
            'store_type' => 'nullable|in:retail,distributor,clinic,pharmacy',
            
            // G. Financial Settings
            'default_discount' => 'nullable|numeric|min:0|max:100',
            'credit_limit'     => 'nullable|numeric|min:0',
            'payment_terms'    => 'nullable|string|max:100',
        ], [
            'phone.regex' => 'Mobile number must be 10 digits.',
            'phone.unique' => 'This phone number is already registered.',
            'alt_phone.regex' => 'Alternate phone must be 10 digits.',
            'email.unique' => 'This email address is already registered.',
            'aadhaar.regex' => 'Aadhaar must be 12 digits.',
            'aadhaar.unique' => 'This Aadhaar number is already registered.',
            'gst_no.regex' => 'Invalid GST number format.',
            'pan_no.regex' => 'Invalid PAN number format.',
            'license_expiry.after' => 'License expiry must be in the future.',
        ]);

        // Handle file uploads
        $ownerPhotoPath = null;
        $storePhotoPath = null;

        if ($request->hasFile('owner_photo')) {
            try {
                $ownerPhotoPath = $request->file('owner_photo')->store('stores/owners', 'public');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['owner_photo' => 'Failed to upload owner photo.']);
            }
        }

        if ($request->hasFile('store_photo')) {
            try {
                $storePhotoPath = $request->file('store_photo')->store('stores/storefronts', 'public');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['store_photo' => 'Failed to upload store photo.']);
            }
        }

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

        // Create store record with all new fields
        $store = Store::create([
            'mr_id' => $mrId,
            'assigned_mr_id' => $mrId,
            'user_id' => $user->id,
            'store_name' => $validated['store_name'],
            'owner_name' => $validated['owner_name'],
            'store_code' => $storeCode,
            'phone' => $validated['phone'],
            'alt_phone' => $validated['alt_phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'aadhaar' => $validated['aadhaar'] ?? null,
            'owner_photo' => $ownerPhotoPath,
            'store_photo' => $storePhotoPath,
            'address' => $validated['address'],
            'gst_no' => $validated['gst_no'] ?? null,
            'drug_license_no' => $validated['drug_license_no'] ?? null,
            'license_expiry' => $validated['license_expiry'] ?? null,
            'pan_no' => $validated['pan_no'] ?? null,
            'pincode' => $validated['pincode'],
            'state_id' => $validated['state_id'],
            'district_id' => $validated['district_id'],
            'city_id' => $validated['city_id'],
            'area_id' => $validated['area_id'],
            'state' => $state?->name,
            'district' => $district?->name,
            'city' => $city?->name,
            'area' => $area?->name,
            'store_type' => $validated['store_type'] ?? null,
            'default_discount' => $validated['default_discount'] ?? 0,
            'credit_limit' => $validated['credit_limit'] ?? 0,
            'payment_terms' => $validated['payment_terms'] ?? null,
            'status' => $status,
        ]);

        // Sync store_code to user's unique_code for referral matching
        $user->update(['unique_code' => $storeCode]);

        // If Admin created, activate immediately; otherwise deactivate until approval
        $user->update(['status' => auth()->user()->hasRole('Admin') ? 'approved' : 'pending']);

        // Redirect based on role
        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            return redirect()->route($this->getStoreIndexRoute())
                ->with('success', 'Store created and approved successfully. Login account created with email: ' . $user->email);
        }

        return redirect()->route($this->getStoreIndexRoute())
            ->with('success', 'Store registered successfully. Awaiting admin approval. Login account created with email: ' . $user->email);
    }

    /**
     * Display the specified store
     * - Admin: Can view any store
     * - MR: Can only view their own stores
     */
    public function show(Store $store)
    {
        // Authorization check
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])
            && $store->mr_id !== auth()->id()
            && $store->assigned_mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access to store.');
        }

        $store->load('user', 'approver', 'state', 'district', 'city', 'area', 'updateRequests');

        return view('mr.stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified store
     * - Admin: Can edit any approved store
     * - MR: Can only edit their own pending/rejected stores
     */
    public function edit(Store $store)
    {
        // Authorization check
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])
            && $store->mr_id !== auth()->id()
            && $store->assigned_mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access to store.');
        }

        // MR can only edit pending or rejected stores
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !$store->isPending() && !$store->isRejected()) {
            return redirect()->route('mr.stores.index')
                ->with('error', 'Cannot edit store after approval. Submit an update request instead.');
        }

        $states = State::where('is_active', true)->get();
        return view('mr.stores.edit', compact('store', 'states'));
    }

    /**
     * Update the specified store
     * 
     * WORKFLOW:
     * - Admin: Direct update (changes applied immediately)
     * - MR: Create update request (pending admin approval)
     */
    public function update(Request $request, Store $store)
    {
        // Authorization check
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])
            && $store->mr_id !== auth()->id()
            && $store->assigned_mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access to store.');
        }

        // MR can only edit pending or rejected stores; Admin can edit any store
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !$store->isPending() && !$store->isRejected()) {
            return redirect()->route('mr.stores.index')
                ->with('error', 'Cannot edit approved store directly. Submit an update request instead.');
        }

        $validated = $request->validate([
            // A. Basic Store Details
            'store_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|regex:/^\d{10}$/',
            'alt_phone' => 'nullable|regex:/^\d{10}$/',
            'email' => 'nullable|email|max:255',
            
            // B. Owner Identity
            'aadhaar' => 'nullable|regex:/^\d{12}$/',
            'owner_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // C. Store Media
            'store_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            
            // D. Address & Location
            'address' => 'required|string|max:500',
            'pincode' => 'required|regex:/^\d{6}$/|size:6',
            'state_id' => 'required|exists:mr_states,id',
            'district_id' => 'required|exists:mr_districts,id',
            'city_id' => 'required|exists:mr_cities,id',
            'area_id' => 'required|exists:mr_areas,id',
            
            // E. Business Details
            'gst_no' => 'nullable|regex:/^\d{2}[A-Z]{5}\d{4}[A-Z]{1}[A-Z0-9]{1}Z[A-Z0-9]{1}$/',
            'drug_license_no' => 'nullable|string|max:50',
            'license_expiry' => 'nullable|date|after:today',
            'pan_no' => 'nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            
            // F. Store Type
            'store_type' => 'nullable|in:retail,distributor,clinic,pharmacy',
            
            // G. Financial Settings
            'default_discount' => 'nullable|numeric|min:0|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:100',
        ]);

        // Handle file uploads
        $ownerPhotoPath = $store->owner_photo;
        $storePhotoPath = $store->store_photo;

        if ($request->hasFile('owner_photo')) {
            try {
                $ownerPhotoPath = $request->file('owner_photo')->store('stores/owners', 'public');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['owner_photo' => 'Failed to upload owner photo.']);
            }
        }

        if ($request->hasFile('store_photo')) {
            try {
                $storePhotoPath = $request->file('store_photo')->store('stores/storefronts', 'public');
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['store_photo' => 'Failed to upload store photo.']);
            }
        }

        // Get territory names
        $state = State::find($validated['state_id']);
        $district = District::find($validated['district_id']);
        $city = City::find($validated['city_id']);
        $area = Area::find($validated['area_id']);

        // Build update data
        $updateData = [
            'store_name' => $validated['store_name'],
            'owner_name' => $validated['owner_name'],
            'phone' => $validated['phone'],
            'alt_phone' => $validated['alt_phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'aadhaar' => $validated['aadhaar'] ?? null,
            'owner_photo' => $ownerPhotoPath,
            'store_photo' => $storePhotoPath,
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
            'gst_no' => $validated['gst_no'] ?? null,
            'drug_license_no' => $validated['drug_license_no'] ?? null,
            'license_expiry' => $validated['license_expiry'] ?? null,
            'pan_no' => $validated['pan_no'] ?? null,
            'store_type' => $validated['store_type'] ?? null,
            'default_discount' => $validated['default_discount'] ?? 0,
            'credit_limit' => $validated['credit_limit'] ?? 0,
            'payment_terms' => $validated['payment_terms'] ?? null,
        ];

        // ════════════════════════════════════════════════════════════════
        // APPROVAL WORKFLOW LOGIC
        // ════════════════════════════════════════════════════════════════

        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            // ✅ ADMIN: Direct update (changes apply immediately)
            $store->update($updateData);

            // Also update store user account
            if ($store->user) {
                $store->user->update([
                    'name' => $validated['store_name'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? $store->user->email,
                ]);
            }

            return redirect()->route($this->getStoreIndexRoute())
                ->with('success', 'Store updated successfully. Changes applied immediately.');

        } else {
            // 📝 MR: Create update request (pending admin approval)
            
            // Delete any previous pending request for this store from this MR
            StoreUpdateRequest::where('store_id', $store->id)
                ->where('requested_by', auth()->id())
                ->where('status', 'pending')
                ->delete();

            // Create new update request
            $updateRequest = StoreUpdateRequest::create([
                'store_id' => $store->id,
                'requested_by' => auth()->id(),
                'requested_role' => 'mr',
                ...$updateData,
                'status' => 'pending',
            ]);

            // Notify all admins about the new update request
            $admins = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Super Admin']);
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new StoreUpdateRequested($updateRequest));
            }

            return redirect()->route($this->getStoreIndexRoute())
                ->with('success', 'Store update request submitted successfully. Awaiting admin approval.');
        }
    }

    /**
     * Resolve proper store module index route by role.
     */
    private function getStoreIndexRoute(): string
    {
        return auth()->user()->hasAnyRole(['Admin', 'Super Admin'])
            ? 'admin.stores.approval.index'
            : 'mr.stores.index';
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
