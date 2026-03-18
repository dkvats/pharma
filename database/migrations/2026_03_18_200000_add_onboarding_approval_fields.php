<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('status');
                $table->index('role');
            }
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
        }

        Schema::table('mr_doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('mr_doctors', 'license_no')) {
                $table->string('license_no')->nullable()->after('clinic_name');
            }
            if (!Schema::hasColumn('mr_doctors', 'pincode')) {
                $table->string('pincode', 6)->nullable()->after('address');
            }
            if (!Schema::hasColumn('mr_doctors', 'state')) {
                $table->string('state')->nullable()->after('state_id');
            }
            if (!Schema::hasColumn('mr_doctors', 'district')) {
                $table->string('district')->nullable()->after('district_id');
            }
            if (!Schema::hasColumn('mr_doctors', 'city')) {
                $table->string('city')->nullable()->after('city_id');
            }
            if (!Schema::hasColumn('mr_doctors', 'assigned_mr_id')) {
                $table->foreignId('assigned_mr_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('mr_stores', function (Blueprint $table) {
            if (!Schema::hasColumn('mr_stores', 'assigned_mr_id')) {
                $table->foreignId('assigned_mr_id')->nullable()->after('mr_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mr_stores', function (Blueprint $table) {
            if (Schema::hasColumn('mr_stores', 'assigned_mr_id')) {
                $table->dropForeign(['assigned_mr_id']);
                $table->dropColumn('assigned_mr_id');
            }
        });

        Schema::table('mr_doctors', function (Blueprint $table) {
            if (Schema::hasColumn('mr_doctors', 'assigned_mr_id')) {
                $table->dropForeign(['assigned_mr_id']);
                $table->dropColumn('assigned_mr_id');
            }
            if (Schema::hasColumn('mr_doctors', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('mr_doctors', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('mr_doctors', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('mr_doctors', 'pincode')) {
                $table->dropColumn('pincode');
            }
            if (Schema::hasColumn('mr_doctors', 'license_no')) {
                $table->dropColumn('license_no');
            }
        });

        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            }
        });
    }
};
