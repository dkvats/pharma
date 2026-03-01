<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'probability',
        'is_active',
        'stock',
        'image',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'probability' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'integer',
    ];

    public function spinHistories()
    {
        return $this->hasMany(SpinHistory::class);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && ($this->stock === null || $this->stock > 0);
    }
}
