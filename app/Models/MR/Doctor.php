<?php

namespace App\Models\MR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'mr_doctors';

    protected $fillable = [
        'user_id',
        'doctor_code',
        'name',
        'specialization',
        'clinic_name',
        'license_no',
        'address',
        'pincode',
        'area_id',
        'city_id',
        'city',
        'district_id',
        'district',
        'state_id',
        'state',
        'mobile',
        'email',
        'phone',
        'aadhaar_no',
        'pan_no',
        'bank_name',
        'ifsc',
        'account_no',
        'created_by',
        'assigned_mr_id',
        'is_active',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedMr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_mr_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(DoctorVisit::class, 'doctor_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'doctor_id');
    }

    public function samples(): HasMany
    {
        return $this->hasMany(Sample::class, 'doctor_id');
    }

    // Scopes
    public function scopeForMR($query, $mrId)
    {
        return $query->where(function ($q) use ($mrId) {
            $q->where('assigned_mr_id', $mrId)
              ->orWhere('created_by', $mrId);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisitedToday($query)
    {
        return $query->whereHas('visits', function ($q) {
            $q->whereDate('visit_date', today());
        });
    }

    public function scopeNewToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    
    // Status helpers
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
    
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }
    
    public function canReceiveReferrals(): bool
    {
        return $this->isApproved() && $this->user_id !== null;
    }
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>',
            'rejected' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>',
            'inactive' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>',
        };
    }
    
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'inactive' => 'gray',
            default => 'gray',
        };
    }
}
