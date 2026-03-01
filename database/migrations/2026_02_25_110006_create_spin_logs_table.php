<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->string('action'); // 'attempt', 'success', 'failure', 'blocked'
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('result')->nullable(); // 'win', 'loss', 'error'
            $table->text('details')->nullable();
            $table->timestamps();
            
            // Indexes for fraud detection queries
            $table->index(['doctor_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_logs');
    }
};
