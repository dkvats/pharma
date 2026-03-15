<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomepageSection extends Model
{
    protected $fillable = ['section_key', 'section_title', 'layout_type', 'status', 'sort_order'];

    public function contents(): HasMany
    {
        return $this->hasMany(\App\Models\HomepageContent::class, 'section_id');
    }

    /**
     * Return a key→value map of this section's contents.
     * e.g. ['title' => 'Hero Title', 'subtitle' => 'Some text', ...]
     */
    public function contentMap(): array
    {
        return $this->contents->pluck('field_value', 'field_key')->toArray();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
