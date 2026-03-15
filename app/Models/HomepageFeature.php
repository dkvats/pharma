<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageFeature extends Model
{
    protected $fillable = ['title', 'description', 'icon', 'icon_color', 'status', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active features only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordering by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Check if feature is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the Tailwind background class for the icon container.
     */
    public function getIconBgClassAttribute(): string
    {
        return match($this->icon_color) {
            'blue'     => 'bg-blue-100',
            'yellow'   => 'bg-yellow-100',
            'green'    => 'bg-green-100',
            'purple'   => 'bg-purple-100',
            'red'      => 'bg-red-100',
            'indigo'   => 'bg-indigo-100',
            'orange'   => 'bg-orange-100',
            'pink'     => 'bg-pink-100',
            'cyan'     => 'bg-cyan-100',
            'gray'     => 'bg-gray-100',
            default    => 'bg-blue-100',
        };
    }

    /**
     * Get the Tailwind text color class for the icon.
     */
    public function getIconTextClassAttribute(): string
    {
        return match($this->icon_color) {
            'blue'     => 'text-blue-600',
            'yellow'   => 'text-yellow-600',
            'green'    => 'text-green-600',
            'purple'   => 'text-purple-600',
            'red'      => 'text-red-600',
            'indigo'   => 'text-indigo-600',
            'orange'   => 'text-orange-600',
            'pink'     => 'text-pink-600',
            'cyan'     => 'text-cyan-600',
            'gray'     => 'text-gray-600',
            default    => 'text-blue-600',
        };
    }

    /**
     * Get the full Font Awesome class.
     */
    public function getIconClassAttribute(): string
    {
        return 'fas fa-' . $this->icon;
    }
}
