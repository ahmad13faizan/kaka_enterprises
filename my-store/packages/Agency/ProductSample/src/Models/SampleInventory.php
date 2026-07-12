<?php

namespace Agency\ProductSample\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SampleInventory extends Model
{
    protected $table = 'sample_inventory';

    protected $fillable = [
        'product_id',
        'sample_stock',
        'allocated',
    ];

    /**
     * Get available sample stock (total - allocated).
     */
    public function getAvailableAttribute(): int
    {
        return $this->sample_stock - $this->allocated;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Product\Models\Product::class, 'product_id');
    }
}
