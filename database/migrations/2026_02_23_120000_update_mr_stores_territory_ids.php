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
        Schema::table('mr_stores', function (Blueprint $table) {
            // Add ID-based territory fields (same as mr_doctors)
            $table->foreignId('state_id')->nullable()->after('pincode')->constrained('mr_states')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->after('state_id')->constrained('mr_districts')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('district_id')->constrained('mr_cities')->onDelete('set null');
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained('mr_areas')->onDelete('set null');
            
            // Drop old text-based fields (after migrating data if needed)
            // For now, keep them for backward compatibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mr_stores', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['area_id']);
            
            $table->dropColumn(['state_id', 'district_id', 'city_id', 'area_id']);
        });
    }
};
