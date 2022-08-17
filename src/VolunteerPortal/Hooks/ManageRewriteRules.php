<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Hooks;

class ManageRewriteRules
{
    /**
     * Adds the rewrite rules for the volunteer portal.
     */
    public function appendRewriteRules(array $rules): array
    {
        return [
                   '^volunteer-portal/?$' => 'index.php?sdrt-page=volunteer-portal',
                   '^volunteer-portal/([^/]+)/?$' => 'index.php?sdrt-page=volunteer-portal&sdrt-portal-page=$matches[1]',
               ] + $rules;
    }

    /**
     * Adds the query vars for the volunteer portal.
     */
    public function appendQueryVars(array $vars): array
    {
        $vars[] = 'sdrt-page';
        $vars[] = 'sdrt-portal-page';

        return $vars;
    }

    /**
     * Renders the Volunteer Portal page.
     */
    public function renderVolunteerPortal(string $template): string
    {
        if (get_query_var('sdrt-page') !== 'volunteer-portal') {
            return $template;
        }

        if ( ! is_user_logged_in()) {
            wp_redirect(wp_login_url(home_url('volunteer-portal')));
        }

        return SDRT_FUNCTIONS_DIR . 'src/views/volunteer-portal/page.php';
    }
}