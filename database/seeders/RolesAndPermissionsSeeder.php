<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management
            'manage_users',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Product Management
            'manage_products',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            
            // Order Management
            'manage_orders',
            'view_all_orders',
            'approve_orders',
            'reject_orders',
            'deliver_orders',
            
            // Own Orders
            'place_order',
            'view_own_orders',
            
            // Rewards
            'manage_rewards',
            'view_rewards',
            
            // Reports
            'view_reports',
            'view_admin_reports',
            'view_doctor_reports',
            'view_store_reports',
            
            // Spin
            'spin_wheel',
            'view_targets',
            
            // Super Admin - CMS & System
            'manage_system_settings',
            'manage_modules',
            'manage_cms_pages',
            'manage_ui_settings',
            'manage_dashboard_widgets',
            'manage_notification_templates',
            'manage_feature_flags',
            'manage_admins',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Super Admin Role - Full System Access
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin Role
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Sub Admin Role
        $subAdminRole = Role::create(['name' => 'Sub Admin']);
        $subAdminRole->givePermissionTo([
            'view_users', 'create_users', 'edit_users',
            'view_products', 'create_products', 'edit_products',
            'view_all_orders', 'approve_orders', 'reject_orders', 'deliver_orders',
            'view_rewards',
            'view_reports',
        ]);

        // Doctor Role
        $doctorRole = Role::create(['name' => 'Doctor']);
        $doctorRole->givePermissionTo([
            'place_order',
            'view_own_orders',
            'spin_wheel',
            'view_targets',
            'view_doctor_reports',
        ]);

        // Store Role
        $storeRole = Role::create(['name' => 'Store']);
        $storeRole->givePermissionTo([
            'place_order',
            'view_own_orders',
            'view_store_reports',
        ]);

        // End User Role
        $endUserRole = Role::create(['name' => 'End User']);
        $endUserRole->givePermissionTo([
            'place_order',
            'view_own_orders',
        ]);

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@pharma.com',
            'password' => Hash::make('password'),
            'code' => 'ADM-' . strtoupper(Str::random(6)),
            'status' => 'active',
            'phone' => '1234567890',
            'address' => 'Admin Office',
        ]);
        $admin->assignRole('Admin');

        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@pharma.com',
            'password' => Hash::make('password'),
            'code' => 'SUP-' . strtoupper(Str::random(6)),
            'status' => 'active',
            'phone' => '1234567800',
            'address' => 'System Headquarters',
        ]);
        $superAdmin->assignRole('Super Admin');

        // Create Sub Admin User
        $subAdmin = User::create([
            'name' => 'Sub Admin User',
            'email' => 'subadmin@pharma.com',
            'password' => Hash::make('password'),
            'code' => 'SAD-' . strtoupper(Str::random(6)),
            'status' => 'active',
            'phone' => '1234567891',
            'address' => 'Sub Admin Office',
        ]);
        $subAdmin->assignRole('Sub Admin');

        // Create Doctor User
        $doctor = User::create([
            'name' => 'Doctor User',
            'email' => 'doctor@pharma.com',
            'password' => Hash::make('password'),
            'code' => 'DOC-' . strtoupper(Str::random(6)),
            'status' => 'active',
            'phone' => '1234567892',
            'address' => 'Doctor Clinic',
        ]);
        $doctor->assignRole('Doctor');

        // Create Store User
        $store = User::create([
            'name' => 'Store User',
            'email' => 'store@pharma.com',
            'password' => Hash::make('password'),
            'code' => 'STR-' . strtoupper(Str::random(6)),
            'status' => 'active',
            'phone' => '1234567893',
            'address' => 'Store Location',
        ]);
        $store->assignRole('Store');

        // Create End User
        $endUser = User::create([
            'name' => 'End User',
            'email' => 'user@pharma.com',
            'password' => Hash::make('password'),
            'code' => null,
            'status' => 'active',
            'phone' => '1234567894',
            'address' => 'User Address',
        ]);
        $endUser->assignRole('End User');

        $this->command->info('Roles, permissions, and default users created successfully!');
        $this->command->info('');
        $this->command->info('Default Login Credentials:');
        $this->command->info('Super Admin: superadmin@pharma.com / password');
        $this->command->info('Admin: admin@pharma.com / password');
        $this->command->info('Sub Admin: subadmin@pharma.com / password');
        $this->command->info('Doctor: doctor@pharma.com / password');
        $this->command->info('Store: store@pharma.com / password');
        $this->command->info('End User: user@pharma.com / password');
    }
}
