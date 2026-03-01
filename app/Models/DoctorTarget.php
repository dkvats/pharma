<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorTarget extends Model
{
    protected $fillable = [
        'doctor_id',
        'year',
        'month',
        'target_quantity',
        'achieved_quantity',
        'target_completed',
        'spin_eligible',
        'spin_completed_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'target_quantity' => 'integer',
        'achieved_quantity' => 'integer',
        'target_completed' => 'boolean',
        'spin_eligible' => 'boolean',
        'spin_completed_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
