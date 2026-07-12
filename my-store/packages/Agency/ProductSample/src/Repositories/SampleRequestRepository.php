<?php

namespace Agency\ProductSample\Repositories;

use Agency\ProductSample\Models\SampleRequest;
use Illuminate\Support\Collection;
use Webkul\Core\Eloquent\Repository;

class SampleRequestRepository extends Repository
{
    public function model(): string
    {
        return SampleRequest::class;
    }

    public function findByCustomer(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Count active (non-rejected, non-cancelled) requests for a customer+product combo.
     */
    public function countByCustomerAndProduct(int $customerId, int $productId): int
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->whereNotIn('status', ['rejected'])
            ->count();
    }
}
