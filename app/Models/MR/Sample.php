<?php

namespace App\Models\MR;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sample extends Model
{
    use HasFactory;

    protected $table = 'mr_samples';

    protected $fillable = [
        'doctor_id',
        'mr_id',
        'product_id',
        'quantity',
        'given_date',
        'remarks',
        'batch_no',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'given_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function mr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mr_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Scopes
    public function scopeForMR($query, $mrId)
    {
        return $query->where('mr_id', $mrId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('given_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('given_date', now()->month)
                     ->whereYear('given_date', now()->year);
    }
}
