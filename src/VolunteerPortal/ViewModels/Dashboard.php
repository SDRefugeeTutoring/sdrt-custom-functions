<?php

declare(strict_types=1);

namespace SDRT\CustomFunctions\VolunteerPortal\ViewModels;

use WP_User;

use function SDRT\CustomFunctions\Helpers\Events\get_event_category_name;
use function SDRT\CustomFunctions\Helpers\Events\get_next_user_event;

class Dashboard
{
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'nextEvent' => $this->getNextEvent(),
            'volunteerStats' => $this->getVolunteerStats(),
        ];
    }

    private function getMessage(): ?array {
        if ( empty(get_option('sdrt_volunteer_message_enabled'))) {
            return null;
        }

        return [
            'heading' => wp_kses_post(get_option('sdrt_volunteer_message_heading')),
            'text' => wp_kses_post(get_option('sdrt_volunteer_message')),
            'urgency' => get_option('sdrt_volunteer_message_urgency'),
        ];
    }

    private function getNextEvent(): ?array
    {
        $event = get_next_user_event(get_current_user_id());

        if ($event === null) {
            return null;
        }

        return (new NextEvent($event))->toArray();
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