<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    protected $fillable = [
        'pincode',
        'post_office',
        'office_type',
        'related_suboffice',
        'related_headoffice',
        'state',
        'district',
        'country',
    ];

    /**
     * Lookup PIN code and return location data
     */
    public static function lookup(string $pin): ?self
    {
        return self::where('pincode', $pin)->first();
    }

    /**
     * Get all PINs for a state
     */
    public static function getByState(string $state)
    {
        return self::where('state', $state)->get();
    }
}
