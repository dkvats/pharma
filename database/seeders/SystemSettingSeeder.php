<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Pharma ERP', 'type' => 'string', 'description' => 'Website name'],
            ['key' => 'site_tagline', 'value' => 'Complete Pharmacy Management System', 'type' => 'string', 'description' => 'Website tagline'],
            ['key' => 'site_email', 'value' => 'info@pharma.com', 'type' => 'string', 'description' => 'Website contact email'],
            ['key' => 'site_phone', 'value' => '+1-234-567-8900', 'type' => 'string', 'description' => 'Website contact phone'],
            
            ['key' => 'spin_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Spin & Win feature'],
            ['key' => 'offers_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Offers feature'],
            ['key' => 'mr_module_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable MR Module'],
            ['key' => 'grand_draw_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Grand Draw feature'],
            ['key' => 'homepage_cms_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Homepage CMS'],
            
            ['key' => 'order_auto_approve', 'value' => '0', 'type' => 'boolean', 'description' => 'Auto-approve orders without prescription'],
            ['key' => 'doctor_target_quantity', 'value' => '30', 'type' => 'integer', 'description' => 'Monthly target quantity for doctors'],
            ['key' => 'leaderboard_refresh_interval', 'value' => '300', 'type' => 'integer', 'description' => 'Leaderboard refresh interval in seconds'],
            
            ['key' => 'notification_email_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable email notifications'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'description' => 'Enable maintenance mode'],
            
            ['key' => 'default_currency', 'value' => 'USD', 'type' => 'string', 'description' => 'Default currency'],
            ['key' => 'default_timezone', 'value' => 'UTC', 'type' => 'string', 'description' => 'Default timezone'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('System settings seeded successfully.');
    }
}
