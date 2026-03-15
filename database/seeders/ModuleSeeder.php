<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['module_name' => 'Spin & Win', 'slug' => 'spin', 'status' => 'active', 'description' => 'Spin and win rewards system for doctors', 'icon' => 'fas fa-dharmachakra', 'sort_order' => 1],
            ['module_name' => 'Offers', 'slug' => 'offers', 'status' => 'active', 'description' => 'Offers and discounts management', 'icon' => 'fas fa-tags', 'sort_order' => 2],
            ['module_name' => 'MR Module', 'slug' => 'mr', 'status' => 'active', 'description' => 'Medical Representative management module', 'icon' => 'fas fa-user-tie', 'sort_order' => 3],
            ['module_name' => 'Orders', 'slug' => 'orders', 'status' => 'active', 'description' => 'Order management system', 'icon' => 'fas fa-shopping-cart', 'sort_order' => 4],
            ['module_name' => 'Products', 'slug' => 'products', 'status' => 'active', 'description' => 'Product catalog management', 'icon' => 'fas fa-pills', 'sort_order' => 5],
            ['module_name' => 'Doctors', 'slug' => 'doctors', 'status' => 'active', 'description' => 'Doctor management and referral system', 'icon' => 'fas fa-user-md', 'sort_order' => 6],
            ['module_name' => 'Stores', 'slug' => 'stores', 'status' => 'active', 'description' => 'Store management and inventory', 'icon' => 'fas fa-store', 'sort_order' => 7],
            ['module_name' => 'Grand Draw', 'slug' => 'grand-draw', 'status' => 'active', 'description' => 'Grand draw and lottery system', 'icon' => 'fas fa-gift', 'sort_order' => 8],
            ['module_name' => 'Homepage CMS', 'slug' => 'homepage-cms', 'status' => 'active', 'description' => 'Homepage content management', 'icon' => 'fas fa-home', 'sort_order' => 9],
            ['module_name' => 'Reports', 'slug' => 'reports', 'status' => 'active', 'description' => 'Reports and analytics', 'icon' => 'fas fa-chart-bar', 'sort_order' => 10],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['slug' => $module['slug']],
                array_merge($module, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Modules seeded successfully.');
    }
}
