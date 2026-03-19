<?php

namespace Modules\Admin\App\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Services\TreasuryApiService;

/**
 * Orchestrator for financial auditing of event registrations.
 *
 * - Creates the unique FinancialEntry for confirmed registrations.
 * - Ensures idempotency by reference_number: REG-{registrationId}
 * - Delegates persistence + treasury audit to TreasuryApiService.
 */
class CreateFinancialEntryOnEventRegistrationConfirmed
{
    public function __construct(
        private readonly TreasuryApiService $treasuryApiService
    ) {}

    public function handle(RegistrationConfirmed $event): void
    {
        $registration = $event->registration;

        if ($registration->total_amount <= 0) {
            return;
        }

        $reference = 'REG-' . $registration->id;
        if (FinancialEntry::where('reference_number', $reference)->exists()) {
            return;
        }

        $actorUserId = auth()->user()?->id ?? (int) $registration->user_id;

        $payment = $registration->latestPayment;
        $paymentId = $payment?->id;

        $eventModel = $registration->event;
        $eventTitle = $eventModel?->title ?? '';
        $campaignId = $eventModel?->treasury_campaign_id ?? null;
        $ministryId = $eventModel?->ministry_id ?? null;

        // Category 'campaign' keeps Treasury campaign totals in sync with campaign_id.
        $participantsCount = $registration->participants->count();
        $data = [
            'type' => 'income',
            'category' => 'campaign',
            'title' => "Inscrição: {$eventTitle}",
            'description' => "Inscrição #{$registration->id} para a formação '{$eventTitle}'. Participantes: {$participantsCount}",
            'amount' => $registration->total_amount,
            'entry_date' => ($registration->paid_at ? $registration->paid_at->toDateString() : now()->toDateString()),
            'user_id' => $registration->user_id,
            'payment_id' => $paymentId,
            'payment_method' => $registration->payment_method ?? 'outros',
            'reference_number' => $reference,
            'campaign_id' => $campaignId,
            'ministry_id' => $ministryId,
            'metadata' => [
                'event_id' => $registration->event_id,
                'registration_id' => $registration->id,
                'participants_count' => $participantsCount,
                'event_title' => $eventTitle,
            ],
        ];

        try {
            $this->treasuryApiService->createEntryInternal($data, $actorUserId);
        } catch (\Throwable $e) {
            Log::error("Admin: failed to create treasury entry for registration #{$registration->id}: " . $e->getMessage());
        }
    }
}

