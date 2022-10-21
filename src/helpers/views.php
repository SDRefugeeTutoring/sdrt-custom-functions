<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Helpers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

function view(string $view, array $data = []): string
{
    static $twig = null;

    if ( $twig === null ) {
        $loader = new FilesystemLoader(SDRT_FUNCTIONS_DIR . '/src/views');

        $twig = new Environment($loader, [
//            'cache' => wp_get_environment_type() === 'local' ? false : SDRT_FUNCTIONS_DIR . 'src/views/cache',
            'cache' => false,
        ]);

        // WordPress functions
        $twig->addFunction(new TwigFunction('isUserLoggedIn', 'is_user_logged_in'));
        $twig->addFunction(new TwigFunction('siteUrl', 'site_url'));
        $twig->addFunction(new TwigFunction('wpLoginUrl', 'wp_login_url'));
        $twig->addFunction(new TwigFunction('getCurrentUserId', 'get_current_user_id'));
        $twig->addFunction(new TwigFunction('getDeletePostLink', 'get_delete_post_link'));

        $twig->addFunction(new TwigFunction('userCanRsvp', 'user_can_rsvp'));
        $twig->addFunction(new TwigFunction('getUserRsvpForEvent', 'get_user_rsvp_for_event'));
        $twig->addFunction(new TwigFunction('gravityForm', 'gravity_form'));
        $twig->addFunction(new TwigFunction('getEventRsvpCount', 'get_event_rsvp_count'));
    }

    return $twig->load("$view.html.twig")->render($data);
}