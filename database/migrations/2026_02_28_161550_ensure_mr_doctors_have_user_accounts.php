<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MR\Doctor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create user accounts for all MR doctors without user_id.
     */
    public function up(): void
    {
        // Get all MR doctors without linked user accounts
        $doctorsWithoutUsers = Doctor::whereNull('user_id')->get();
        
        foreach ($doctorsWithoutUsers as $doctor) {
            // Generate system email if missing
            $email = $this->generateSystemEmail($doctor);
            
            // Generate username from name
            $username = $this->generateUsername($doctor->name);
            
            // Generate password
            $password = Str::random(12);
            
            // Create user account
            $user = User::create([
                'name' => $doctor->name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'phone' => $doctor->mobile ?? $doctor->phone,
                'status' => $doctor->is_active ? 'active' : 'inactive',
                'role' => 'doctor',
                'created_by' => $doctor->created_by,
            ]);
            
            // Assign Doctor role using spatie
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Doctor');
            }
            
            // Link doctor to user
            $doctor->update(['user_id' => $user->id]);
            
            // Log the migration
            DB::table('migration_logs')->insert([
                'migration' => 'ensure_mr_doctors_have_user_accounts',
                'doctor_id' => $doctor->id,
                'user_id' => $user->id,
                'email' => $email,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed
        // User accounts created should not be deleted
    }
    
    /**
     * Generate system email for doctor
     */
    private function generateSystemEmail(Doctor $doctor): string
    {
        // Use existing email if available
        if (!empty($doctor->email)) {
            return $doctor->email;
        }
        
        // Generate from doctor_code
        $baseEmail = strtolower(str_replace(['DOC-', ' '], ['', ''], $doctor->doctor_code));
        $email = $baseEmail . '@pharma.local';
        
        // Ensure unique
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@pharma.local';
            $counter++;
        }
        
        return $email;
    }
    
    /**
     * Generate unique username from doctor name
     */
    private function generateUsername(string $name): string
    {
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $username = $baseUsername;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }
        
        return $username;
    }
};
