<?php

/**
 * Checks for any events happening tomorrow. If there are any, then it sends a reminder email to all volunteers that
 * have provided an RSVP and are planning to attend.
 */
function event_rsvp_send_reminder()
{
    $tomorrow = new DateTime('+1 day', wp_timezone());

    $events_tomorrow = tribe_get_events([
        'eventDisplay' => 'day',
        'eventDate'    => $tomorrow->format('Y-m-d'),
        'meta_query'   => [
            [
                'key'   => 'rsvp_send_reminder',
                'value' => 'yes',
            ],
        ],
    ]);

    if (empty($events_tomorrow)) {
        return;
    }

    foreach ($events_tomorrow as $event) {
        $rsvps = get_event_rsvps($event->ID, [
            'meta_query' => [
                [
                    'key'   => 'attending',
                    'value' => 'yes',
                ],
            ],
            'fields'     => 'ids',
        ]);

        foreach ($rsvps as $rsvp_id) {
            $volunteer_name  = get_post_meta($rsvp_id, 'volunteer_name', true);
            $volunteer_email = get_post_meta($rsvp_id, 'volunteer_email', true);

            if (empty($volunteer_email)) {
                continue;
            }

            wp_mail(
                $volunteer_email,
                "Reminder for {$event->post_title} tomorrow",
                sdrt_send_email([
                    'option'      => 'sdrt_rsvp_upcoming_reminder',
                    'fname'       => trim(explode(',', $volunteer_name)[1] ?: ''),
                    'event_title' => $event->post_title,
                ]),
                [
                    'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
                ]
            );
        }
    }
}
