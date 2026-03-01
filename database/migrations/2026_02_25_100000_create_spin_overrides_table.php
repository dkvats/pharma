<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spin_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->boolean('is_used')->default(false);
            $table->timestamps();
            
            // Index for quick lookup of active overrides
            $table->index(['doctor_id', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_overrides');
    }
};
