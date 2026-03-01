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
        Schema::table('grand_draw_winners', function (Blueprint $table) {
            // Prevent same doctor winning multiple times in same year
            $table->unique(['doctor_id', 'year'], 'grand_draw_winner_doctor_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grand_draw_winners', function (Blueprint $table) {
            $table->dropUnique('grand_draw_winner_doctor_year_unique');
        });
    }
};
