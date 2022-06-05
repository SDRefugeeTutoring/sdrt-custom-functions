<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Controllers;

use DateTime;
use SDRT\CustomFunctions\Checkr\Actions\CreateCandidate;
use SDRT\CustomFunctions\Checkr\Actions\CreateInvitation;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User;

class RequirementsEndpoint
{
    private const NAMESPACE = 'sdrt/v1/requirements';

    public function register(): void
    {
        register_rest_route(self::NAMESPACE, '/background-check', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$this, 'requestBackgroundCheck'],
            'permission_callback' => [$this, 'permissionCheck'],
        ]);

        register_rest_route(self::NAMESPACE, '/orientation-rsvp', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$this, 'rsvpToOrientation'],
            'permission_callback' => [$this, 'permissionCheck'],
        ]);
    }

    public function requestBackgroundCheck(WP_REST_Request $request): WP_REST_Response
    {
        /** @var WP_User $user */
        $user = wp_get_current_user();
        $candidateId = get_user_meta($user->ID, 'background_check_candidate_id', true);
        $newCandidate = false;

        if (empty($candidateId)) {
            $dateOfBirth = get_user_meta($user->ID, 'your_date_of_birth', true);
            $newCandidate = true;

            if (empty($dateOfBirth)) {
                return $this->respondWithError('dob_error');
            }

            $candidate = sdrt(CreateCandidate::class)(
                $user->first_name,
                $user->last_name,
                $user->user_email,
                new DateTime($dateOfBirth)
            );

            if (is_wp_error($candidate)) {
                return $this->respondWithError('candidate_error');
            }

            $candidateId = $candidate->id;
            update_user_meta($user->ID, 'background_check_candidate_id', $candidateId);
        }

        if ( ! $newCandidate) {
            $inviteUrl = get_user_meta($user->ID, 'background_check_invite_url', true);
            if ( ! empty($inviteUrl)) {
                update_user_meta($user->ID, 'background_check', 'Invited');
                return $this->respondWithSuccess($inviteUrl);
            }
        }

        $invitation = sdrt(CreateInvitation::class)($candidateId);

        if (is_wp_error($invitation)) {
            return $this->respondWithError('invitation_error');
        }

        update_user_meta($user->ID, 'background_check_invite_url', $invitation->invitationUrl);
        update_user_meta($user->ID, 'background_check', 'Invited');

        return $this->respondWithSuccess($invitation->invitationUrl);
    }

    public function rsvpToOrientation(WP_REST_Request $request): WP_REST_Response {
        $eventId = $request->get_param('eventId');

        /** @var WP_User $user */
        $user = wp_get_current_user();

        if ( !tribe_event_in_category('orientation-dates', $eventId) || user_can_rsvp($user->ID)) {
            return new WP_REST_Response(['reason' => 'event_not_orientation'], 400);
        }

        if ( user_has_event_rsvp($user->ID, $eventId) ) {
            return new WP_REST_Response(['reason' => 'rsvp_already_exists'], 400);
        }

        $rsvp = create_orientation_rsvp($user, tribe_get_event($eventId));

        return new WP_REST_Response($rsvp);
    }

    private function respondWithSuccess(string $inviteUrl): WP_REST_Response
    {
        return new WP_REST_Response(['status' => 'invited', 'inviteUrl' => $inviteUrl], 200);
    }

    private function respondWithError(string $status): WP_REST_Response
    {
        return new WP_REST_Response(['status' => $status, 'inviteUrl' => null], 400);
    }

    public function permissionCheck(): bool
    {
        return is_user_logged_in();
    }
}