<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Events;

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
    }
}