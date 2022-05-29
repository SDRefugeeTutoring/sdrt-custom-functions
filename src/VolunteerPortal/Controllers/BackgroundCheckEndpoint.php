<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Controllers;

use DateTime;
use SDRT\CustomFunctions\Checkr\Actions\CreateCandidate;
use SDRT\CustomFunctions\Checkr\Actions\CreateInvitation;
use WP_REST_Request;
use WP_REST_Response;
use WP_User;

class BackgroundCheckEndpoint
{
    private const NAMESPACE = 'sdrt/v1/background-check';

    public function register(): void
    {
        register_rest_route(self::NAMESPACE, '/', [
            'methods' => 'POST',
            'callback' => [$this, 'requestBackgroundCheck'],
            'permission_callback' => [$this, 'permissionCheck'],
        ]);
    }

    public function requestBackgroundCheck(WP_REST_Request $request): WP_REST_Response
    {
        /** @var WP_User $user */
        $user = wp_get_current_user();
        $candidateId = get_user_meta($user->ID, 'background_check_candidate_id', true);

        if (empty($candidateId)) {
            $dateOfBirth = get_user_meta($user->ID, 'your_date_of_birth', true);

            if (empty($dateOfBirth)) {
                return new WP_REST_Response(['status' => 'dob_error'], 400);
            }

            $candidate = sdrt(CreateCandidate::class)(
                $user->first_name,
                $user->last_name,
                $user->user_email,
                new DateTime($dateOfBirth)
            );

            if (is_wp_error($candidate)) {
                return new WP_REST_Response(['status' => 'candidate_error'], 400);
            }

            $candidateId = $candidate->id;
            update_user_meta($user->ID, 'background_check_candidate_id', $candidateId);
        }

        $inviteUrl = get_user_meta($user->ID, 'background_check_invite_url', true);
        if ( ! empty($inviteUrl)) {
            return new WP_REST_Response(['status' => 'invited'], 400);
        }

        $invitation = sdrt(CreateInvitation::class)($candidateId);

        if (is_wp_error($invitation)) {
            return new WP_REST_Response(['status' => 'invitation_error'], 400);
        }

        update_user_meta($user->ID, 'background_check_invite_url', $candidateId);

        return new WP_REST_Response(['status' => 'invited'], 400);
    }

    public function permissionCheck(): bool
    {
        return is_user_logged_in();
    }
}