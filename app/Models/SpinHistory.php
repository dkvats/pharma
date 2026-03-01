<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinHistory extends Model
{
    protected $fillable = [
        'doctor_id',
        'reward_id',
        'spin_date',
        'claimed',
        'claimed_at',
    ];

    protected $casts = [
        'spin_date' => 'date',
        'claimed' => 'boolean',
        'claimed_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}
