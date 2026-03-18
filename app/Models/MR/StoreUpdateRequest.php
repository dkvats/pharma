<?php

namespace App\Models\MR;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreUpdateRequest extends Model
{
    protected $fillable = [
        'store_id',
        'requested_by',
        'requested_role',
        'store_name',
        'owner_name',
        'phone',
        'alt_phone',
        'email',
        'aadhaar',
        'owner_photo',
        'store_photo',
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
        'gst_no',
        'drug_license_no',
        'license_expiry',
        'pan_no',
        'store_type',
        'default_discount',
        'credit_limit',
        'payment_terms',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'license_expiry' => 'date',
        'default_discount' => 'decimal:2',
        'credit_limit' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * Store being updated
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * User who requested the update
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Admin who approved/rejected the request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get pending requests for a specific store
     */
    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Get requests by a specific user
     */
    public function scopeByRequester($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ─── Comparison Methods ───────────────────────────────────────────────────

    /**
     * Get differences between requested changes and current store data
     */
    public function getChangesSummary(): array
    {
        $originalStore = $this->store;
        $changes = [];

        $fields = [
            'store_name' => 'Store Name',
            'owner_name' => 'Owner Name',
            'phone' => 'Phone',
            'alt_phone' => 'Alternate Phone',
            'email' => 'Email',
            'aadhaar' => 'Aadhaar',
            'address' => 'Address',
            'pincode' => 'Pincode',
            'state' => 'State',
            'district' => 'District',
            'city' => 'City',
            'area' => 'Area',
            'gst_no' => 'GST Number',
            'drug_license_no' => 'Drug License',
            'license_expiry' => 'License Expiry',
            'pan_no' => 'PAN Number',
            'store_type' => 'Store Type',
            'default_discount' => 'Default Discount',
            'credit_limit' => 'Credit Limit',
            'payment_terms' => 'Payment Terms',
        ];

        foreach ($fields as $field => $label) {
            $oldValue = $originalStore->{$field};
            $newValue = $this->{$field};

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'label' => $label,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Apply approved changes to the store
     */
    public function applyToStore(): void
    {
        if (!$this->isApproved()) {
            throw new \Exception('Only approved requests can be applied to store.');
        }

        $updateData = [
            'store_name' => $this->store_name,
            'owner_name' => $this->owner_name,
            'phone' => $this->phone,
            'alt_phone' => $this->alt_phone,
            'email' => $this->email,
            'aadhaar' => $this->aadhaar,
            'owner_photo' => $this->owner_photo,
            'store_photo' => $this->store_photo,
            'address' => $this->address,
            'pincode' => $this->pincode,
            'state_id' => $this->state_id,
            'district_id' => $this->district_id,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'state' => $this->state,
            'district' => $this->district,
            'city' => $this->city,
            'area' => $this->area,
            'gst_no' => $this->gst_no,
            'drug_license_no' => $this->drug_license_no,
            'license_expiry' => $this->license_expiry,
            'pan_no' => $this->pan_no,
            'store_type' => $this->store_type,
            'default_discount' => $this->default_discount,
            'credit_limit' => $this->credit_limit,
            'payment_terms' => $this->payment_terms,
        ];

        $this->store->update($updateData);
    }
}
