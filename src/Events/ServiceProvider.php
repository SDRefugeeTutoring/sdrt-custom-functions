<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Events;

use SDRT\CustomFunctions\Events\Hooks\HideInPersonEventsInEventsArchive;
use SDRT\CustomFunctions\Events\RestApi\AddEventData;
use SDRT\CustomFunctions\Events\RestApi\AddTrimesterQuerySupport;
use SDRT\CustomFunctions\Events\Trimesters\RegisterTrimesters;
use SDRT\CustomFunctions\Support\Hooks;
use SDRT\CustomFunctions\Support\Contracts\ServiceProvider as ServiceProviderContract;

class ServiceProvider implements ServiceProviderContract
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        Hooks::addAction('init', RegisterTrimesters::class, 'register');

        Hooks::addFilter('tribe_rest_events_archive_data', AddEventData::class);
        Hooks::addFilter('tribe_events_archive_get_args', AddTrimesterQuerySupport::class, '__invoke', 10, 3);

        Hooks::addAction('pre_get_posts', HideInPersonEventsInEventsArchive::class);
    }
}