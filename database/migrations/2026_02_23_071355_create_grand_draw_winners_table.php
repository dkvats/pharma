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
        Schema::create('grand_draw_winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->year('year');
            $table->timestamp('draw_date');
            $table->foreignId('drawn_by')->constrained('users')->onDelete('cascade');
            $table->integer('total_eligible_doctors');
            $table->timestamps();
            
            // Ensure only one winner per year
            $table->unique('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grand_draw_winners');
    }
};
