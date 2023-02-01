<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Reports\Controllers;

use SDRT\CustomFunctions\Support\Mix;

use function SDRT\CustomFunctions\Helpers\view;

class ReportsPage
{
    public function registerPage(): void
    {
        add_menu_page(
            'Reports',
            'Reports',
            'manage_options',
            'sdrt-reports',
            [$this, 'renderPage'],
            'dashicons-chart-line',
            59
        );
    }

    public function renderPage(): void
    {
        echo view('reports/page');
    }

    public function enqueueAssets(string $hook): void
    {
        if ( $hook !== 'toplevel_page_sdrt-reports') {
            return;
        }

        Mix::enqueueScript('reports.js');
        Mix::addInlineScript('reports.js', 'sdrtReports', [
            'restApi' => [
                'url' => rest_url('sdrt/v1/reports/'),
                'nonce' => wp_create_nonce('wp_rest'),
            ],
        ]);
    }
}