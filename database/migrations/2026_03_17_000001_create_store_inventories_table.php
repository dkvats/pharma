<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_inventories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('store_id')
                  ->constrained('users');

            $table->foreignId('product_batch_id')
                  ->constrained('product_batches')
                  ->cascadeOnDelete();

            $table->integer('quantity')->default(0);

            $table->timestamps();

            // Prevent duplicate allocations for same store+batch pair
            $table->unique(['store_id', 'product_batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_inventories');
    }
};
