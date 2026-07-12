<?php

namespace Agency\ProductSample\Repositories;

use Agency\ProductSample\Models\SampleInventory;
use Webkul\Core\Eloquent\Repository;

class SampleInventoryRepository extends Repository
{
    public function model(): string
    {
        return SampleInventory::class;
    }

    /**
     * Get sample inventory for a product (create if doesn't exist).
     */
    public function getForProduct(int $productId): SampleInventory
    {
        return SampleInventory::firstOrCreate(
            ['product_id' => $productId],
            ['sample_stock' => 0, 'allocated' => 0]
        );
    }

    /**
     * Update sample stock for a product.
     */
    public function updateStock(int $productId, int $quantity): SampleInventory
    {
        $inventory = $this->getForProduct($productId);
        $inventory->update(['sample_stock' => $quantity]);

        return $inventory->fresh();
    }

    /**
     * Allocate stock (called when request is approved).
     * Returns false if insufficient stock.
     */
    public function allocate(int $productId, int $quantity): bool
    {
        $inventory = $this->getForProduct($productId);

        if ($inventory->available < $quantity) {
            return false;
        }

        $inventory->increment('allocated', $quantity);

        return true;
    }

    /**
     * Release allocation (when request moves to delivered, stock is consumed).
     */
    public function releaseAllocation(int $productId, int $quantity): void
    {
        $inventory = $this->getForProduct($productId);
        $inventory->decrement('allocated', min($quantity, $inventory->allocated));
        $inventory->decrement('sample_stock', min($quantity, $inventory->sample_stock));
    }

    /**
     * Check if enough stock is available.
     */
    public function hasAvailableStock(int $productId, int $quantity): bool
    {
        $inventory = $this->getForProduct($productId);

        return $inventory->available >= $quantity;
    }
}
