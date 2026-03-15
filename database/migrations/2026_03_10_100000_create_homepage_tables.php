<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // --- homepage_sections ---
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();   // hero, about, features, contact, cta
            $table->string('section_title');           // Human-readable name for admin
            $table->string('status')->default('active'); // active | inactive
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // --- homepage_contents ---
        Schema::create('homepage_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('homepage_sections')->onDelete('cascade');
            $table->string('field_key');               // e.g. title, subtitle, button_text, image
            $table->text('field_value')->nullable();
            $table->timestamps();
            $table->unique(['section_id', 'field_key']);
        });

        // ---- Seed default sections ----
        $sections = [
            ['section_key' => 'hero',     'section_title' => 'Hero / Banner',    'status' => 'active', 'sort_order' => 1],
            ['section_key' => 'about',    'section_title' => 'About Us',         'status' => 'active', 'sort_order' => 2],
            ['section_key' => 'features', 'section_title' => 'Features',         'status' => 'active', 'sort_order' => 3],
            ['section_key' => 'cta',      'section_title' => 'Call to Action',   'status' => 'active', 'sort_order' => 4],
            ['section_key' => 'contact',  'section_title' => 'Contact Us',       'status' => 'active', 'sort_order' => 5],
        ];

        foreach ($sections as $s) {
            DB::table('homepage_sections')->insert(array_merge($s, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ---- Seed default contents ----
        $sectionIds = DB::table('homepage_sections')->pluck('id', 'section_key');

        $contents = [
            // Hero
            [$sectionIds['hero'], 'title',        'Connecting Doctors, Stores & Patients'],
            [$sectionIds['hero'], 'subtitle',     'A professional pharmaceutical distribution platform that streamlines ordering, referrals, and field operations.'],
            [$sectionIds['hero'], 'button_text',  'Login to Platform'],
            [$sectionIds['hero'], 'button_link',  '/login'],
            [$sectionIds['hero'], 'image',        null],
            // About
            [$sectionIds['about'], 'title',       'About Our Platform'],
            [$sectionIds['about'], 'description', 'Our platform connects medical representatives, doctors, and stores to streamline pharmaceutical distribution across India.'],
            [$sectionIds['about'], 'image',       null],
            // Features
            [$sectionIds['features'], 'title',    'Everything You Need'],
            [$sectionIds['features'], 'subtitle', 'A fully integrated system designed for the modern pharmaceutical industry.'],
            // CTA
            [$sectionIds['cta'], 'title',         'Ready to Get Started?'],
            [$sectionIds['cta'], 'subtitle',      'Login to your account or register to join the platform.'],
            [$sectionIds['cta'], 'button_text',   'Login Now'],
            [$sectionIds['cta'], 'button_link',   '/login'],
            // Contact
            [$sectionIds['contact'], 'phone',     null],
            [$sectionIds['contact'], 'email',     null],
            [$sectionIds['contact'], 'address',   null],
            [$sectionIds['contact'], 'map_link',  null],
        ];

        foreach ($contents as [$secId, $key, $value]) {
            DB::table('homepage_contents')->insert([
                'section_id'  => $secId,
                'field_key'   => $key,
                'field_value' => $value,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_contents');
        Schema::dropIfExists('homepage_sections');
    }
};
