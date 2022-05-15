<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Hooks;

use WP_Post;
use WP_Theme;

class RegisterPageTemplate
{
    public function __invoke(array $templates, WP_Theme $theme, WP_Post $post): array
    {
        if ( $post->post_type !== 'page' ) {
            return $templates;
        }

        $templates['sdrt-volunteer-portal.php'] = 'Volunteer Portal';

        return $templates;
    }
}