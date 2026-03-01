<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_id',
        'doctor_id',
        'quantity',
        'prescription_path',
        'sale_type',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'sale_type' => 'string',
    ];

    /**
     * Get the store that made this sale
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_id');
    }

    /**
     * Get the product sold
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the doctor referred (if any)
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get prescription URL
     */
    public function getPrescriptionUrlAttribute(): string
    {
        return asset('storage/' . $this->prescription_path);
    }

    /**
     * Check if this is a doctor referral sale
     */
    public function isDoctorReferral(): bool
    {
        return $this->sale_type === 'doctor_referral';
    }

    /**
     * Check if this is a store direct sale
     */
    public function isStoreDirect(): bool
    {
        return $this->sale_type === 'store_direct';
    }
}
