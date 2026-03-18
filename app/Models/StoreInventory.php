<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInventory extends Model
{
    protected $fillable = [
        'store_id',
        'product_batch_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'store_id');
    }

    public function productBatch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }
}
