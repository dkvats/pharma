<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MRRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create MR Permissions (idempotent - won't duplicate)
        $mrPermissions = [
            // MR Dashboard
            'mr_dashboard',
            
            // Doctor Management
            'mr_manage_doctors',
            'mr_view_doctors',
            'mr_create_doctors',
            'mr_edit_doctors',
            
            // Visit Management (DCR)
            'mr_manage_visits',
            'mr_view_visits',
            'mr_create_visits',
            
            // Order Management
            'mr_manage_orders',
            'mr_view_orders',
            'mr_create_orders',
            
            // Sample Management
            'mr_manage_samples',
            'mr_view_samples',
            'mr_create_samples',
            
            // Reports
            'mr_view_reports',
        ];

        foreach ($mrPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create MR Role (idempotent - won't duplicate)
        $mrRole = Role::firstOrCreate(['name' => 'MR', 'guard_name' => 'web']);
        $mrRole->givePermissionTo($mrPermissions);

        // Create Sample MR User
        $mrUser = User::firstOrCreate(
            ['email' => 'mr@pharma.com'],
            [
                'name' => 'Medical Representative',
                'password' => Hash::make('password'),
                'code' => 'MR-' . strtoupper(Str::random(6)),
                'status' => 'active',
                'phone' => '1234567895',
                'address' => 'MR Territory',
            ]
        );
        $mrUser->assignRole('MR');

        $this->command->info('MR Role and permissions created successfully!');
        $this->command->info('MR Login: mr@pharma.com / password');
    }
}
