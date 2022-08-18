<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Hooks;

class SetPageTitle
{
    public function __invoke($title)
    {
        if (get_query_var('sdrt-page') !== 'volunteer-portal') {
            return $title;
        }

        return 'Volunteer Portal - ' . get_option('blogname');
    }
}