<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal;

use SDRT\CustomFunctions\Support\Contracts\ServiceProvider as ServiceProviderContract;
use SDRT\CustomFunctions\Support\Hooks;
use SDRT\CustomFunctions\VolunteerPortal\Hooks\EnqueueScripts;
use SDRT\CustomFunctions\VolunteerPortal\Hooks\ManageRewriteRules;

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
        Hooks::addFilter('rewrite_rules_array', ManageRewriteRules::class, 'appendRewriteRules');
        Hooks::addFilter('query_vars', ManageRewriteRules::class, 'appendQueryVars');
        Hooks::addFilter('template_include', ManageRewriteRules::class, 'renderVolunteerPortal');
        Hooks::addAction('wp_enqueue_scripts', EnqueueScripts::class);
    }
}