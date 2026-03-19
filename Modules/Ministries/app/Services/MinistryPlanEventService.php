<?php

namespace Modules\Ministries\App\Services;

use Modules\Events\App\Models\Event;
use Modules\Ministries\App\Models\MinistryPlan;

class MinistryPlanEventService
{
    /**
     * Create one Event from a plan activity by index.
     * Activity shape: title|name, date|start_date, end_date (optional), description (optional), requires_approval (optional).
     */
    public function createEventFromActivity(MinistryPlan $plan, int $activityIndex): Event
    {
        $activities = is_array($plan->activities) ? $plan->activities : [];
        $activity = $activities[$activityIndex] ?? null;
        if (! $activity || ! is_array($activity)) {
            throw new \InvalidArgumentException('Atividade não encontrada no plano.');
        }

        $dateStr = $activity['date'] ?? $activity['start_date'] ?? null;
        if (! $dateStr) {
            throw new \InvalidArgumentException('Atividade sem data definida.');
        }

        $startDate = \Carbon\Carbon::parse($dateStr);
        $endDate = null;
        if (! empty($activity['end_date'])) {
            $endDate = \Carbon\Carbon::parse($activity['end_date']);
        } else {
            $endDate = $startDate->copy()->addHours(2);
        }
        $title = $activity['title'] ?? $activity['name'] ?? 'Atividade do plano ' . $plan->title;
        $description = $activity['description'] ?? '';
        $requiresExtraApproval = (bool) ($activity['requires_approval'] ?? false);
        $ministry = $plan->ministry;
        $requiresAdministrativeApproval = $requiresExtraApproval || ($ministry && $ministry->requires_approval);

        $status = Event::STATUS_PUBLISHED;
        if ($requiresAdministrativeApproval) {
            $status = Event::STATUS_AWAITING_APPROVAL;
        }

        $event = Event::create([
            'title' => $title,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'ministry_id' => $plan->ministry_id,
            'ministry_plan_id' => $plan->id,
            'status' => $status,
            'visibility' => Event::VISIBILITY_PUBLIC,
            'requires_administrative_approval' => $requiresAdministrativeApproval,
            'created_by' => $plan->created_by ?? auth()->id(),
        ]);

        return $event;
    }

    /**
     * Create events for multiple activity indices.
     *
     * @return Event[]
     */
    public function createEventsFromActivities(MinistryPlan $plan, array $indices): array
    {
        $events = [];
        foreach ($indices as $index) {
            $events[] = $this->createEventFromActivity($plan, (int) $index);
        }
        return $events;
    }
}
