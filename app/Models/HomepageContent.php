<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomepageContent extends Model
{
    protected $fillable = ['section_id', 'field_key', 'field_value'];

    public function section()
    {
        return $this->belongsTo(HomepageSection::class, 'section_id');
    }

    /**
     * Return full public URL if this field stores an image path.
     */
    public function imageUrl(): ?string
    {
        return $this->field_value ? asset('storage/' . $this->field_value) : null;
    }
}
