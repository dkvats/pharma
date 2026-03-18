<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_product_promotions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mr_id')
                  ->constrained('users');

            $table->foreignId('doctor_id')
                  ->constrained('users');

            $table->foreignId('product_id')
                  ->constrained();

            $table->foreignId('visit_id')
                  ->nullable()
                  ->constrained('mr_doctor_visits')
                  ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_product_promotions');
    }
};
