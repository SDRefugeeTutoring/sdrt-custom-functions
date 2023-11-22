<?php

namespace SDRT\CustomFunctions\GiveWP\GivingTuesday2023;

use SDRT\CustomFunctions\GiveWP\GivingTuesday2023\Actions\ModifyFormGoalProgress;
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
        Hooks::addFilter('give_goal_shortcode_stats', ModifyFormGoalProgress::class, '__invoke', 10, 4);
    }
}