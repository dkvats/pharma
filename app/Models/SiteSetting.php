<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'site_name',
        'tagline',
        'logo',
        'favicon',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'about_title',
        'about_description',
        'contact_phone',
        'contact_email',
        'address',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'instagram_url',
        'whatsapp_number',
    ];

    /**
     * Always return the first (only) row, creating defaults if none exist.
     */
    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'site_name'         => 'Pharma Distribution Network',
            'hero_title'        => 'Connecting Doctors, Stores & Patients',
            'hero_subtitle'     => 'A professional pharmaceutical distribution platform.',
            'about_title'       => 'About Us',
            'about_description' => 'Our platform connects medical representatives, doctors, and stores.',
        ]);
    }

    /**
     * Full URL for logo image
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Full URL for hero image
     */
    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->hero_image ? asset('storage/' . $this->hero_image) : null;
    }
}
