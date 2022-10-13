<?php
/**
 *   MAIN RSVP FORM FUNCTION
 *
 *   This hooks into the Events Calendar single template
 *   It conditionally shows the login/registration
 *   Then the RSVP form for logged-in users
 *   It also outputs the RSVP table for Registration
 *
 */

use function SDRT\CustomFunctions\Helpers\view;

add_action('tribe_events_single_event_after_the_meta', function () {
    global $post;
    $event = tribe_get_event($post);

    // Only output if RSVPs are enabled for this Event
    if ($event->enable_rsvps !== 'enabled') {
        return;
    }

    $rsvp_form = get_post_meta(get_the_ID(), 'rsvp_form', true);
    $is_orientation = (int)$rsvp_form === SDRT_ORIENTATION_FORM;
    $rsvps = get_event_rsvps($post->ID, $is_orientation ? [] : ['attending' => true]);
    $user_id = get_current_user_id();

    echo view('events/rsvp-form', [
        'event' => $event,
        'eventForm' => $event->rsvp_form,
        'rsvpLimit' => absint($event->rsvps_limit),
        'rsvpTotal' => count($rsvps),
        'mustLogin' => $event->logged_in_status !== 'no',
        'eventUrl' => $event->permalink,
        'userId' => $user_id,
        'userIsLeader' => current_user_can('edit_rsvps'),
        'userHasRsvpd' => get_user_rsvp_for_event($user_id, $post->ID) !== null,
    ]);

    // Display the RSVP list for volunteer coordinators
    if (empty($rsvps) || ! current_user_can('can_view_rsvps')) {
        return;
    }

    $rsvp_nonce = wp_create_nonce('sdrt_attendance_nonce');
    echo view('events/rsvp-list', [
        'rsvps' => $rsvps,
        'event' => $event,
        'exportData' => [
            'nonce' => $rsvp_nonce,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'eventName' => $event->post_title,
            'rsvps' => array_map(static function($rsvp) {
                return [
                    'volunteerName' => $rsvp->volunteer_name,
                    'volunteerEmail' => $rsvp->volunteer_email,
                    'attended' => $rsvp->attended,
                ];
            }, $rsvps),
        ],
    ]);
});