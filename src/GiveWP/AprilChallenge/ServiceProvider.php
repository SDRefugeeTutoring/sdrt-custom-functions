<?php

namespace SDRT\CustomFunctions\GiveWP\AprilChallenge;

use SDRT\CustomFunctions\GiveWP\AprilChallenge\Actions\AddFixedAmountToGoal;
use SDRT\CustomFunctions\Support\Contracts\ServiceProvider as ServiceProviderContract;
use SDRT\CustomFunctions\Support\Hooks;

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
        Hooks::addFilter('give_goal_shortcode_stats', AddFixedAmountToGoal::class, '__invoke', 10, 4);
    }
}