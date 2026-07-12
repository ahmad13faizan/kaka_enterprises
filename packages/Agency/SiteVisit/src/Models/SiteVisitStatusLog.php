<?php

namespace Agency\SiteVisit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisitStatusLog extends Model
{
    public $timestamps = false;

    protected $table = 'site_visit_status_logs';

    protected $fillable = [
        'site_visit_id',
        'previous_status',
        'new_status',
        'changed_by_id',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * The site visit this log entry belongs to.
     */
    public function siteVisit(): BelongsTo
    {
        return $this->belongsTo(SiteVisit::class, 'site_visit_id');
    }

    /**
     * The user who triggered this status change.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(\Webkul\User\Models\Admin::class, 'changed_by_id');
    }
}
