<?php

require_once( SDRT_FUNCTIONS_DIR . '/rsvps/attendance.php');
require_once( SDRT_FUNCTIONS_DIR . '/rsvps/admin_menu.php');

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
 * @param int   $event_id
 * @param array $args additional WP_Query args to add or overload
 *
 * @return WP_Post[]|int[]
 */
function get_event_rsvps(int $event_id, array $args = []): array
{
    $meta_query = $args['meta_query'] ?: [];

    $meta_query[] = [
        'key'   => 'event_id',
        'value' => $event_id,
    ];

    $args['meta_query'] = $meta_query;

    return get_rsvps($args);
}