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
        Schema::create('mr_stores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mr_id')->constrained('users');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('store_name');
            $table->string('owner_name');
            $table->string('store_code')->unique();

            $table->string('phone');
            $table->string('email')->nullable();

            $table->string('address');

            // PINCODE SYSTEM
            $table->string('pincode', 6);
            $table->string('state');
            $table->string('district');
            $table->string('city');
            $table->string('area')->nullable();

            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'inactive'])->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mr_stores');
    }
};
