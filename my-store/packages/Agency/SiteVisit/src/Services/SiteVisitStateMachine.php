<?php

namespace Agency\SiteVisit\Services;

use Agency\SiteVisit\Models\SiteVisit;
use Agency\SiteVisit\Models\SiteVisitStatusLog;
use Illuminate\Validation\ValidationException;

class SiteVisitStateMachine
{
    /**
     * Valid transitions: current_status => [allowed_next_statuses].
     */
    private const TRANSITIONS = [
        'requested'  => ['assigned', 'cancelled'],
        'assigned'   => ['scheduled', 'cancelled'],
        'scheduled'  => ['completed', 'cancelled'],
        'completed'  => [],
        'cancelled'  => [],
    ];

    /**
     * Check if a transition from one status to another is valid.
     */
    public function canTransition(string $from, string $to): bool
    {
        if (! isset(self::TRANSITIONS[$from])) {
            return false;
        }

        return in_array($to, self::TRANSITIONS[$from], true);
    }

    /**
     * Get all allowed next statuses from the current status.
     */
    public function getAllowedTransitions(string $currentStatus): array
    {
        return self::TRANSITIONS[$currentStatus] ?? [];
    }

    /**
     * Perform a status transition on a site visit.
     *
     * Validates the transition, updates metadata, records audit log.
     *
     * @param  SiteVisit  $visit     The site visit to transition
     * @param  string     $to        Target status
     * @param  int        $userId    The user (admin) performing the transition
     * @param  array      $metadata  Additional data (scheduled_date, scheduled_time, completion_notes, etc.)
     *
     * @throws ValidationException If transition is invalid or required metadata is missing
     */
    public function transition(SiteVisit $visit, string $to, int $userId, array $metadata = []): SiteVisit
    {
        $from = $visit->status;

        // Validate the transition is allowed
        if (! $this->canTransition($from, $to)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition from '{$from}' to '{$to}'. Allowed: "
                    . implode(', ', $this->getAllowedTransitions($from)) ?: 'none (terminal state)',
            ]);
        }

        // Validate required metadata for specific transitions
        $this->validateMetadata($to, $metadata);

        // Build the update data
        $updateData = ['status' => $to];

        // Apply transition-specific metadata
        if ($to === 'scheduled') {
            $updateData['scheduled_date'] = $metadata['scheduled_date'];
            $updateData['scheduled_time'] = $metadata['scheduled_time'];
        }

        if ($to === 'completed') {
            $updateData['completed_at'] = now();
            if (isset($metadata['completion_notes'])) {
                $updateData['completion_notes'] = $metadata['completion_notes'];
            }
        }

        // Update the site visit
        $visit->update($updateData);

        // Create audit log entry
        SiteVisitStatusLog::create([
            'site_visit_id'   => $visit->id,
            'previous_status' => $from,
            'new_status'      => $to,
            'changed_by_id'   => $userId,
            'notes'           => $metadata['notes'] ?? null,
        ]);

        return $visit->fresh();
    }

    /**
     * Validate that required metadata is present for specific transitions.
     *
     * @throws ValidationException
     */
    private function validateMetadata(string $targetStatus, array $metadata): void
    {
        if ($targetStatus === 'scheduled') {
            $errors = [];

            if (empty($metadata['scheduled_date'])) {
                $errors['scheduled_date'] = 'Scheduled date is required when scheduling a visit.';
            }

            if (empty($metadata['scheduled_time'])) {
                $errors['scheduled_time'] = 'Scheduled time is required when scheduling a visit.';
            }

            if (! empty($errors)) {
                throw ValidationException::withMessages($errors);
            }
        }
    }
}
