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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('batch_number');
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date');
            $table->integer('quantity')->default(0);
            $table->decimal('mrp', 10, 2)->default(0);
            $table->timestamps();

            $table->index(['product_id', 'expiry_date']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
