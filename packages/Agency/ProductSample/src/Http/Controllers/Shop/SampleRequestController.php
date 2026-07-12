<?php

namespace Agency\ProductSample\Http\Controllers\Shop;

use Agency\ProductSample\Http\Requests\SampleRequestFormRequest;
use Agency\ProductSample\Notifications\SampleRequestReceived;
use Agency\ProductSample\Repositories\SampleInventoryRepository;
use Agency\ProductSample\Repositories\SampleRequestRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Webkul\User\Models\Admin;

class SampleRequestController extends Controller
{
    public function __construct(
        protected SampleRequestRepository $requestRepository,
        protected SampleInventoryRepository $inventoryRepository
    ) {}

    public function index(): View
    {
        $customerId = auth()->guard('customer')->id();
        $requests = $this->requestRepository->findByCustomer($customerId);

        return view('product-sample::shop.index', compact('requests'));
    }

    public function store(SampleRequestFormRequest $request): RedirectResponse
    {
        if (! $this->isModuleEnabled()) {
            abort(404);
        }

        $customerId = auth()->guard('customer')->id();
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Check sample limit
        $limit = (int) core()->getConfigData('product_samples.general.sample_limit') ?: 2;
        $existingCount = $this->requestRepository->countByCustomerAndProduct($customerId, $productId);

        if ($existingCount >= $limit) {
            return redirect()->back()
                ->with('error', trans('product-sample::app.shop.sample-requests.limit-reached'));
        }

        // Check stock availability
        if (! $this->inventoryRepository->hasAvailableStock($productId, $quantity)) {
            return redirect()->back()
                ->with('error', trans('product-sample::app.shop.sample-requests.no-stock'));
        }

        $sampleRequest = $this->requestRepository->create([
            'customer_id'      => $customerId,
            'product_id'       => $productId,
            'quantity'         => $quantity,
            'shipping_address' => $request->shipping_address,
            'status'           => 'pending',
        ]);

        // Notify admins
        Notification::send(Admin::all(), new SampleRequestReceived($sampleRequest));

        return redirect()
            ->route('shop.sample-requests.index')
            ->with('success', trans('product-sample::app.shop.sample-requests.success'));
    }

    public function cancel(int $id): RedirectResponse
    {
        $customerId = auth()->guard('customer')->id();
        $request = $this->requestRepository->find($id);

        if (! $request || $request->customer_id !== $customerId) {
            abort(404);
        }

        if ($request->status !== 'pending') {
            return redirect()->route('shop.sample-requests.index')
                ->with('error', trans('product-sample::app.shop.sample-requests.cancel-error'));
        }

        $request->update(['status' => 'rejected']);

        return redirect()->route('shop.sample-requests.index')
            ->with('success', trans('product-sample::app.shop.sample-requests.cancelled'));
    }

    private function isModuleEnabled(): bool
    {
        return (bool) core()->getConfigData('product_samples.general.enabled');
    }
}
