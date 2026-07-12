<?php

namespace Agency\ProductSample\Services;

use Agency\ProductSample\Models\SampleRequest;
use Agency\ProductSample\Repositories\SampleInventoryRepository;
use Illuminate\Validation\ValidationException;

class SampleStateMachine
{
    private const TRANSITIONS = [
        'pending'   => ['approved', 'rejected'],
        'approved'  => ['shipped'],
        'shipped'   => ['delivered'],
        'delivered' => [],
        'rejected'  => [],
    ];

    public function __construct(
        protected SampleInventoryRepository $inventoryRepository
    ) {}

    public function canTransition(string $from, string $to): bool
    {
        if (! isset(self::TRANSITIONS[$from])) {
            return false;
        }

        return in_array($to, self::TRANSITIONS[$from], true);
    }

    public function getAllowedTransitions(string $currentStatus): array
    {
        return self::TRANSITIONS[$currentStatus] ?? [];
    }

    /**
     * Perform a status transition.
     *
     * @throws ValidationException
     */
    public function transition(SampleRequest $request, string $to, int $adminId, array $metadata = []): SampleRequest
    {
        $from = $request->status;

        if (! $this->canTransition($from, $to)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from '{$from}' to '{$to}'.",
            ]);
        }

        $updateData = ['status' => $to];

        // Handle approval — check and allocate stock
        if ($to === 'approved') {
            $allocated = $this->inventoryRepository->allocate($request->product_id, $request->quantity);

            if (! $allocated) {
                throw ValidationException::withMessages([
                    'stock' => 'Insufficient sample stock to approve this request.',
                ]);
            }
        }

        // Handle rejection
        if ($to === 'rejected') {
            $updateData['rejection_reason'] = $metadata['rejection_reason'] ?? null;
        }

        // Handle shipped
        if ($to === 'shipped') {
            $updateData['shipped_at'] = now();
            $updateData['tracking_info'] = $metadata['tracking_info'] ?? null;
        }

        // Handle delivered — release allocation (stock is consumed)
        if ($to === 'delivered') {
            $updateData['delivered_at'] = now();
            $this->inventoryRepository->releaseAllocation($request->product_id, $request->quantity);
        }

        $request->update($updateData);

        return $request->fresh();
    }
}
