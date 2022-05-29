<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Hooks;

use SDRT\CustomFunctions\Support\Mix;
use SDRT\CustomFunctions\VolunteerPortal\ViewModels\Dashboard;
use SDRT\CustomFunctions\VolunteerPortal\ViewModels\Requirements;
use WP_User;

class EnqueueScripts
{
    public function __invoke()
    {
        if ( ! is_user_logged_in() || get_query_var('sdrt-page') !== 'volunteer-portal') {
            return;
        }

        /** @var WP_User $user */
        $user = wp_get_current_user();

        Mix::enqueueScript('volunteer-portal.js');
        Mix::addInlineScript('volunteer-portal.js', 'sdrtVolunteerPortal', [
            'dashboard' => (new Dashboard())->toArray(),
            'requirements' => (new Requirements())->toArray(),
            'user' => [
                'id' => $user->ID,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->user_email,
                'dateOfBirth' => $user->your_date_of_birth,
            ],
            'restApi' => [
                'url' => rest_url(),
                'nonce' => wp_create_nonce('wp_rest'),
            ],
        ]);
    }
}