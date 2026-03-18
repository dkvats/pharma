<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'gst_no',
    ];

    /**
     * Get the product batches for this supplier.
     */
    public function productBatches(): HasMany
    {
        return $this->hasMany(ProductBatch::class, 'supplier_id');
    }
}
