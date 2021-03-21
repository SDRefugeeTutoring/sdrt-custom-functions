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
    return get_posts($args + [
            'post_type'        => 'rsvp',
            'post_status'      => ['publish'],
            'order'            => 'ASC',
            'orderby'          => 'meta_value',
            'meta_key'         => 'volunteer_name',
            'suppress_filters' => false,
            'posts_per_page'   => -1,
            'nopaging'         => true,
            'no_found_rows'    => true,
        ]);
}

/**
 * Retrieves the rsvps for a given event
 *
 * @param int|int[] $event_id
 * @param array     $args additional WP_Query args to add or overload
 *
 * @return WP_Post[]|int[]
 */
function get_event_rsvps($event_id, array $args = []): array
{
    $meta_query = $args['meta_query'] ?? [];

    $meta_query[] = [
        'key'     => 'event_id',
        'value'   => $event_id,
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
    $rsvp_id  = $rsvp instanceof WP_Post ? $rsvp->ID : (int)$rsvp;
    $event_id = get_post_meta($rsvp_id, 'event_id', true);

    if (empty($event_id)) {
        return null;
    }

    return tribe_get_event($event_id);
}