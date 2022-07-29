<?php

require_once SDRT_FUNCTIONS_DIR . 'rsvps/admin_menu.php';
require_once SDRT_FUNCTIONS_DIR . 'rsvps/crons.php';
require_once SDRT_FUNCTIONS_DIR . 'rsvps/ajax.php';
require_once SDRT_FUNCTIONS_DIR . 'rsvps/exporter/Exporter.php';

/**
 * Helper functions for RSVPs
 */

/**
 * Queries the RSVPs
 *
 * @param array $args additional WP_Query args to overload or add
 *
 * @return WP_Post[]|int[]
 */
function get_rsvps(array $args = []): array
{
    return get_posts(
        $args + [
            'post_type' => 'rsvp',
            'post_status' => ['publish'],
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_key' => 'volunteer_name',
            'suppress_filters' => false,
            'posts_per_page' => -1,
            'nopaging' => true,
            'no_found_rows' => true,
        ]
    );
}

/**
 * @return WP_Post[]|int[]
 */
function get_user_rsvps(int $userId, $args = []): array
{
    return get_rsvps(
        [
            'meta_query' => [
                [
                    'key' => 'volunteer_user_id',
                    'value' => $userId,
                ],
            ],
        ] + $args
    );
}

/**
 * @return WP_Post[]|int[]
 */
function get_attending_events(int $userId, array $args = []): array
{
    global $wpdb;

    // queries the event ids for all events the user has RSVP'd to as attending
    $eventIds = $wpdb->get_col(
        $wpdb->prepare(
            '
        SELECT DISTINCT pm.meta_value events from wp_postmeta pm
            INNER JOIN (
                SELECT DISTINCT post_id from wp_postmeta
                     WHERE meta_key = "volunteer_user_id" AND meta_value = %d
            ) pm2 ON pm2.post_id = pm.post_id
            INNER JOIN (
                SELECT post_id from wp_postmeta
                     WHERE meta_key = "attending" AND meta_value = "yes"
            ) pm3 ON pm3.post_id = pm.post_id 
            WHERE pm.meta_key = "event_id"
	',
            $userId
        )
    );

    return tribe_get_events(
        [
            'post__in' => $eventIds,
        ] + $args
    );
}

/**
 * Retrieves the rsvps for a given event
 *
 * @param int|int[] $event_id
 * @param array{attended: bool, attending: boolean}
 * @param array     $args additional WP_Query args to add or overload
 *
 * @return WP_Post[]|int[]
 */
function get_event_rsvps($event_id, array $options = [], array $args = []): array
{
    $meta_query = $args['meta_query'] ?? [];

    if (isset($options['attending'])) {
        $meta_query[] = [
            'key' => 'attending',
            'value' => $options['attending'] ? 'yes' : 'no',
        ];
    }

    if (isset($options['attended'])) {
        $meta_query[] = [
            'key' => 'attended',
            'value' => $options['attended'] ? 'yes' : 'no',
        ];
    }

    $meta_query[] = [
        'key' => 'event_id',
        'value' => $event_id,
        'compare' => is_array($event_id) ? 'IN' : '=',
    ];

    $args['meta_query'] = $meta_query;

    return get_rsvps($args);
}

/**
 * Retrieves the Event for a given RSVP id or post
 *
 * @param int|WP_Post $rsvp
 *
 * @return WP_Post|null
 */
function get_rsvp_event($rsvp): ?WP_Post
{
    $rsvp_id = $rsvp instanceof WP_Post ? $rsvp->ID : (int)$rsvp;
    $event_id = get_post_meta($rsvp_id, 'event_id', true);

    if (empty($event_id)) {
        return null;
    }

    return tribe_get_event($event_id);
}

/**
 * Returns the RSVP for a user for a given event
 */
function get_user_rsvp_for_event(int $user_id, int $event_id): ?WP_Post
{
    $rsvp = get_rsvps([
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => 'event_id',
                'value' => $event_id,
            ],
            [
                'key' => 'volunteer_user_id',
                'value' => $user_id,
            ],
        ],
    ]);

    return empty($rsvp) ? null : $rsvp[0];
}

/**
 * Checks to see if the given user passes all requirements to be able to RSVP
 */
function user_can_rsvp(int $user_id): bool
{
    $role = user_can($user_id, 'can_rsvp');
    $orientation = get_user_meta($user_id, 'sdrt_orientation_attended', true);
    $coc = get_user_meta($user_id, 'sdrt_coc_consented', true);
    $waiver = get_user_meta($user_id, 'sdrt_waiver_consented', true);
    $background_check = get_user_meta($user_id, 'background_check', true);

    return $role
           && $background_check === 'Yes'
           && $orientation === 'Yes'
           && $coc === 'Yes'
           && $waiver === 'Yes';
}

function user_has_event_rsvp(int $userId, int $eventId): bool
{
    return (bool)get_user_rsvp_for_event($userId, $eventId);
}

function user_is_attending_event(int $userId, int $eventId, $attending = true): bool
{
    $rsvp = get_user_rsvp_for_event($userId, $eventId);

    if ($rsvp === null) {
        return ! $attending;
    }

    return $attending
        ? $rsvp->attending === 'yes'
        : $rsvp->attending !== 'yes';
}

function user_is_not_attending_event(int $userId, int $eventId): bool
{
    return user_is_attending_event($userId, $eventId, false);
}

function set_rsvp_to_attending(int $rsvpId, bool $attending = true)
{
    update_post_meta($rsvpId, 'attending', $attending ? 'yes' : 'no');
}

function create_event_rsvp(WP_User $user, WP_Post $event, $attending = true): WP_Post
{
    $rsvpId = wp_insert_post([
        'post_type' => 'rsvp',
        'post_author' => $user->ID,
        'post_title' => $event->post_title,
        'post_status' => 'publish',
        'post_content' => "RSVP to $event->post_title for $user->display_name",
        'meta_input' => [
            'volunteer_user_id' => $user->ID,
            'volunteer_name' => "$user->last_name, $user->first_name",
            'volunteer_email' => $user->user_email,
            'event_id' => $event->ID,
            'event_name' => $event->post_title,
            'event_date' => $event->start_date,
            'rsvp_date' => (new DateTime())->format('Ymd'),
            'attended' => 'no',
            'attending' => $attending ? 'yes' : 'no',
        ],
    ]);

    if (is_wp_error($rsvpId)) {
        throw new Exception("Failed to create RSVP: {$rsvpId->get_error_message()}");
    }

    return get_post($rsvpId);
}

function send_rsvp_email(WP_User $user, WP_Post $event, bool $attending)
{
    if ($attending) {
        $subject_will = 'WILL';
    } else {
        $subject_will = 'will NOT';
    }

    $subject = "RSVP: $user->first_name $user->last_name $subject_will attend the $event->post_title";

    if (strlen($subject) > 70) {
        $subject = substr($subject, 0, 70);
    }

    sdrt_mail(
        'info@sdrefugeetutoring.com',
        $subject,
        view('mail/send-admin-rsvp-notice', ['user' => $user, 'event' => $event, 'attending' => $attending])
    );
}