<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\GravityForms;

use SDRT\CustomFunctions\GravityForms\Hooks\CreateInvitationForNewPendingVolunteers;
use SDRT\CustomFunctions\Support\Contracts\ServiceProvider;
use SDRT\CustomFunctions\Support\Hooks;

class GravityFormsServiceProvider implements ServiceProvider
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
        Hooks::addAction('gform_user_registered', CreateInvitationForNewPendingVolunteers::class, '__invoke', 10, 4);
    }
}