<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Hooks;

use SDRT\CustomFunctions\Support\Mix;
use SDRT\CustomFunctions\VolunteerPortal\ViewModels\Dashboard;

class EnqueueScripts
{
    public function __invoke()
    {
        if (get_query_var('sdrt-page') !== 'volunteer-portal') {
            return;
        }

        Mix::enqueueScript('volunteer-portal.js');
        Mix::addInlineScript('volunteer-portal.js', 'sdrtVolunteerPortal', [
            'dashboard' => (new Dashboard())->toArray(),
        ]);
    }
}