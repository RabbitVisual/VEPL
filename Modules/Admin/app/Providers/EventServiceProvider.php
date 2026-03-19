<?php

namespace Modules\Admin\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Admin\App\Listeners\CreateFinancialEntryOnEventRegistrationConfirmed;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        RegistrationConfirmed::class => [
            CreateFinancialEntryOnEventRegistrationConfirmed::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
