<?php

namespace Agency\SiteVisit\Http\Controllers\Admin;

use Agency\SiteVisit\DataGrids\SiteVisitDataGrid;
use Agency\SiteVisit\Notifications\SiteVisitAssigned;
use Agency\SiteVisit\Notifications\SiteVisitReassigned;
use Agency\SiteVisit\Notifications\SiteVisitStatusChanged;
use Agency\SiteVisit\Repositories\SiteVisitRepository;
use Agency\SiteVisit\Services\SiteVisitStateMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Webkul\User\Models\Admin;

class SiteVisitController extends Controller
{
    public function __construct(
        protected SiteVisitRepository $siteVisitRepository,
        protected SiteVisitStateMachine $stateMachine
    ) {}

    /**
     * DataGrid listing of all site visits.
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SiteVisitDataGrid::class)->toJson();
        }

        return view('site-visit::admin.index');
    }

    /**
     * Detail view for a single site visit.
     */
    public function show(int $id): View
    {
        $visit = $this->siteVisitRepository->find($id);

        if (! $visit) {
            abort(404);
        }

        $visit->load(['customer', 'product', 'assignedRep', 'assignedBy', 'statusLogs.changedBy']);
        $allowedTransitions = $this->stateMachine->getAllowedTransitions($visit->status);
        $salesReps = Admin::all();

        return view('site-visit::admin.show', compact('visit', 'allowedTransitions', 'salesReps'));
    }

    /**
     * Assign a sales rep to a site visit.
     */
    public function assign(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'assigned_rep_id' => 'required|exists:admins,id',
        ]);

        $visit = $this->siteVisitRepository->find($id);

        if (! $visit) {
            abort(404);
        }

        $adminId = auth()->guard('admin')->id();
        $previousRepId = $visit->assigned_rep_id;
        $newRepId = (int) $request->assigned_rep_id;

        // If already has a rep, this is a reassignment
        if ($previousRepId && $previousRepId !== $newRepId) {
            $visit->update([
                'assigned_rep_id' => $newRepId,
                'assigned_by_id'  => $adminId,
                'assigned_at'     => now(),
            ]);

            $previousRep = Admin::find($previousRepId);
            $newRep = Admin::find($newRepId);
            $previousRep->notify(new SiteVisitReassigned($visit, 'removed'));
            $newRep->notify(new SiteVisitReassigned($visit, 'assigned'));
        } else {
            // First assignment — use state machine to transition
            $this->stateMachine->transition($visit, 'assigned', $adminId);
            $visit->update([
                'assigned_rep_id' => $newRepId,
                'assigned_by_id'  => $adminId,
                'assigned_at'     => now(),
            ]);

            $newRep = Admin::find($newRepId);
            $newRep->notify(new SiteVisitAssigned($visit->fresh()));

            // Notify requester of status change
            $visit->fresh()->customer->notify(new SiteVisitStatusChanged($visit->fresh()));
        }

        return redirect()
            ->route('admin.site-visits.show', $id)
            ->with('success', 'Sales rep assigned successfully.');
    }

    /**
     * Update the status of a site visit (schedule, complete, cancel).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status'         => 'required|in:scheduled,completed,cancelled',
            'scheduled_date' => 'required_if:status,scheduled|nullable|date',
            'scheduled_time' => 'required_if:status,scheduled|nullable|string',
            'completion_notes' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $visit = $this->siteVisitRepository->find($id);

        if (! $visit) {
            abort(404);
        }

        $adminId = auth()->guard('admin')->id();

        $metadata = array_filter([
            'scheduled_date'   => $request->scheduled_date,
            'scheduled_time'   => $request->scheduled_time,
            'completion_notes' => $request->completion_notes,
            'notes'            => $request->notes,
        ]);

        $visit = $this->stateMachine->transition($visit, $request->status, $adminId, $metadata);

        // Notify requester of status change
        $visit->customer->notify(new SiteVisitStatusChanged($visit));

        return redirect()
            ->route('admin.site-visits.show', $id)
            ->with('success', 'Status updated to ' . $request->status . '.');
    }

    /**
     * Mass cancel selected site visits.
     */
    public function massCancel(Request $request): RedirectResponse
    {
        $ids = $request->input('indices', []);

        if (empty($ids)) {
            return redirect()->route('admin.site-visits.index')
                ->with('error', 'No site visits selected.');
        }

        $adminId = auth()->guard('admin')->id();
        $cancelledCount = 0;

        foreach ($ids as $id) {
            $visit = $this->siteVisitRepository->find($id);

            if ($visit && $this->stateMachine->canTransition($visit->status, 'cancelled')) {
                $this->stateMachine->transition($visit, 'cancelled', $adminId);
                $visit->customer->notify(new SiteVisitStatusChanged($visit->fresh()));
                $cancelledCount++;
            }
        }

        return redirect()->route('admin.site-visits.index')
            ->with('success', "{$cancelledCount} site visit(s) cancelled.");
    }
}
