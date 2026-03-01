<?php

namespace App\Console\Commands;

use App\Models\StoreSale;
use App\Models\User;
use Illuminate\Console\Command;

class DebugDoctorReferrals extends Command
{
    protected $signature = 'debug:doctor-referrals';
    protected $description = 'Debug doctor referral data';

    public function handle(): void
    {
        $this->info('=== DOCTORS ===');
        foreach (User::role('Doctor')->get() as $d) {
            $this->info("ID: {$d->id}, Name: {$d->name}, Code: [{$d->unique_code}]");
        }

        $this->info('');
        $this->info('=== STORE SALES (Latest 5) ===');
        foreach (StoreSale::latest()->take(5)->get() as $s) {
            $doctorId = $s->doctor_id ?? 'NULL';
            $this->info("ID: {$s->id}, doctor_id: {$doctorId}, sale_type: {$s->sale_type}, qty: {$s->quantity}");
        }
    }
}
