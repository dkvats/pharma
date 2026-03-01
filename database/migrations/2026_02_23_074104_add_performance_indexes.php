<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Orders table indexes - critical for reporting queries
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'orders_status_index');
            $table->index('doctor_id', 'orders_doctor_id_index');
            $table->index('created_at', 'orders_created_at_index');
            $table->index(['doctor_id', 'created_at'], 'orders_doctor_created_index');
            $table->index(['status', 'created_at'], 'orders_status_created_index');
        });

        // Spin histories indexes - for Grand Draw eligibility queries
        Schema::table('spin_histories', function (Blueprint $table) {
            $table->index('doctor_id', 'spin_histories_doctor_id_index');
            $table->index('created_at', 'spin_histories_created_at_index');
            $table->index(['doctor_id', 'created_at'], 'spin_histories_doctor_created_index');
        });

        // Doctor targets indexes - for target progress queries
        Schema::table('doctor_targets', function (Blueprint $table) {
            $table->index('doctor_id', 'doctor_targets_doctor_id_index');
            $table->index('year', 'doctor_targets_year_index');
            $table->index('month', 'doctor_targets_month_index');
        });

        // Activity logs indexes - for cleanup and reporting
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('created_at', 'activity_logs_created_at_index');
            $table->index(['action', 'created_at'], 'activity_logs_action_created_index');
        });

        // Grand draw winners indexes
        Schema::table('grand_draw_winners', function (Blueprint $table) {
            $table->index('year', 'grand_draw_winners_year_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_doctor_id_index');
            $table->dropIndex('orders_created_at_index');
            $table->dropIndex('orders_doctor_created_index');
            $table->dropIndex('orders_status_created_index');
        });

        Schema::table('spin_histories', function (Blueprint $table) {
            $table->dropIndex('spin_histories_doctor_id_index');
            $table->dropIndex('spin_histories_created_at_index');
            $table->dropIndex('spin_histories_doctor_created_index');
        });

        Schema::table('doctor_targets', function (Blueprint $table) {
            $table->dropIndex('doctor_targets_doctor_id_index');
            $table->dropIndex('doctor_targets_year_index');
            $table->dropIndex('doctor_targets_month_index');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_created_at_index');
            $table->dropIndex('activity_logs_action_created_index');
        });

        Schema::table('grand_draw_winners', function (Blueprint $table) {
            $table->dropIndex('grand_draw_winners_year_index');
        });
    }
};
