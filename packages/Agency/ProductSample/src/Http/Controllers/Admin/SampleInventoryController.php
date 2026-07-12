<?php

namespace Agency\ProductSample\Http\Controllers\Admin;

use Agency\ProductSample\DataGrids\SampleInventoryDataGrid;
use Agency\ProductSample\Repositories\SampleInventoryRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SampleInventoryController extends Controller
{
    public function __construct(
        protected SampleInventoryRepository $inventoryRepository
    ) {}

    public function index()
    {
        if (request()->ajax()) {
            return app(SampleInventoryDataGrid::class)->toJson();
        }

        return view('product-sample::admin.inventory.index');
    }

    public function update(Request $request, int $productId): RedirectResponse
    {
        $request->validate([
            'sample_stock' => 'required|integer|min:0',
        ]);

        $this->inventoryRepository->updateStock($productId, (int) $request->sample_stock);

        return redirect()->route('admin.sample-inventory.index')
            ->with('success', trans('product-sample::app.admin.sample-inventory.updated'));
    }
}
