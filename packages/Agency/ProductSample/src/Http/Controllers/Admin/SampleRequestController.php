<?php

namespace Agency\ProductSample\Http\Controllers\Admin;

use Agency\ProductSample\DataGrids\SampleRequestDataGrid;
use Agency\ProductSample\Notifications\SampleRequestStatusChanged;
use Agency\ProductSample\Repositories\SampleRequestRepository;
use Agency\ProductSample\Services\SampleStateMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class SampleRequestController extends Controller
{
    public function __construct(
        protected SampleRequestRepository $requestRepository,
        protected SampleStateMachine $stateMachine
    ) {}

    public function index()
    {
        if (request()->ajax()) {
            return app(SampleRequestDataGrid::class)->toJson();
        }

        return view('product-sample::admin.requests.index');
    }

    public function show(int $id): View
    {
        $sampleRequest = $this->requestRepository->find($id);

        if (! $sampleRequest) {
            abort(404);
        }

        $sampleRequest->load(['customer', 'product']);
        $allowedTransitions = $this->stateMachine->getAllowedTransitions($sampleRequest->status);

        return view('product-sample::admin.requests.show', compact('sampleRequest', 'allowedTransitions'));
    }

    public function approve(int $id): RedirectResponse
    {
        $sampleRequest = $this->requestRepository->find($id);

        if (! $sampleRequest) {
            abort(404);
        }

        $adminId = auth()->guard('admin')->id();

        try {
            $sampleRequest = $this->stateMachine->transition($sampleRequest, 'approved', $adminId);
            $sampleRequest->customer->notify(new SampleRequestStatusChanged($sampleRequest));

            return redirect()->route('admin.sample-requests.show', $id)
                ->with('success', 'Sample request approved.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $sampleRequest = $this->requestRepository->find($id);

        if (! $sampleRequest) {
            abort(404);
        }

        $adminId = auth()->guard('admin')->id();

        $sampleRequest = $this->stateMachine->transition($sampleRequest, 'rejected', $adminId, [
            'rejection_reason' => $request->rejection_reason,
        ]);

        $sampleRequest->customer->notify(new SampleRequestStatusChanged($sampleRequest));

        return redirect()->route('admin.sample-requests.show', $id)
            ->with('success', 'Sample request rejected.');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status'        => 'required|in:shipped,delivered',
            'tracking_info' => 'nullable|string',
        ]);

        $sampleRequest = $this->requestRepository->find($id);

        if (! $sampleRequest) {
            abort(404);
        }

        $adminId = auth()->guard('admin')->id();

        $sampleRequest = $this->stateMachine->transition($sampleRequest, $request->status, $adminId, [
            'tracking_info' => $request->tracking_info,
        ]);

        $sampleRequest->customer->notify(new SampleRequestStatusChanged($sampleRequest));

        return redirect()->route('admin.sample-requests.show', $id)
            ->with('success', 'Status updated to ' . $request->status . '.');
    }
}
