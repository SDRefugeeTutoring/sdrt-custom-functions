<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Reports\Repositories;

use DateTimeInterface;
use SDRT\CustomFunctions\Reports\DataTransferObjects\Session;

class ReportsRepository
{
    /**
     * @return Session[]
     */
    public function getSessions(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        string $category = null
    ): array {
        global $wpdb;

        $sessionsData = $wpdb->get_results(
            $wpdb->prepare(
                "
            SELECT
                events.ID AS id,
                events.post_title AS name,
                tm.name AS category,
                rsvpAttendedCount.totalAttend AS totalAttending,
                rsvpAttendedCount.total AS totalAttended,
                startDateMeta.meta_value AS startDate,
                endDateMeta.meta_value AS endDate
            FROM
                wp_posts events
                LEFT JOIN wp_postmeta startDateMeta ON events.ID = startDateMeta.post_id
                    AND startDateMeta.meta_key = '_EventStartDate'
                LEFT JOIN wp_postmeta endDateMeta ON events.ID = endDateMeta.post_id
                    AND endDateMeta.meta_key = '_EventEndDate'
                LEFT JOIN (
                    SELECT
                        events.ID AS eventId,
                        rsvpCounts.attendedRsvps AS total,
                        rsvpAttendCounts.attendRsvps AS totalAttend
                    FROM
                        wp_posts events
                        LEFT JOIN (
                            SELECT
                                eventMeta.meta_value AS eventId,
                                COUNT(rsvps.ID) AS attendedRsvps
                            FROM
                                wp_posts AS rsvps
                                INNER JOIN wp_postmeta AS rsvpMeta ON rsvps.ID = rsvpMeta.post_id
                                LEFT JOIN wp_postmeta AS eventMeta ON rsvps.ID = eventMeta.post_id
                                    AND eventMeta.meta_key = 'event_id'
                            WHERE
                                rsvps.post_type = 'rsvp'
                                AND rsvpMeta.meta_key = 'attended'
                                AND rsvpMeta.meta_value = 'Yes'
                            GROUP BY
                                eventMeta.meta_value) AS rsvpCounts ON events.ID = rsvpCounts.eventId
                        LEFT JOIN (
                            SELECT
                                eventMeta.meta_value AS eventId,
                                COUNT(rsvps.ID) AS attendRsvps
                            FROM
                                wp_posts AS rsvps
                                INNER JOIN wp_postmeta AS rsvpMeta ON rsvps.ID = rsvpMeta.post_id
                                LEFT JOIN wp_postmeta AS eventMeta ON rsvps.ID = eventMeta.post_id
                                    AND eventMeta.meta_key = 'event_id'
                            WHERE
                                rsvps.post_type = 'rsvp'
                                AND rsvpMeta.meta_key = 'attending'
                                AND rsvpMeta.meta_value = 'Yes'
                            GROUP BY
                                eventMeta.meta_value) AS rsvpAttendCounts ON events.ID = rsvpAttendCounts.eventId
                        WHERE
                            events.post_type = 'tribe_events') AS rsvpAttendedCount ON events.ID = rsvpAttendedCount.eventId
                INNER JOIN wp_term_relationships tr ON events.ID = tr.object_id
                INNER JOIN wp_term_taxonomy tx ON tr.term_taxonomy_id = tx.term_taxonomy_id
                INNER JOIN wp_terms tm ON tx.term_id = tm.term_id
            WHERE
                events.post_status = 'publish'
                AND events.post_type = 'tribe_events'
                AND tx.taxonomy = 'tribe_events_cat'
                AND tm.term_id IN(17, 18)
            HAVING
                startDate >= %s
                AND endDate <= %s
        ",
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            ),
            ARRAY_A
        );

        return array_map(
            static function ($sessionData) {
                return Session::fromArray((array)$sessionData);
            },
            $sessionsData
        );
    }
}