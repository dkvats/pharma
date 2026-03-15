<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. System Settings Table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default system settings
        DB::table('system_settings')->insert([
            ['key' => 'spin_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable/disable spin wheel system', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'doctor_target_quantity', 'value' => '30', 'type' => 'integer', 'description' => 'Monthly product target for doctors', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'leaderboard_refresh_interval', 'value' => '300', 'type' => 'integer', 'description' => 'Leaderboard cache duration in seconds', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'order_auto_approve', 'value' => '0', 'type' => 'boolean', 'description' => 'Auto-approve new orders', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'offers_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable/disable offers system', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'mr_module_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable/disable MR module', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'homepage_cms_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable/disable homepage CMS', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'grand_draw_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable/disable grand lucky draw', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'notification_email_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable email notifications', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'description' => 'Put site in maintenance mode', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Modules Table
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name')->unique();
            $table->string('slug')->unique();
            $table->string('status')->default('active'); // active, inactive
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default modules
        DB::table('modules')->insert([
            ['module_name' => 'Spin System', 'slug' => 'spin', 'status' => 'active', 'description' => 'Doctor spin wheel and rewards', 'icon' => 'fa-dharmachakra', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'Leaderboard', 'slug' => 'leaderboard', 'status' => 'active', 'description' => 'Doctor ranking and tiers', 'icon' => 'fa-trophy', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'Offers', 'slug' => 'offers', 'status' => 'active', 'description' => 'Discount offers management', 'icon' => 'fa-tags', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'MR Module', 'slug' => 'mr', 'status' => 'active', 'description' => 'Medical Representative management', 'icon' => 'fa-user-tie', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'Reports', 'slug' => 'reports', 'status' => 'active', 'description' => 'Analytics and reporting', 'icon' => 'fa-chart-bar', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'Homepage CMS', 'slug' => 'homepage-cms', 'status' => 'active', 'description' => 'Homepage content management', 'icon' => 'fa-home', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'Grand Draw', 'slug' => 'grand-draw', 'status' => 'active', 'description' => 'Grand lucky draw events', 'icon' => 'fa-gift', 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['module_name' => 'Territory', 'slug' => 'territory', 'status' => 'active', 'description' => 'Territory and location management', 'icon' => 'fa-map-marked-alt', 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. CMS Pages Table
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('status')->default('active'); // active, draft, archived
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        // Seed default CMS pages
        DB::table('cms_pages')->insert([
            ['slug' => 'about-us', 'title' => 'About Us', 'content' => '<h1>About Us</h1><p>Content goes here...</p>', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'terms', 'title' => 'Terms & Conditions', 'content' => '<h1>Terms & Conditions</h1><p>Content goes here...</p>', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'privacy-policy', 'title' => 'Privacy Policy', 'content' => '<h1>Privacy Policy</h1><p>Content goes here...</p>', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'faq', 'title' => 'FAQ', 'content' => '<h1>Frequently Asked Questions</h1><p>Content goes here...</p>', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'doctor-guidelines', 'title' => 'Doctor Guidelines', 'content' => '<h1>Doctor Guidelines</h1><p>Content goes here...</p>', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['slug' => 'store-guidelines', 'title' => 'Store Guidelines', 'content' => '<h1>Store Guidelines</h1><p>Content goes here...</p>', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. UI Settings Table
        Schema::create('ui_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, color, image, json
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default UI settings
        DB::table('ui_settings')->insert([
            ['key' => 'site_logo', 'value' => null, 'type' => 'image', 'description' => 'Main site logo', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_primary_color', 'value' => '#2563eb', 'type' => 'color', 'description' => 'Primary brand color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_secondary_color', 'value' => '#1e3a5f', 'type' => 'color', 'description' => 'Secondary brand color', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_text', 'value' => 'All rights reserved.', 'type' => 'text', 'description' => 'Footer copyright text', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'dashboard_theme', 'value' => 'light', 'type' => 'text', 'description' => 'Dashboard theme (light/dark)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'sidebar_style', 'value' => 'expanded', 'type' => 'text', 'description' => 'Sidebar style (expanded/collapsed)', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 5. Dashboard Widgets Table
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('widget_name');
            $table->string('widget_key')->unique();
            $table->string('role'); // Admin, Doctor, Store, MR, End User
            $table->string('status')->default('active');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        // Seed default dashboard widgets
        DB::table('dashboard_widgets')->insert([
            // Admin widgets
            ['widget_name' => 'Total Users', 'widget_key' => 'total_users', 'role' => 'Admin', 'status' => 'active', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Total Orders', 'widget_key' => 'total_orders', 'role' => 'Admin', 'status' => 'active', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Total Revenue', 'widget_key' => 'total_revenue', 'role' => 'Admin', 'status' => 'active', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Recent Activities', 'widget_key' => 'recent_activities', 'role' => 'Admin', 'status' => 'active', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            // Doctor widgets
            ['widget_name' => 'My Orders', 'widget_key' => 'my_orders', 'role' => 'Doctor', 'status' => 'active', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Target Progress', 'widget_key' => 'target_progress', 'role' => 'Doctor', 'status' => 'active', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Spin Eligibility', 'widget_key' => 'spin_eligibility', 'role' => 'Doctor', 'status' => 'active', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Rank Card', 'widget_key' => 'rank_card', 'role' => 'Doctor', 'status' => 'active', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            // Store widgets
            ['widget_name' => 'My Orders', 'widget_key' => 'store_orders', 'role' => 'Store', 'status' => 'active', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Stock Status', 'widget_key' => 'stock_status', 'role' => 'Store', 'status' => 'active', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            // MR widgets
            ['widget_name' => 'My Doctors', 'widget_key' => 'my_doctors', 'role' => 'MR', 'status' => 'active', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'My Stores', 'widget_key' => 'my_stores', 'role' => 'MR', 'status' => 'active', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['widget_name' => 'Today Visits', 'widget_key' => 'today_visits', 'role' => 'MR', 'status' => 'active', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. Notification Templates Table
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_key')->unique();
            $table->string('name');
            $table->string('subject');
            $table->text('body');
            $table->string('type')->default('email'); // email, sms, push, in_app
            $table->string('status')->default('active');
            $table->json('variables')->nullable(); // Available placeholder variables
            $table->timestamps();
        });

        // Seed default notification templates
        DB::table('notification_templates')->insert([
            ['template_key' => 'order_placed', 'name' => 'Order Placed', 'subject' => 'Your order has been placed', 'body' => 'Dear {user_name}, your order #{order_number} has been placed successfully.', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name', 'order_number', 'total_amount']), 'created_at' => now(), 'updated_at' => now()],
            ['template_key' => 'order_approved', 'name' => 'Order Approved', 'subject' => 'Your order has been approved', 'body' => 'Dear {user_name}, your order #{order_number} has been approved.', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name', 'order_number']), 'created_at' => now(), 'updated_at' => now()],
            ['template_key' => 'order_delivered', 'name' => 'Order Delivered', 'subject' => 'Your order has been delivered', 'body' => 'Dear {user_name}, your order #{order_number} has been delivered.', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name', 'order_number']), 'created_at' => now(), 'updated_at' => now()],
            ['template_key' => 'spin_reward_won', 'name' => 'Spin Reward Won', 'subject' => 'Congratulations! You won a reward', 'body' => 'Dear {user_name}, you have won {reward_name} from spin wheel!', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name', 'reward_name', 'reward_value']), 'created_at' => now(), 'updated_at' => now()],
            ['template_key' => 'target_achieved', 'name' => 'Target Achieved', 'subject' => 'Monthly target achieved!', 'body' => 'Dear {user_name}, congratulations! You have achieved your monthly target.', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name', 'target_quantity', 'achieved_quantity']), 'created_at' => now(), 'updated_at' => now()],
            ['template_key' => 'doctor_registered', 'name' => 'Doctor Registered', 'subject' => 'Welcome to our platform', 'body' => 'Dear {user_name}, your doctor account has been created successfully.', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name', 'login_email']), 'created_at' => now(), 'updated_at' => now()],
            ['template_key' => 'store_registered', 'name' => 'Store Registered', 'subject' => 'Welcome to our platform', 'body' => 'Dear {user_name}, your store account has been created successfully.', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name', 'login_email']), 'created_at' => now(), 'updated_at' => now()],
            ['template_key' => 'welcome_user', 'name' => 'Welcome User', 'subject' => 'Welcome to Pharma Distribution', 'body' => 'Dear {user_name}, welcome to our platform!', 'type' => 'email', 'status' => 'active', 'variables' => json_encode(['user_name']), 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7. Feature Flags Table
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('flag_key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(false);
            $table->json('rollout_percentage')->nullable(); // For gradual rollout
            $table->json('target_roles')->nullable(); // Which roles can access
            $table->timestamps();
        });

        // Seed default feature flags
        DB::table('feature_flags')->insert([
            ['flag_key' => 'new_spin_algorithm', 'name' => 'New Spin Algorithm', 'description' => 'Use improved probability algorithm for spin wheel', 'enabled' => false, 'created_at' => now(), 'updated_at' => now()],
            ['flag_key' => 'beta_rewards', 'name' => 'Beta Rewards', 'description' => 'Enable beta testing for new rewards', 'enabled' => false, 'created_at' => now(), 'updated_at' => now()],
            ['flag_key' => 'advanced_leaderboard', 'name' => 'Advanced Leaderboard', 'description' => 'Show advanced leaderboard metrics', 'enabled' => false, 'created_at' => now(), 'updated_at' => now()],
            ['flag_key' => 'real_time_notifications', 'name' => 'Real-time Notifications', 'description' => 'Enable real-time push notifications', 'enabled' => false, 'created_at' => now(), 'updated_at' => now()],
            ['flag_key' => 'mobile_app_api', 'name' => 'Mobile App API', 'description' => 'Enable mobile app API endpoints', 'enabled' => false, 'created_at' => now(), 'updated_at' => now()],
            ['flag_key' => 'multi_currency', 'name' => 'Multi-Currency Support', 'description' => 'Enable multiple currency support', 'enabled' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('ui_settings');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('system_settings');
    }
};
