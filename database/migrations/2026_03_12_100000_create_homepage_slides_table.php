<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('image');                             // storage path (relative to public disk)
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default('active');        // active | inactive
            $table->timestamps();
        });

        // Seed one default slide so the slider is never empty after migration
        DB::table('homepage_slides')->insert([
            'title'       => 'Premium Veterinary Medicines',
            'subtitle'    => 'Trusted pharmaceutical solutions for cattle, buffalo, goat, and poultry across India.',
            'image'       => '',                                // blank — slider handles empty gracefully
            'button_text' => 'Explore Products',
            'button_link' => '#products',
            'sort_order'  => 1,
            'status'      => 'active',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_slides');
    }
};
