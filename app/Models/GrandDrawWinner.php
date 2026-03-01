<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrandDrawWinner extends Model
{
    protected $fillable = [
        'doctor_id',
        'year',
        'draw_date',
        'drawn_by',
        'total_eligible_doctors',
    ];

    protected $casts = [
        'draw_date' => 'datetime',
    ];

    /**
     * The winning doctor
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * The admin who ran the draw
     */
    public function drawnBy()
    {
        return $this->belongsTo(User::class, 'drawn_by');
    }
}
