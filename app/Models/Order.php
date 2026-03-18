<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'doctor_id',
        'referred_by',
        'store_id',
        'referral_code',
        'status',
        'sale_type', // Set once at creation, immutable via booted() check
        'total_amount',
        'discount_amount',
        'offer_id',
        'notes',
        'prescription',
        'prescription_uploaded_at',
        'prescription_required',
        'approved_at',
        'approved_by',
        'delivered_at',
        'bill_generated',
        'bill_path',
    ];

    /**
     * Boot the model.
     * Enforce enterprise hardening rules.
     */
    protected static function booted(): void
    {
        // ENFORCE: sale_type is immutable after creation
        static::updating(function ($order) {
            if ($order->isDirty('sale_type')) {
                throw new \Exception('Sale type cannot be modified after order creation. This field is immutable for audit integrity.');
            }
        });
    }

    protected $casts = [
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
        'prescription_uploaded_at' => 'datetime',
        'prescription_required' => 'boolean',
        'bill_generated' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function store()
    {
        return $this->belongsTo(User::class, 'store_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getSaleTypeLabelAttribute(): string
    {
        return match($this->sale_type) {
            'doctor_direct' => 'Doctor Direct Sale',
            'referral' => 'Referral Sale',
            'store_linked' => 'Store Linked Sale',
            'company_direct' => 'Company Direct Sale',
            default => 'Unknown',
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getPrescriptionUrlAttribute(): ?string
    {
        return $this->prescription ? asset('storage/' . $this->prescription) : null;
    }

    public function hasPrescription(): bool
    {
        return !empty($this->prescription);
    }
}
