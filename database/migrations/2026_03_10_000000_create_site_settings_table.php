<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Pharma Distribution Network');
            $table->string('logo')->nullable();
            $table->string('hero_title')->default('Connecting Doctors, Stores & Patients');
            $table->text('hero_subtitle')->nullable()->default('A professional pharmaceutical distribution platform that streamlines ordering, referrals, and field operations.');
            $table->string('hero_image')->nullable();
            $table->string('about_title')->default('About Us');
            $table->text('about_description')->nullable()->default('Our platform connects medical representatives, doctors, and stores to streamline pharmaceutical distribution across India.');
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        // Insert default row so there is always one record to update
        DB::table('site_settings')->insert([
            'site_name'           => 'Pharma Distribution Network',
            'hero_title'          => 'Connecting Doctors, Stores & Patients',
            'hero_subtitle'       => 'A professional pharmaceutical distribution platform that streamlines ordering, referrals, and field operations.',
            'about_title'         => 'About Us',
            'about_description'   => 'Our platform connects medical representatives, doctors, and stores to streamline pharmaceutical distribution across India.',
            'contact_phone'       => null,
            'contact_email'       => null,
            'address'             => null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
