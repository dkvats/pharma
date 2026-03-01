<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DebugDoctorCode extends Command
{
    protected $signature = 'debug:doctor-code {code?}';
    protected $description = 'Debug doctor code lookup';

    public function handle(): void
    {
        $doctor = User::role('Doctor')->first();
        
        $this->info('Doctor ID: ' . $doctor->id);
        $this->info('Code: [' . $doctor->unique_code . ']');
        $this->info('Code Length: ' . strlen($doctor->unique_code));
        $this->info('Lowercase: [' . strtolower($doctor->unique_code) . ']');
        
        // Test lookup
        $testCode = $this->argument('code') ?? $doctor->unique_code;
        $this->info('');
        $this->info('Testing lookup with: [' . $testCode . ']');
        
        $trimmed = trim($testCode);
        $lowercase = strtolower($trimmed);
        
        $found = User::whereRaw('LOWER(unique_code) = ?', [$lowercase])
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Doctor');
            })
            ->first();
        
        if ($found) {
            $this->info('✓ Doctor found: ID ' . $found->id);
        } else {
            $this->error('✗ Doctor NOT found');
        }
    }
}
