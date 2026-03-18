<?php

namespace App\Http\Controllers;

use App\Models\MR\Doctor;
use App\Models\MR\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PublicRegistrationController extends Controller
{
    public function showDoctorForm()
    {
        return view('auth.register-doctor');
    }

    public function showStoreForm()
    {
        return view('auth.register-store');
    }

    public function registerDoctor(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'regex:/^\d{10}$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'clinic_name' => ['nullable', 'string', 'max:255'],
            'license_no' => ['nullable', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:1000'],
            'pincode' => ['required', 'regex:/^\d{6}$/'],
            'state' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $doctorCode = $this->generateUniqueCode('DOC-', Doctor::class, 'doctor_code');
        $defaultMrId = $this->defaultMrId();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['mobile'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
            'role' => 'doctor',
            'created_by' => $defaultMrId,
            'unique_code' => $doctorCode,
        ]);

        $user->syncRoles(['Doctor']);

        Doctor::create([
            'user_id' => $user->id,
            'doctor_code' => $doctorCode,
            'name' => $validated['name'],
            'clinic_name' => $validated['clinic_name'] ?? null,
            'license_no' => $validated['license_no'] ?? null,
            'address' => $validated['address'],
            'pincode' => $validated['pincode'],
            'state' => $validated['state'],
            'district' => $validated['district'],
            'city' => $validated['city'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'created_by' => $defaultMrId,
            'assigned_mr_id' => null,
            'is_active' => false,
            'status' => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', 'Your request has been submitted and is pending admin approval.');
    }

    public function registerStore(Request $request)
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^\d{10}$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'address' => ['required', 'string', 'max:1000'],
            'pincode' => ['required', 'regex:/^\d{6}$/'],
            'state' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $storeCode = $this->generateUniqueCode('STR-', Store::class, 'store_code');
        $defaultMrId = $this->defaultMrId();

        $user = User::create([
            'name' => $validated['owner_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'status' => 'pending',
            'role' => 'store',
            'created_by' => $defaultMrId,
            'unique_code' => $storeCode,
        ]);

        $user->syncRoles(['Store']);

        Store::create([
            'mr_id' => $defaultMrId,
            'assigned_mr_id' => null,
            'user_id' => $user->id,
            'store_name' => $validated['store_name'],
            'owner_name' => $validated['owner_name'],
            'store_code' => $storeCode,
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'pincode' => $validated['pincode'],
            'state' => $validated['state'],
            'district' => $validated['district'],
            'city' => $validated['city'],
            'status' => 'pending',
        ]);

        return redirect()->route('home')
            ->with('success', 'Your request has been submitted and is pending admin approval.');
    }

    private function defaultMrId(): int
    {
        $mr = User::role('MR')->orderBy('id')->first();
        if ($mr) {
            return $mr->id;
        }

        $admin = User::role('Admin')->orderBy('id')->first();
        if ($admin) {
            return $admin->id;
        }

        return User::query()->orderBy('id')->value('id') ?? 1;
    }

    private function generateUniqueCode(string $prefix, string $modelClass, string $column): string
    {
        do {
            $code = $prefix . strtoupper(Str::random(8));
        } while ($modelClass::where($column, $code)->exists());

        return $code;
    }
}
