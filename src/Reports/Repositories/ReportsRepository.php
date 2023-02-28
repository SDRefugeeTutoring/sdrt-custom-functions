<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\Reports\Repositories;

use DateTimeInterface;
use SDRT\CustomFunctions\Reports\DataTransferObjects\EventVolunteer;
use SDRT\CustomFunctions\Reports\DataTransferObjects\Session;
use SDRT\CustomFunctions\Reports\DataTransferObjects\Volunteer;

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

    /**
     * @return array<EventVolunteer>
     */
    public function getEventVolunteers(int $eventId): array
    {
        global $wpdb;

        $volunteerData = $wpdb->get_results(
            $wpdb->prepare(
                "
                    SELECT
                        rsvps.ID as id,
                        rsvpNameMeta.meta_value AS name,
                        rsvpAttendMeta.meta_value AS rsvp,
                        rsvpAttendedMeta.meta_value AS attended
                    FROM
                        $wpdb->posts AS rsvps
                        INNER JOIN $wpdb->postmeta AS eventMeta ON rsvps.ID = eventMeta.post_id
                            AND eventMeta.meta_key = 'event_id'
                        LEFT JOIN $wpdb->postmeta AS rsvpNameMeta ON rsvps.ID = rsvpNameMeta.post_id
                            AND rsvpNameMeta.meta_key = 'volunteer_name'
                        LEFT JOIN $wpdb->postmeta AS rsvpAttendMeta ON rsvps.ID = rsvpAttendMeta.post_id
                            AND rsvpAttendMeta.meta_key = 'attending'
                        LEFT JOIN $wpdb->postmeta AS rsvpAttendedMeta ON rsvps.ID = rsvpAttendedMeta.post_id
                            AND rsvpAttendedMeta.meta_key = 'attended'
                    WHERE
                        rsvps.post_type = 'rsvp'
                        AND eventMeta.meta_value = %d
                ",
                $eventId
            ),
            ARRAY_A
        );

        return array_map(
            static function ($volunteer) {
                return EventVolunteer::fromArray($volunteer);
            },
            $volunteerData
        );
    }

    /**
     * @return Volunteer[]
     */
    public function getVolunteers(): array
    {
        global $wpdb;

        $volunteers = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT
                    volunteers.ID AS id,
                    volunteers.user_email AS email,
                    volunteers.user_registered AS joinDate,
                    sessions.totalSessions,
                    sessions.totalK5,
                    sessions.totalMiddleHigh,
                    sessions.firstSessionDate,
                    sessions.latestSessionDate,
                    TIMESTAMPDIFF(YEAR, sessions.firstSessionDate, sessions.latestSessionDate) AS yearsActive
                FROM
                    wp_users AS volunteers
                    INNER JOIN (
                        SELECT
                            rsvpUserIdMeta.meta_value AS userId
                        FROM
                            wp_posts AS rsvps
                            LEFT JOIN wp_postmeta AS rsvpUserIdMeta ON rsvps.ID = rsvpUserIdMeta.post_id
                                AND rsvpUserIdMeta.meta_key = 'volunteer_user_id'
                        WHERE
                            rsvps.post_type = 'rsvp'
                        GROUP BY
                            userId) AS rsvps ON volunteers.ID = rsvps.userId
                    LEFT JOIN (
                        SELECT
                            MIN(startDateMeta.meta_value) AS firstSessionDate,
                            MAX(startDateMeta.meta_value) AS latestSessionDate,
                            COUNT(events.ID) AS TotalSessions,
                            SUM(IF(tm.term_id = 17, 1, 0)) AS totalK5,
                            SUM(IF(tm.term_id = 18, 1, 0)) AS totalMiddleHigh,
                            rsvps.userId
                        FROM
                            wp_posts events
                            LEFT JOIN wp_postmeta startDateMeta ON events.ID = startDateMeta.post_id
                                AND startDateMeta.meta_key = '_EventStartDate'
                            LEFT JOIN (
                                SELECT
                                    eventMeta.meta_value AS eventId,
                                    rsvpUserMeta.meta_value AS userId
                                FROM
                                    wp_posts AS rsvps
                                    INNER JOIN wp_postmeta AS rsvpMeta ON rsvps.ID = rsvpMeta.post_id
                                        AND rsvpMeta.meta_key = 'attended'
                                        AND rsvpMeta.meta_value = 'Yes'
                                LEFT JOIN wp_postmeta AS eventMeta ON rsvps.ID = eventMeta.post_id
                                    AND eventMeta.meta_key = 'event_id'
                                LEFT JOIN wp_postmeta AS rsvpUserMeta ON rsvps.ID = rsvpUserMeta.post_id
                                    AND rsvpUserMeta.meta_key = 'volunteer_user_id'
                                WHERE
                                    rsvps.post_type = 'rsvp'
                                GROUP BY
                                    eventMeta.meta_value,
                                    rsvpUserMeta.meta_value) AS rsvps ON events.ID = rsvps.eventId
                            INNER JOIN wp_term_relationships tr ON events.ID = tr.object_id
                            INNER JOIN wp_term_taxonomy tx ON tr.term_taxonomy_id = tx.term_taxonomy_id
                            INNER JOIN wp_terms tm ON tx.term_id = tm.term_id
                        WHERE
                            events.post_status = 'publish'
                            AND events.post_type = 'tribe_events'
                            AND tx.taxonomy = 'tribe_events_cat'
                            AND tm.term_id IN(3, 17, 18)
                        GROUP BY rsvps.userId
                    ) AS sessions ON volunteers.ID = sessions.userId
                ORDER BY volunteers.ID
            "
            )
            , ARRAY_A
        );

        return array_map(
            static function (array $volunteer) {
                return Volunteer::fromArray($volunteer);
            },
            $volunteers
        );
    }
}