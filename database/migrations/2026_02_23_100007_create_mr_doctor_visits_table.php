<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_doctor_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('mr_doctors')->onDelete('cascade');
            $table->foreignId('mr_id')->constrained('users')->onDelete('cascade');
            
            // Visit details
            $table->date('visit_date');
            $table->time('visit_time')->nullable();
            $table->text('remarks')->nullable();
            $table->text('products_discussed')->nullable();
            $table->date('next_visit_date')->nullable();
            
            // Photo upload
            $table->string('photo_path')->nullable();
            
            // Visit status
            $table->enum('status', ['planned', 'completed', 'cancelled', 'rescheduled'])->default('completed');
            
            $table->timestamps();
            
            // Indexes
            $table->index('doctor_id');
            $table->index('mr_id');
            $table->index('visit_date');
            $table->index(['mr_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_doctor_visits');
    }
};
