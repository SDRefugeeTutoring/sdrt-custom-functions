<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\ViewModels;

use WP_User;

class Dashboard
{
    public function toArray(): array
    {
        return [
//            'message' => [
//                'text' => 'We take the safety and health of our students very seriously. At the same time, we are extremely fortunate to have such a generous group of volunteers eager and willing to tutor. It\'s our goal to ensure the safety of our students and also make volunteering have as low a barrier to entry as possible.',
//                'urgency' => 'urgent',
//            ],
            'message' => null,
            'nextEvent' => $this->getNextEvent(),
            'volunteerStats' => $this->getVolunteerStats(),
        ];
    }

    private function getNextEvent(): ?array
    {
        $nextEvent = get_attending_events(get_current_user_id(), [
            'posts_per_page' => 1,
        ]);

        if (empty($nextEvent)) {
            return null;
        }

        $event = $nextEvent[0];

        $categories = wp_get_post_terms($event->ID, 'tribe_events_cat', [
            'number' => 1,
            'fields' => 'names',
        ]);

        $hasVenue = tribe_has_venue($event->ID);

        return [
            'eventId' => $event->ID,
            'name' => $event->post_title,
            'date' => $event->event_date,
            'category' => empty($categories) ? null : $categories[0],
            'organizer' => tribe_get_organizer($event->ID),
            'location' => $hasVenue ? [
                'name' => tribe_get_venue($event->ID),
                'address' => tribe_get_address($event->ID) . ' ' . tribe_get_city($event->ID) . ', ' . tribe_get_region(
                        $event->ID
                    ) . ' ' . tribe_get_zip($event->ID),
            ] : null,
        ];
    }

    private function getVolunteerStats(): array
    {
        global $wpdb;

        /** @var WP_User $user */
        $user = wp_get_current_user();

        $eventsAttended = $wpdb->get_var(
            $wpdb->prepare(
                '
                    SELECT COUNT(DISTINCT pm.meta_value) from wp_postmeta pm
                        INNER JOIN (
                            SELECT DISTINCT post_id from wp_postmeta
                                 WHERE meta_key = "volunteer_user_id" AND meta_value = %d
                        ) pm2 ON pm2.post_id = pm.post_id
                        INNER JOIN (
                            SELECT post_id from wp_postmeta
                                 WHERE meta_key = "attended" AND meta_value = "yes"
                        ) pm3 ON pm3.post_id = pm.post_id 
                        WHERE pm.meta_key = "event_id"
	            ',
                $user->ID
            )
        );

        return [
            'startDate' => $user->user_registered,
            'eventsAttended' => absint($eventsAttended),
            'totalHours' => 123,
            'currentTrimesterAttendanceRate' => 1,
            'previousTrimesterAttendanceRate' => 0.75,
        ];
    }
}