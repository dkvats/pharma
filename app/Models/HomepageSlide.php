<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'button_text',
        'button_link',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active slides only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Full public URL for the slide image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Whether this slide has an uploaded image.
     */
    public function hasImage(): bool
    {
        return !empty($this->image);
    }

    /**
     * Check if slide is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
