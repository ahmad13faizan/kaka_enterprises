<?php

namespace Agency\SiteVisit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteVisit extends Model
{
    protected $table = 'site_visits';

    protected $fillable = [
        'customer_id',
        'product_id',
        'address',
        'preferred_date',
        'preferred_time_slot',
        'notes',
        'status',
        'assigned_rep_id',
        'assigned_by_id',
        'assigned_at',
        'scheduled_date',
        'scheduled_time',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'assigned_at'    => 'datetime',
        'scheduled_date' => 'date',
        'completed_at'   => 'datetime',
    ];

    /**
     * The customer who requested the visit.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Customer\Models\Customer::class, 'customer_id');
    }

    /**
     * The product the visit is about.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Product\Models\Product::class, 'product_id');
    }

    /**
     * The sales rep assigned to this visit.
     */
    public function assignedRep(): BelongsTo
    {
        return $this->belongsTo(\Webkul\User\Models\Admin::class, 'assigned_rep_id');
    }

    /**
     * The admin who made the assignment.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\User\Models\Admin::class, 'assigned_by_id');
    }

    /**
     * Status change history.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(SiteVisitStatusLog::class, 'site_visit_id')->orderBy('created_at', 'desc');
    }
}
