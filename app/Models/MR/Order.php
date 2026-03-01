<?php

namespace App\Models\MR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'mr_orders';

    protected $fillable = [
        'order_number',
        'doctor_id',
        'mr_id',
        'total_amount',
        'remarks',
        'status',
        'ordered_at',
        'approved_at',
        'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function mr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Scopes
    public function scopeForMR($query, $mrId)
    {
        return $query->where('mr_id', $mrId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('ordered_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('ordered_at', now()->month)
                     ->whereYear('ordered_at', now()->year);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Generate unique order number
    public static function generateOrderNumber(): string
    {
        $prefix = 'MR-ORD-';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;
        return $prefix . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
