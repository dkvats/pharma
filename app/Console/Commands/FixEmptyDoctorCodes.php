<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixEmptyDoctorCodes extends Command
{
    protected $signature = 'doctors:fix-empty-codes';
    protected $description = 'Fix doctors with empty or null unique_code';

    public function handle(): void
    {
        // Find doctors with empty or null unique_code
        $doctors = User::whereHas('roles', function ($q) {
                $q->where('name', 'Doctor');
            })
            ->where(function ($query) {
                $query->whereNull('unique_code')
                      ->orWhere('unique_code', '');
            })
            ->get();

        $this->info('Found ' . $doctors->count() . ' doctors with empty codes.');

        foreach ($doctors as $doctor) {
            do {
                $code = 'DOC-' . strtoupper(Str::random(6));
            } while (User::where('unique_code', $code)->exists());
            
            $doctor->unique_code = $code;
            $doctor->save();
            
            $this->info('Fixed: Dr. ' . $doctor->name . ' (ID: ' . $doctor->id . ') -> ' . $code);
        }

        $this->info('Done! All doctors now have valid unique codes.');
    }
}
