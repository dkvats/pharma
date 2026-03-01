<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_doctors', function (Blueprint $table) {
            $table->id();
            $table->string('doctor_code')->unique();
            $table->string('name');
            $table->string('specialization')->nullable();
            $table->string('clinic_name')->nullable();
            $table->text('address')->nullable();
            
            // Location hierarchy
            $table->foreignId('area_id')->nullable()->constrained('mr_areas')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained('mr_cities')->onDelete('set null');
            $table->foreignId('district_id')->nullable()->constrained('mr_districts')->onDelete('set null');
            $table->foreignId('state_id')->nullable()->constrained('mr_states')->onDelete('set null');
            
            // Contact info
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // KYC details
            $table->string('aadhaar_no')->nullable();
            $table->string('pan_no')->nullable();
            
            // Bank details
            $table->string('bank_name')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('account_no')->nullable();
            
            // MR who created this doctor
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('created_by');
            $table->index('mobile');
            $table->index(['state_id', 'district_id', 'city_id', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_doctors');
    }
};
