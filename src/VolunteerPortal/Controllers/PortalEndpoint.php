<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\Controllers;

use SDRT\CustomFunctions\VolunteerPortal\ViewModels\NextEvent;
use function SDRT\CustomFunctions\Helpers\Events\get_next_user_event;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User;

class PortalEndpoint
{
    private const NAMESPACE = 'sdrt/v1';

    public function register(): void
    {
        register_rest_route(self::NAMESPACE, 'portal/cancel-rsvp/(?P<id>\d+)', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$this, 'cancelRsvp'],
            'permission_callback' => [$this, 'checkLoggedInPermission'],
        ]);
    }

    public function cancelRsvp(WP_REST_Request $request): WP_REST_Response
    {
        $eventId = $request->get_param('id');
        $event = tribe_get_event($eventId);

        /** @var WP_User $user */
        $user = wp_get_current_user();

        if ( ! $event) {
            return new WP_REST_Response([
                'message' => 'Event not found',
            ], 404);
        }

        $rsvp = get_user_rsvp_for_event($user->ID, $event->ID);

        if ( ! $rsvp) {
            return new WP_REST_Response([
                'message' => 'RSVP not found',
            ], 404);
        }

        if ((int)$rsvp->volunteer_user_id !== $user->ID) {
            return new WP_REST_Response([
                'message' => 'You do not have permission to cancel this RSVP',
            ], 403);
        }

        set_rsvp_to_attending($rsvp->ID, false);
        send_rsvp_email($user, $event, false);

        $nextEvent = get_next_user_event($user->ID);

        if ( $nextEvent === null ) {
            return new WP_REST_Response(['nextEvent' => null], 200);
        }

        return new WP_REST_Response(['nextEvent' => (new NextEvent($nextEvent))->toArray()], 200);
    }

    public function checkLoggedInPermission(WP_REST_Request $request): bool
    {
        return is_user_logged_in();
    }
}