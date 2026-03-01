<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 6)->index();
            $table->string('post_office');
            $table->string('state');
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
            
            // Composite index for faster lookups
            $table->index(['pincode', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincodes');
    }
};
