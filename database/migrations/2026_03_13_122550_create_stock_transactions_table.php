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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('product_name');
            $table->enum('transaction_type', ['purchase', 'sale', 'adjustment']);
            $table->integer('quantity');
            $table->integer('opening_balance');
            $table->integer('closing_balance');
            $table->string('reference_type')->nullable(); // e.g., 'order', 'sale', 'adjustment'
            $table->unsignedBigInteger('reference_id')->nullable(); // polymorphic reference
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['store_id', 'product_id']);
            $table->index(['store_id', 'created_at']);
            $table->index(['transaction_type']);
            $table->index(['created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
