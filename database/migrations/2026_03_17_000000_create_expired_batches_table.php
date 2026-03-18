<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expired_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_batch_id')
                  ->constrained('product_batches')
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained();

            $table->string('batch_number');

            $table->date('expiry_date');

            $table->integer('quantity');

            $table->enum('status', [
                'pending_return',
                'returned',
                'disposed',
            ])->default('pending_return');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expired_batches');
    }
};
