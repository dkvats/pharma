<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageNavItem extends Model
{
    protected $fillable = ['label', 'url', 'is_external', 'sort_order', 'status'];

    protected $casts = [
        'is_external' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
