<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUserCodes extends Command
{
    protected $signature = 'users:check-codes {id}';
    protected $description = 'Check user unique_code and code values';

    public function handle(): void
    {
        $user = User::find($this->argument('id'));
        
        if (!$user) {
            $this->error('User not found');
            return;
        }
        
        $this->info('User ID: ' . $user->id);
        $this->info('Name: ' . $user->name);
        $this->info('Email: ' . $user->email);
        $this->info('unique_code: [' . $user->unique_code . ']');
        $this->info('code: [' . ($user->code ?? 'NULL') . ']');
        $this->info('Roles: ' . $user->roles->pluck('name')->join(', '));
    }
}
