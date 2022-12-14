<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Controllers;

use DateTime;
use Exception;
use SDRT\CustomFunctions\Checkr\Actions\CreateCandidate;
use SDRT\CustomFunctions\Checkr\Actions\CreateInvitation;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User;

class RequirementsEndpoint
{
    private const NAMESPACE = 'sdrt/v1';

    public function register(): void
    {
        register_rest_route(self::NAMESPACE, 'requirements/background-check', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$this, 'requestBackgroundCheck'],
            'permission_callback' => [$this, 'permissionCheck'],
        ]);

        register_rest_route(self::NAMESPACE, 'requirements/rsvp', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$this, 'rsvpToEvent'],
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

        if (!$newCandidate) {
            $inviteUrl = get_user_meta($user->ID, 'background_check_invite_url', true);
            if (!empty($inviteUrl)) {
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

    public function rsvpToEvent(WP_REST_Request $request): WP_REST_Response
    {
        $eventId = (int)$request->get_param('eventId');
        $attending = (bool)$request->get_param('attending');

        /** @var WP_User $user */
        $user = wp_get_current_user();
        $event = tribe_get_event($eventId);

        if ($event === null || $event->post_type !== 'tribe_events') {
            return new WP_REST_Response(['reason' => 'not_event'], 400);
        }

        if (strtotime($event->_EventStartDate) < current_time('U')) {
            return new WP_REST_Response(['reason' => 'past_event'], 400);
        }

        $categories = tribe_get_event_cat_slugs($eventId);
        if ((in_array('k-5th-grade', $categories, true) || in_array(
                    'middle-high-school',
                    $categories,
                    true
                )) && !user_can_rsvp($user->ID)) {
            return new WP_REST_Response(['reason' => 'cannot_volunteer'], 400);
        }

        if ($attending && event_has_reached_capacity($eventId)) {
            send_wait_list_email($event, $user);
            return new WP_REST_Response(['reason' => 'event_full'], 400);
        }

        $rsvp = get_user_rsvp_for_event($user->ID, $eventId);

        try {
            if ($rsvp) {
                set_rsvp_to_attending($rsvp->ID, $attending);
            } else {
                $rsvp = create_event_rsvp($user, tribe_get_event($eventId), $attending);
            }

            send_rsvp_email($user, $event, $attending);
        } catch (Exception $exception) {
            return new WP_REST_Response(['reason' => 'unexpected_failure'], 500);
        }

        return new WP_REST_Response(['rsvp' => $rsvp, 'eventAtCapacity' => event_has_reached_capacity($eventId)]);
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