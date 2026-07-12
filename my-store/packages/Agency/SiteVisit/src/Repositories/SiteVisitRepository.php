<?php

namespace Agency\SiteVisit\Repositories;

use Agency\SiteVisit\Models\SiteVisit;
use Illuminate\Support\Collection;
use Webkul\Core\Eloquent\Repository;

class SiteVisitRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return SiteVisit::class;
    }

    /**
     * Get all site visits for a specific customer.
     */
    public function findByCustomer(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all site visits with a specific status.
     */
    public function findByStatus(string $status): Collection
    {
        return $this->model
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Assign a sales rep to a site visit.
     * Returns the updated site visit.
     */
    public function assignRep(int $visitId, int $repId, int $adminId): SiteVisit
    {
        $visit = $this->find($visitId);

        $visit->update([
            'assigned_rep_id' => $repId,
            'assigned_by_id'  => $adminId,
            'assigned_at'     => now(),
            'status'          => 'assigned',
        ]);

        return $visit->fresh();
    }
}
