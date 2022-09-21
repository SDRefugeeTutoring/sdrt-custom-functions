<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Helpers\Events;

use WP_Post;

/**
 * @return WP_Post[]|int[]
 */
function get_attending_events(int $userId, array $args = []): array
{
    global $wpdb;

    // queries the event ids for all events the user has RSVP'd to as attending
    $eventIds = $wpdb->get_col(
        $wpdb->prepare(
            "
                    SELECT DISTINCT
                        pm.meta_value
                    FROM
                        $wpdb->posts p
                        JOIN $wpdb->postmeta AS pm ON p.ID = pm.post_id
                            AND pm.meta_key = 'event_id'
                        JOIN $wpdb->postmeta AS pm2 ON p.ID = pm2.post_id
                            AND pm2.meta_key = 'volunteer_user_id'
                        JOIN $wpdb->postmeta AS pm3 ON p.ID = pm3.post_id
                            AND pm3.meta_key = 'attending'
                    WHERE
                        p.post_type = 'rsvp'
                        AND p.post_status = 'publish'
                        AND pm2.meta_value = %d
                        AND pm3.meta_value = 'yes'
	        ",
            $userId
        )
    );

    if (empty($eventIds)) {
        return [];
    }

    return tribe_get_events(
        [
            'post__in' => $eventIds,
            'ends_after' => 'now',
        ] + $args
    );
}

function get_next_user_event(int $userId): ?WP_Post
{
    $events = get_attending_events($userId, [
        'posts_per_page' => 1,
    ]);

    return $events[0] ?? null;
}

/**
 * Retrieves the category name for a given event
 */
function get_event_category_name(int $eventId): ?string
{
    $categories = wp_get_post_terms($eventId, 'tribe_events_cat', [
        'fields' => 'names',
        'number' => 1,
    ]);

    return $categories[0] ?? null;
}