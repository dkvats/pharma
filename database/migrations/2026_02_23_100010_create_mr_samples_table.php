<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('mr_doctors')->onDelete('cascade');
            $table->foreignId('mr_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Sample details
            $table->integer('quantity');
            $table->date('given_date');
            $table->text('remarks')->nullable();
            
            // Batch info
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('doctor_id');
            $table->index('mr_id');
            $table->index('product_id');
            $table->index('given_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_samples');
    }
};
