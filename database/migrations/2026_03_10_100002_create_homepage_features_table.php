<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_features', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->default('star'); // Font Awesome icon name without 'fa-'
            $table->string('icon_color')->default('blue'); // blue, yellow, green, purple, red, indigo, etc.
            $table->string('status')->default('active'); // active | inactive
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default features matching current hardcoded values
        $features = [
            ['title' => 'Doctor Rewards', 'description' => 'Track referrals, earn rewards, and climb the leaderboard — all in one dashboard for doctors.', 'icon' => 'user-md', 'icon_color' => 'blue', 'sort_order' => 1],
            ['title' => 'Spin & Win', 'description' => 'Doctors earn spin chances through sales targets. Win exciting rewards through our animated spin wheel.', 'icon' => 'sync-alt', 'icon_color' => 'yellow', 'sort_order' => 2],
            ['title' => 'Store Inventory', 'description' => 'Real-time inventory tracking for pharmacy stores with automated low-stock alerts.', 'icon' => 'boxes', 'icon_color' => 'green', 'sort_order' => 3],
            ['title' => 'ERP Reporting', 'description' => 'Powerful sales reports, territory management, and doctor target tracking for admins and MRs.', 'icon' => 'chart-line', 'icon_color' => 'purple', 'sort_order' => 4],
            ['title' => 'Prescription Management', 'description' => 'Upload and verify prescriptions online. Secure, fast, and paper-free order management.', 'icon' => 'file-prescription', 'icon_color' => 'red', 'sort_order' => 5],
            ['title' => 'Monthly Leaderboard', 'description' => 'Competitive monthly ranking system for doctors based on product referrals and delivery performance.', 'icon' => 'trophy', 'icon_color' => 'indigo', 'sort_order' => 6],
        ];

        foreach ($features as $f) {
            DB::table('homepage_features')->insert(array_merge($f, [
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_features');
    }
};
