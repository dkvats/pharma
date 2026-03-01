<?php

namespace App\Console\Commands;

use App\Models\MR\Doctor;
use App\Models\User;
use Illuminate\Console\Command;

class SyncDoctorCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doctors:sync-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync mr_doctors.doctor_code to users.unique_code for referral matching';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing doctor codes...');
        
        $synced = 0;
        $skipped = 0;
        
        // Get all MR doctors with linked user accounts
        $doctors = Doctor::whereNotNull('user_id')->get();
        
        foreach ($doctors as $doctor) {
            $user = User::find($doctor->user_id);
            
            if (!$user) {
                $this->warn("User not found for doctor ID: {$doctor->id}");
                $skipped++;
                continue;
            }
            
            // Sync doctor_code to unique_code if different
            if ($user->unique_code !== $doctor->doctor_code) {
                $oldCode = $user->unique_code;
                $user->update(['unique_code' => $doctor->doctor_code]);
                $this->info("Updated: {$user->name} | Old: {$oldCode} | New: {$doctor->doctor_code}");
                $synced++;
            } else {
                $skipped++;
            }
        }
        
        $this->newLine();
        $this->info("Sync complete!");
        $this->info("Synced: {$synced}");
        $this->info("Skipped (already matching): {$skipped}");
        
        return Command::SUCCESS;
    }
}
