<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Controllers;

use WP_REST_Request;
use WP_REST_Response;

class ProfileRestPoint
{
    private const NAMESPACE = 'sdrt/v1/portal';

    public function register(): void
    {
        register_rest_route(self::NAMESPACE, '/profile', [
            'methods' => 'POST',
            'callback' => [$this, 'updateProfile'],
            'validate_callback' => [$this, 'validateProfile'],
            'permission_callback' => [$this, 'updateProfilePermission'],
        ]);
    }

    public function updateProfile(WP_REST_Request $request): WP_REST_Response
    {
        /** @var WP_User $user */
        $user = wp_get_current_user();

        $user->first_name = $request->get_param('firstName');
        $user->last_name = $request->get_param('lastName');
        $user->user_email = $request->get_param('email');

        $update = wp_update_user($user);

        if ( is_wp_error($update) ) {
            return new WP_REST_Response($update, 500);
        }

        return new WP_REST_Response($user->to_array(), 200);
    }

    public function validateProfile(WP_REST_Request $request): bool
    {
        if ( ! $request->get_param('firstName') || ! $request->get_param('lastName') || ! $request->get_param('email')) {
            return false;
        }

        if ( $request->get_param('password') && $request->get_param('password') !== $request->get_param('passwordConfirm')) {
            return false;
        }

        return true;
    }

    public function updateProfilePermission(WP_REST_Request $request): bool
    {
        return is_user_logged_in();
    }
}