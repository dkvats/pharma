<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateDoctorCodes extends Command
{
    protected $signature = 'doctors:generate-codes';
    protected $description = 'Generate unique codes for all doctors missing them';

    public function handle(): void
    {
        $doctors = User::whereNull('unique_code')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Doctor');
            })
            ->get();

        $this->info('Found ' . $doctors->count() . ' doctors without unique codes.');

        foreach ($doctors as $doctor) {
            $code = 'DOC-' . strtoupper(Str::random(6));
            
            // Ensure uniqueness
            while (User::where('unique_code', $code)->exists()) {
                $code = 'DOC-' . strtoupper(Str::random(6));
            }
            
            $doctor->unique_code = $code;
            $doctor->save();
            
            $this->info('Generated code for Dr. ' . $doctor->name . ': ' . $code);
        }

        $this->info('Done! All doctors now have unique codes.');
    }
}
