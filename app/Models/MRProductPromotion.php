<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MRProductPromotion extends Model
{
    protected $table = 'mr_product_promotions';

    protected $fillable = [
        'mr_id',
        'doctor_id',
        'product_id',
        'visit_id',
        'notes',
    ];

    public function mr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(\App\Models\MR\DoctorVisit::class, 'visit_id');
    }

    // Scopes
    public function scopeForMR($query, int $mrId)
    {
        return $query->where('mr_id', $mrId);
    }

    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeForVisit($query, int $visitId)
    {
        return $query->where('visit_id', $visitId);
    }
}
