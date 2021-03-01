<?php

function sdrt_rsvp_email_no_show()
{
    $nonce = $_POST['nonce'] ?? null;
    if (empty($nonce) || ! wp_verify_nonce($nonce, 'sdrt_attendance_nonce') || ! current_user_can('can_view_rsvps')) {
        wp_send_json_error(null, 403);
    }

    $rsvp_id = $_POST['rsvp_id'] ?? null;
    if (empty($rsvp_id)) {
        wp_send_json_error('Argument missing', 400);
    }

    $event = get_rsvp_event($rsvp_id);
    if ($event === null) {
        wp_send_json_error('Invalid RSVP', 400);
    }

    $volunteer_name  = get_post_meta($rsvp_id, 'volunteer_name', true);
    $volunteer_email = get_post_meta($rsvp_id, 'volunteer_email', true);

    wp_mail(
        $volunteer_email,
        "We missed you at {$event->post_title}",
        sdrt_send_email([
            'option'      => 'sdrt_rsvp_no_show',
            'fname'       => trim(explode(',', $volunteer_name)[1] ?: ''),
            'event_title' => $event->post_title,
        ]),
        [
            'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
        ]
    );

    wp_send_json_success();
}

add_action('wp_ajax_sdrt_rsvp_email_no_show', 'sdrt_rsvp_email_no_show');