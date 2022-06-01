<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal;

use SDRT\CustomFunctions\Support\Contracts\ServiceProvider as ServiceProviderContract;
use SDRT\CustomFunctions\Support\Hooks;
use SDRT\CustomFunctions\VolunteerPortal\Controllers\ProfileRestPoint;
use SDRT\CustomFunctions\VolunteerPortal\Hooks\EnqueueScripts;
use SDRT\CustomFunctions\VolunteerPortal\Hooks\ManageRewriteRules;
use SDRT\CustomFunctions\VolunteerPortal\Hooks\RegisterVolunteerMeta;

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
//        Hooks::addAction('rest_api_init', ProfileRestPoint::class, 'register');

        Hooks::addAction('init', RegisterVolunteerMeta::class);
        Hooks::addFilter('rewrite_rules_array', ManageRewriteRules::class, 'appendRewriteRules');
        Hooks::addFilter('query_vars', ManageRewriteRules::class, 'appendQueryVars');
        Hooks::addFilter('template_include', ManageRewriteRules::class, 'renderVolunteerPortal');
        Hooks::addAction('wp_enqueue_scripts', EnqueueScripts::class);
    }
}