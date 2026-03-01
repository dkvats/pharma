<?php

namespace App\Models\MR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorVisit extends Model
{
    use HasFactory;

    protected $table = 'mr_doctor_visits';

    protected $fillable = [
        'doctor_id',
        'mr_id',
        'visit_date',
        'visit_time',
        'remarks',
        'products_discussed',
        'next_visit_date',
        'photo_path',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'visit_time' => 'datetime',
        'next_visit_date' => 'date',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function mr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    // Scopes
    public function scopeForMR($query, $mrId)
    {
        return $query->where('mr_id', $mrId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visit_date', now()->month)
                     ->whereYear('visit_date', now()->year);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('next_visit_date', '>=', today())
                     ->orderBy('next_visit_date');
    }
}
