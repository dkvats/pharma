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
        Schema::create('doctor_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users');
            $table->year('year');
            $table->tinyInteger('month');
            $table->integer('target_quantity')->default(30);
            $table->integer('achieved_quantity')->default(0);
            $table->boolean('target_completed')->default(false);
            $table->boolean('spin_eligible')->default(false);
            $table->timestamp('spin_completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_targets');
    }
};
