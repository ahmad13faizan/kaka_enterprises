<?php

namespace Agency\SiteVisit\Http\Controllers\Shop;

use Agency\SiteVisit\Http\Requests\SiteVisitRequest;
use Agency\SiteVisit\Repositories\SiteVisitRepository;
use Agency\SiteVisit\Notifications\SiteVisitRequested;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;
use Webkul\User\Models\Admin;

class SiteVisitController extends Controller
{
    public function __construct(
        protected SiteVisitRepository $siteVisitRepository
    ) {}

    /**
     * List the current customer's site visit requests.
     */
    public function index(): View
    {
        $customerId = auth()->guard('customer')->id();
        $visits = $this->siteVisitRepository->findByCustomer($customerId);

        return view('site-visit::shop.index', compact('visits'));
    }

    /**
     * Show the site visit request form.
     */
    public function create(): View|RedirectResponse
    {
        // Check if module is enabled
        if (! $this->isModuleEnabled()) {
            abort(404);
        }

        $serviceRadius = core()->getConfigData('site_visits.general.service_radius');

        return view('site-visit::shop.create', compact('serviceRadius'));
    }

    /**
     * Store a new site visit request.
     */
    public function store(SiteVisitRequest $request): RedirectResponse
    {
        if (! $this->isModuleEnabled()) {
            abort(404);
        }

        $visit = $this->siteVisitRepository->create([
            'customer_id'         => auth()->guard('customer')->id(),
            'product_id'          => $request->product_id,
            'address'             => $request->address,
            'preferred_date'      => $request->preferred_date,
            'preferred_time_slot' => $request->preferred_time_slot,
            'notes'               => $request->notes,
            'status'              => 'requested',
        ]);

        // Notify admin(s)
        $admins = Admin::all();
        Notification::send($admins, new SiteVisitRequested($visit));

        return redirect()
            ->route('shop.site-visits.index')
            ->with('success', trans('site-visit::app.shop.site-visits.success'));
    }

    /**
     * Cancel a pending site visit request (requester only).
     */
    public function cancel(int $id): RedirectResponse
    {
        $customerId = auth()->guard('customer')->id();
        $visit = $this->siteVisitRepository->find($id);

        // Ensure the visit belongs to this customer
        if (! $visit || $visit->customer_id !== $customerId) {
            abort(404);
        }

        // Only allow cancellation from "requested" status
        if ($visit->status !== 'requested') {
            return redirect()
                ->route('shop.site-visits.index')
                ->with('error', trans('site-visit::app.shop.site-visits.cancel-error'));
        }

        $visit->update(['status' => 'cancelled']);

        return redirect()
            ->route('shop.site-visits.index')
            ->with('success', trans('site-visit::app.shop.site-visits.cancelled'));
    }

    /**
     * Check if the site visits module is enabled via config.
     */
    private function isModuleEnabled(): bool
    {
        return (bool) core()->getConfigData('site_visits.general.enabled');
    }
}
