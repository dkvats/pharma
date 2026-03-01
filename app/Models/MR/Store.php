<?php

namespace App\Models\MR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    protected $table = 'mr_stores';

    protected $fillable = [
        'mr_id',
        'user_id',
        'store_name',
        'owner_name',
        'store_code',
        'phone',
        'email',
        'address',
        'pincode',
        'state_id',
        'district_id',
        'city_id',
        'area_id',
        'state',
        'district',
        'city',
        'area',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * MR who registered this store
     */
    public function mr(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'mr_id');
    }

    /**
     * Actual store user account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Admin who approved this store
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    /**
     * State relationship
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * District relationship
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * City relationship
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Area relationship
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Check if store is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if store is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if store is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if store can receive orders
     */
    public function canReceiveOrders(): bool
    {
        return $this->status === 'approved' && $this->user_id !== null;
    }
}
