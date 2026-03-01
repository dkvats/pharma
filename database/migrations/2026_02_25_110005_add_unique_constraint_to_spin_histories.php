<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spin_histories', function (Blueprint $table) {
            // Add month column for unique constraint
            $table->string('month', 7)->nullable()->after('spin_date'); // Format: YYYY-MM
            
            // Add unique constraint to prevent duplicate spins per month
            $table->unique(['doctor_id', 'month'], 'spin_histories_doctor_month_unique');
        });
    }

    public function down(): void
    {
        Schema::table('spin_histories', function (Blueprint $table) {
            $table->dropUnique('spin_histories_doctor_month_unique');
            $table->dropColumn('month');
        });
    }
};
