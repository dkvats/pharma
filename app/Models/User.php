<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'code',
        'unique_code',
        'status',
        'role',
        'phone',
        'address',
        'city',
        'district',
        'tehsil',
        'village',
        'created_by',
        // MR-specific fields (nullable, only used for MR role)
        'employee_code',
        'designation',
        'assigned_area',
    ];

    /**
     * Boot the model.
     * Generate unique_code for ALL users on creation - not just doctors.
     * This ensures code exists before role assignment.
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            // Auto-generate unique_code for ALL users on creation
            // Use empty() to catch both null and empty string
            if (empty($user->unique_code)) {
                do {
                    $code = 'DOC-' . strtoupper(Str::random(6));
                } while (static::where('unique_code', $code)->exists());
                
                $user->unique_code = $code;
            }
        });
        // NOTE: No updating hook - code never changes once set
    }

    /**
     * Check if user has a valid unique_code (not null or empty).
     */
    public function hasUniqueCode(): bool
    {
        return !empty($this->unique_code);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get users created by this user.
     */
    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Get spin histories for this user (for doctors).
     */
    public function spinHistories()
    {
        return $this->hasMany(SpinHistory::class, 'doctor_id');
    }

    /**
     * Get all orders where this user is the doctor (direct + referral).
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'doctor_id');
    }

    /**
     * Get direct orders placed by this user (for doctors).
     */
    public function directOrders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Get doctor targets for this user.
     */
    public function doctorTargets()
    {
        return $this->hasMany(DoctorTarget::class, 'doctor_id');
    }

    /**
     * Get referral sales from stores (for doctors).
     */
    public function referralSales()
    {
        return $this->hasMany(StoreSale::class, 'doctor_id');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'approved'], true);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the user's cart (one-to-one relationship).
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get the user's wishlist (one-to-one relationship).
     */
    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }
}
