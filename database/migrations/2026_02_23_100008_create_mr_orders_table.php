<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('doctor_id')->constrained('mr_doctors')->onDelete('cascade');
            $table->foreignId('mr_id')->constrained('users')->onDelete('cascade');
            
            // Order details
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            
            // Timestamps
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('doctor_id');
            $table->index('mr_id');
            $table->index('status');
            $table->index('ordered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_orders');
    }
};
