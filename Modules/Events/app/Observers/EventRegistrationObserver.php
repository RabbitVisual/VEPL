<?php

namespace Modules\Events\App\Observers;

use Modules\Events\App\Models\EventRegistration;
use Illuminate\Support\Str;

class EventRegistrationObserver
{
    /**
     * Handle the EventRegistration "creating" event.
     */
    public function creating(EventRegistration $registration): void
    {
        // Generate UUID if not provided
        if (empty($registration->uuid)) {
            $registration->uuid = (string) Str::uuid();
        }

        // Generate registration number if not provided
        if (empty($registration->registration_number)) {
            $year = date('Y');
            $eventId = $registration->event_id;
            
            // Get next sequential number for this event in this year
            $lastRegistration = EventRegistration::where('event_id', $eventId)
                ->where('registration_number', 'LIKE', "VEPL-{$year}-{$eventId}-%")
                ->orderBy('registration_number', 'desc')
                ->first();
            
            $nextNumber = 1;
            if ($lastRegistration) {
                $parts = explode('-', $lastRegistration->registration_number);
                if (count($parts) >= 4) {
                    $nextNumber = intval(end($parts)) + 1;
                }
            }
            
            $registration->registration_number = sprintf('VEPL-%s-%s-%04d', $year, $eventId, $nextNumber);
        }

        // Set required fields based on event configuration
        if (empty($registration->responsible_name) && $registration->user) {
            $registration->responsible_name = $registration->user->name;
        }

        if (empty($registration->responsible_email) && $registration->user) {
            $registration->responsible_email = $registration->user->email;
        }
    }

    /**
     * Handle the EventRegistration "updating" event.
     */
    public function updating(EventRegistration $registration): void
    {
        // Update last significant change timestamp on important status changes
        if ($registration->isDirty('status') || $registration->isDirty('total_amount')) {
            $registration->last_significant_change = now();
        }
    }
}