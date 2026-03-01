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
        Schema::table('doctor_targets', function (Blueprint $table) {
            $table->unique(['doctor_id', 'year', 'month'], 'doctor_month_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_targets', function (Blueprint $table) {
            $table->dropUnique('doctor_month_unique');
        });
    }
};
