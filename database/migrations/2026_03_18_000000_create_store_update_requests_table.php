<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table stores pending update requests from MRs.
     * When an MR edits a store, changes go here instead of directly to mr_stores.
     * Admin can then approve or reject these changes.
     */
    public function up(): void
    {
        Schema::create('store_update_requests', function (Blueprint $table) {
            $table->id();

            // Link to the store being updated
            $table->foreignId('store_id')
                ->constrained('mr_stores')
                ->onDelete('cascade');

            // Who requested the update
            $table->foreignId('requested_by')
                ->constrained('users')
                ->onDelete('cascade');

            // Role of requester (for audit trail)
            $table->enum('requested_role', ['admin', 'mr'])->default('mr');

            // ════════════════════════════════════════════════════════════════
            // STORE DATA SNAPSHOT (Updated fields from mr_stores)
            // ════════════════════════════════════════════════════════════════
            
            // Basic Store Details
            $table->string('store_name')->nullable();
            $table->string('owner_name')->nullable();
            
            // Contact Information
            $table->string('phone')->nullable();
            $table->string('alt_phone')->nullable();
            $table->string('email')->nullable();
            
            // Owner Identity
            $table->string('aadhaar')->nullable();
            $table->string('owner_photo')->nullable();
            
            // Store Media
            $table->string('store_photo')->nullable();
            
            // Address & Location
            $table->string('address')->nullable();
            $table->string('pincode')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->string('state')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            
            // Business Details
            $table->string('gst_no')->nullable();
            $table->string('drug_license_no')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('pan_no')->nullable();
            
            // Store Type
            $table->enum('store_type', ['retail', 'distributor', 'clinic', 'pharmacy'])->nullable();
            
            // Financial Settings
            $table->decimal('default_discount', 5, 2)->nullable();
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->string('payment_terms')->nullable();

            // ════════════════════════════════════════════════════════════════
            // APPROVAL WORKFLOW
            // ════════════════════════════════════════════════════════════════
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Admin who approved/rejected
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            $table->timestamp('approved_at')->nullable();
            
            // Rejection reason (if rejected)
            $table->text('rejection_reason')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['store_id', 'status']);
            $table->index(['requested_by', 'status']);
            $table->index(['status']);
            $table->index('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_update_requests');
    }
};
