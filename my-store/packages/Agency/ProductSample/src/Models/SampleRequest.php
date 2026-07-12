<?php

namespace Agency\ProductSample\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SampleRequest extends Model
{
    protected $table = 'sample_requests';

    protected $fillable = [
        'customer_id',
        'product_id',
        'quantity',
        'shipping_address',
        'status',
        'rejection_reason',
        'shipped_at',
        'tracking_info',
        'delivered_at',
    ];

    protected $casts = [
        'shipped_at'   => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Customer\Models\Customer::class, 'customer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Product\Models\Product::class, 'product_id');
    }
}
