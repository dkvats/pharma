<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add featured_on_homepage to products
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('featured_on_homepage')->default(false)->after('status');
        });

        // 2. Add social links + veterinary fields to site_settings
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('address');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('linkedin_url')->nullable()->after('twitter_url');
            $table->string('instagram_url')->nullable()->after('linkedin_url');
            $table->string('whatsapp_number')->nullable()->after('instagram_url');
            $table->string('tagline')->nullable()->after('site_name');
        });

        // 3. Seed new veterinary homepage sections with PROFESSIONAL ORDER:
        // 1. stats, 2. animals, 3. products, 4. trust, 5. features, 6. about, 7. cta, 8. doctor_cta, 9. contact, 10. footer
        // Note: Slider is separate and always loads first
        $newSections = [
            ['section_key' => 'stats',       'section_title' => 'Platform Statistics', 'layout_type' => 'default', 'status' => 'active', 'sort_order' => 1],
            ['section_key' => 'animals',     'section_title' => 'Animal Categories',   'layout_type' => 'default', 'status' => 'active', 'sort_order' => 2],
            ['section_key' => 'products',    'section_title' => 'Featured Products',   'layout_type' => 'default', 'status' => 'active', 'sort_order' => 3],
            ['section_key' => 'trust',       'section_title' => 'Why Choose Us',       'layout_type' => 'default', 'status' => 'active', 'sort_order' => 4],
            ['section_key' => 'doctor_cta',  'section_title' => 'Doctor / Store CTA',  'layout_type' => 'default', 'status' => 'active', 'sort_order' => 8],
        ];

        // Adjust existing sections sort_order for professional veterinary layout
        DB::table('homepage_sections')->where('section_key', 'features')->update(['sort_order' => 5]);
        DB::table('homepage_sections')->where('section_key', 'about')->update(['sort_order' => 6]);
        DB::table('homepage_sections')->where('section_key', 'cta')->update(['sort_order' => 7]);
        DB::table('homepage_sections')->where('section_key', 'contact')->update(['sort_order' => 9]);
        DB::table('homepage_sections')->where('section_key', 'footer')->update(['sort_order' => 10]);

        foreach ($newSections as $s) {
            $exists = DB::table('homepage_sections')->where('section_key', $s['section_key'])->exists();
            if (!$exists) {
                $id = DB::table('homepage_sections')->insertGetId(array_merge($s, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));

                // Seed default content for each new section
                $contents = [];
                if ($s['section_key'] === 'products') {
                    $contents = [
                        ['field_key' => 'title',    'field_value' => 'Our Featured Products'],
                        ['field_key' => 'subtitle', 'field_value' => 'Trusted veterinary medicines for cattle, buffalo, and livestock health care.'],
                    ];
                } elseif ($s['section_key'] === 'animals') {
                    $contents = [
                        ['field_key' => 'title',       'field_value' => 'Medicines for All Animals'],
                        ['field_key' => 'subtitle',    'field_value' => 'Specialized healthcare solutions for every livestock category.'],
                        ['field_key' => 'cow_label',   'field_value' => 'Cow'],
                        ['field_key' => 'cow_image',   'field_value' => null],
                        ['field_key' => 'buffalo_label',  'field_value' => 'Buffalo'],
                        ['field_key' => 'buffalo_image',  'field_value' => null],
                        ['field_key' => 'goat_label',  'field_value' => 'Goat'],
                        ['field_key' => 'goat_image',  'field_value' => null],
                        ['field_key' => 'poultry_label', 'field_value' => 'Poultry'],
                        ['field_key' => 'poultry_image', 'field_value' => null],
                    ];
                } elseif ($s['section_key'] === 'trust') {
                    $contents = [
                        ['field_key' => 'title',      'field_value' => 'Why Choose Us'],
                        ['field_key' => 'subtitle',   'field_value' => 'We are committed to delivering quality veterinary medicines with reliability and trust.'],
                        ['field_key' => 'point_1_title', 'field_value' => 'Quality Veterinary Medicines'],
                        ['field_key' => 'point_1_desc',  'field_value' => 'All products are manufactured under strict quality control standards.'],
                        ['field_key' => 'point_2_title', 'field_value' => 'Trusted by Doctors'],
                        ['field_key' => 'point_2_desc',  'field_value' => 'Thousands of veterinary doctors recommend our medicines nationwide.'],
                        ['field_key' => 'point_3_title', 'field_value' => 'Nationwide Distribution'],
                        ['field_key' => 'point_3_desc',  'field_value' => 'Pan-India distribution network ensuring timely delivery everywhere.'],
                        ['field_key' => 'point_4_title', 'field_value' => 'Research Based Products'],
                        ['field_key' => 'point_4_desc',  'field_value' => 'Backed by scientific research and continuous innovation in animal healthcare.'],
                    ];
                } elseif ($s['section_key'] === 'doctor_cta') {
                    $contents = [
                        ['field_key' => 'title',         'field_value' => 'Join Our Network'],
                        ['field_key' => 'subtitle',      'field_value' => 'Register as a veterinary doctor or store and become part of our growing network.'],
                        ['field_key' => 'doctor_button', 'field_value' => 'Register as Doctor'],
                        ['field_key' => 'doctor_link',   'field_value' => '/register'],
                        ['field_key' => 'store_button',  'field_value' => 'Register as Store'],
                        ['field_key' => 'store_link',    'field_value' => '/register'],
                    ];
                }

                foreach ($contents as $c) {
                    DB::table('homepage_contents')->insert([
                        'section_id'  => $id,
                        'field_key'   => $c['field_key'],
                        'field_value' => $c['field_value'],
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }

        // 4. Update hero section content to veterinary theme defaults
        $heroSection = DB::table('homepage_sections')->where('section_key', 'hero')->first();
        if ($heroSection) {
            DB::table('homepage_contents')
                ->where('section_id', $heroSection->id)
                ->where('field_key', 'title')
                ->update(['field_value' => 'Premium Veterinary Medicines for Healthy Livestock']);

            DB::table('homepage_contents')
                ->where('section_id', $heroSection->id)
                ->where('field_key', 'subtitle')
                ->update(['field_value' => 'Trusted pharmaceutical solutions for cattle, buffalo, goat, and poultry. Quality medicines delivered to your doorstep.']);

            DB::table('homepage_contents')
                ->where('section_id', $heroSection->id)
                ->where('field_key', 'button_text')
                ->update(['field_value' => 'Explore Products']);
        }

        // 5. Update about section to veterinary theme
        $aboutSection = DB::table('homepage_sections')->where('section_key', 'about')->first();
        if ($aboutSection) {
            DB::table('homepage_contents')
                ->where('section_id', $aboutSection->id)
                ->where('field_key', 'title')
                ->update(['field_value' => 'About Our Veterinary Platform']);

            DB::table('homepage_contents')
                ->where('section_id', $aboutSection->id)
                ->where('field_key', 'description')
                ->update(['field_value' => 'We are a leading veterinary pharmaceutical distribution platform connecting doctors, stores, and farmers across India. Our mission is to ensure every animal receives the best healthcare through quality medicines.']);
        }

        // Update site_settings tagline
        DB::table('site_settings')->update([
            'site_name' => 'VetPharma India',
            'hero_title' => 'Premium Veterinary Medicines',
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('featured_on_homepage');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url', 'twitter_url', 'linkedin_url',
                'instagram_url', 'whatsapp_number', 'tagline',
            ]);
        });

        DB::table('homepage_sections')
            ->whereIn('section_key', ['products', 'animals', 'trust', 'doctor_cta'])
            ->delete();
    }
};
