<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Reports;

use SDRT\CustomFunctions\Reports\Controllers\ReportsPage;
use SDRT\CustomFunctions\Support\Contracts\ServiceProvider;
use SDRT\CustomFunctions\Support\Hooks;

class ReportsServiceProvider implements ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Hooks::addAction('admin_menu', ReportsPage::class, 'registerPage');
        Hooks::addAction('admin_enqueue_scripts', ReportsPage::class, 'enqueueAssets');
    }
}