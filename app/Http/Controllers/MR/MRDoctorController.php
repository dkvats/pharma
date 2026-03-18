<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Area;
use App\Models\MR\City;
use App\Models\MR\District;
use App\Models\MR\Doctor;
use App\Models\MR\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MRDoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::forMR(auth()->id())
            ->with(['area', 'city', 'district', 'state'])
            ->latest()
            ->paginate(15);

        return view('mr.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $states = State::where('is_active', true)->get();
        return view('mr.doctors.create', compact('states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'clinic_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'state_id' => 'required|exists:mr_states,id',
            'district_id' => 'required|exists:mr_districts,id',
            'city_id' => 'required|exists:mr_cities,id',
            'area_id' => 'required|exists:mr_areas,id',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'aadhaar_no' => 'nullable|string|max:20',
            'pan_no' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:20',
            'account_no' => 'nullable|string|max:50',
        ]);

        $validated['doctor_code'] = 'DOC-' . strtoupper(Str::random(8));
        $validated['created_by'] = auth()->id();
        $validated['assigned_mr_id'] = auth()->id();
        $validated['status'] = 'pending'; // Default status for new doctors
        $validated['is_active'] = false; // Inactive until approved

        // ALWAYS create user account for the doctor (mandatory)
        $user = $this->createDoctorUser($validated);
        $validated['user_id'] = $user->id;

        Doctor::create($validated);
        
        // Sync doctor_code to user's unique_code for referral matching
        $user->update(['unique_code' => $validated['doctor_code']]);
        
        // Keep account pending until approved
        $user->update(['status' => 'pending', 'role' => 'doctor']);

        return redirect()->route('mr.doctors.index')
            ->with('success', 'Doctor registered successfully. Awaiting admin approval. Login account created with email: ' . $user->email);
    }

    /**
     * Create a user account for the doctor (MANDATORY)
     */
    private function createDoctorUser(array $doctorData): User
    {
        // Generate system email if not provided
        $email = $this->generateSystemEmail($doctorData);
        
        // Check if user with this email already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return $existingUser;
        }

        // Generate username from name (lowercase, no spaces)
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $doctorData['name']));
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
            'name' => $doctorData['name'],
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'phone' => $doctorData['mobile'] ?? $doctorData['phone'] ?? null,
            'status' => 'pending',
            'role' => 'doctor',
            'created_by' => $doctorData['created_by'] ?? auth()->id(),
        ]);

        // Assign Doctor role using spatie permission
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Doctor');
        }

        return $user;
    }
    
    /**
     * Generate system email for doctor
     */
    private function generateSystemEmail(array $doctorData): string
    {
        // Use provided email if available
        if (!empty($doctorData['email'])) {
            return $doctorData['email'];
        }
        
        // Generate from doctor_code or name
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $doctorData['doctor_code'] ?? $doctorData['name']));
        $email = $base . '@pharma.local';
        
        // Ensure unique
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $base . $counter . '@pharma.local';
            $counter++;
        }
        
        return $email;
    }

    public function show(Doctor $doctor)
    {
        $this->authorizeAccess($doctor);

        $doctor->load(['area', 'city', 'district', 'state', 'visits' => function ($q) {
            $q->latest()->take(10);
        }, 'orders' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('mr.doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $this->authorizeAccess($doctor);

        $states = State::where('is_active', true)->get();
        $districts = District::where('state_id', $doctor->state_id)->get();
        $cities = City::where('district_id', $doctor->district_id)->get();
        $areas = Area::where('city_id', $doctor->city_id)->get();

        return view('mr.doctors.edit', compact('doctor', 'states', 'districts', 'cities', 'areas'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $this->authorizeAccess($doctor);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'clinic_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'state_id' => 'required|exists:mr_states,id',
            'district_id' => 'required|exists:mr_districts,id',
            'city_id' => 'required|exists:mr_cities,id',
            'area_id' => 'required|exists:mr_areas,id',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'aadhaar_no' => 'nullable|string|max:20',
            'pan_no' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'ifsc' => 'nullable|string|max:20',
            'account_no' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $doctor->update($validated);

        return redirect()->route('mr.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $this->authorizeAccess($doctor);

        $doctor->update(['is_active' => false]);

        return redirect()->route('mr.doctors.index')
            ->with('success', 'Doctor deactivated successfully.');
    }

    public function visits(Doctor $doctor)
    {
        $this->authorizeAccess($doctor);

        $visits = $doctor->visits()->with('mr')->latest()->paginate(15);

        return view('mr.doctors.visits', compact('doctor', 'visits'));
    }

    public function orders(Doctor $doctor)
    {
        $this->authorizeAccess($doctor);

        $orders = $doctor->orders()->with('mr')->latest()->paginate(15);

        return view('mr.doctors.orders', compact('doctor', 'orders'));
    }

    // AJAX methods for cascading dropdowns
    public function getDistricts($stateId)
    {
        return District::where('state_id', $stateId)->where('is_active', true)->get();
    }

    public function getCities($districtId)
    {
        return City::where('district_id', $districtId)->where('is_active', true)->get();
    }

    public function getAreas($cityId)
    {
        return Area::where('city_id', $cityId)->where('is_active', true)->get();
    }

    private function authorizeAccess(Doctor $doctor)
    {
        if ($doctor->created_by !== auth()->id() && $doctor->assigned_mr_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
