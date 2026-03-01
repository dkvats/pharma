<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grand_spin_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['cash', 'product', 'voucher', 'other'])->default('other');
            $table->decimal('value', 10, 2)->default(0);
            $table->integer('stock')->nullable(); // null = unlimited
            $table->string('image')->nullable();
            $table->integer('probability')->default(0); // For weighted selection
            $table->boolean('is_active')->default(true);
            $table->boolean('force_equal_distribution')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grand_spin_rewards');
    }
};
