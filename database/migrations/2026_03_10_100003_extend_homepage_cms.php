<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. homepage_nav_items - for dynamic navbar
        Schema::create('homepage_nav_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->boolean('is_external')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // Seed default nav items
        DB::table('homepage_nav_items')->insert([
            ['label' => 'Home',      'url' => '#hero',     'is_external' => false, 'sort_order' => 1, 'status' => 'active'],
            ['label' => 'About',     'url' => '#about',    'is_external' => false, 'sort_order' => 2, 'status' => 'active'],
            ['label' => 'Features',  'url' => '#features', 'is_external' => false, 'sort_order' => 3, 'status' => 'active'],
            ['label' => 'Contact',   'url' => '#contact',  'is_external' => false, 'sort_order' => 4, 'status' => 'active'],
            ['label' => 'Login',     'url' => '/login',    'is_external' => false, 'sort_order' => 5, 'status' => 'active'],
        ]);

        // 2. media_library - for reusable media uploads
        Schema::create('media_library', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->default('image'); // image, document, video
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });

        // 3. Add layout_type to homepage_sections for custom sections
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('layout_type')->default('default')->after('section_title');
        });

        // Update existing sections with layout_type
        DB::table('homepage_sections')->update(['layout_type' => 'default']);

        // 4. Add footer section if not exists
        $footerExists = DB::table('homepage_sections')->where('section_key', 'footer')->exists();
        if (!$footerExists) {
            $footerId = DB::table('homepage_sections')->insertGetId([
                'section_key'   => 'footer',
                'section_title' => 'Footer',
                'layout_type'   => 'default',
                'status'        => 'active',
                'sort_order'    => 6,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Add footer content fields
            DB::table('homepage_contents')->insert([
                ['section_id' => $footerId, 'field_key' => 'description',   'field_value' => 'A professional pharmaceutical distribution platform connecting healthcare providers.', 'created_at' => now(), 'updated_at' => now()],
                ['section_id' => $footerId, 'field_key' => 'phone',         'field_value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['section_id' => $footerId, 'field_key' => 'email',         'field_value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['section_id' => $footerId, 'field_key' => 'address',       'field_value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['section_id' => $footerId, 'field_key' => 'copyright',     'field_value' => 'All rights reserved.', 'created_at' => now(), 'updated_at' => now()],
                ['section_id' => $footerId, 'field_key' => 'facebook',      'field_value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['section_id' => $footerId, 'field_key' => 'twitter',       'field_value' => null, 'created_at' => now(), 'updated_at' => now()],
                ['section_id' => $footerId, 'field_key' => 'linkedin',      'field_value' => null, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_nav_items');
        Schema::dropIfExists('media_library');
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn('layout_type');
        });
    }
};
