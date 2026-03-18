<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorProductPrice extends Model
{
    protected $fillable = [
        'doctor_id',
        'product_id',
        'special_price',
    ];

    protected $casts = [
        'special_price' => 'decimal:2',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
