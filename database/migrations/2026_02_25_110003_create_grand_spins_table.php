<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grand_spins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->foreignId('reward_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            // Index for quick lookup
            $table->index(['doctor_id', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grand_spins');
    }
};
