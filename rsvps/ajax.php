<?php

/**
 * Handles request to send an email to attendees that did not show
 */
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
        'We Missed You!',
        sdrt_send_email([
            'option'      => 'sdrt_rsvp_no_show',
            'fname'       => trim(explode(',', $volunteer_name)[1] ?: ''),
            'event_title' => $event->post_title,
        ]),
        [
            'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
            'Content-Type: text/html',
        ]
    );

    wp_send_json_success();
}

add_action('wp_ajax_sdrt_rsvp_email_no_show', 'sdrt_rsvp_email_no_show');

/**
 * Handles requests to set RSVP attendance
 */
function sdrt_rsvp_set_event_attendance()
{
    if ( ! (current_user_can('can_view_rsvps') || current_user_can('can_rsvp'))) {
        wp_send_json_error(null, 403);
    }

    if (empty($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'sdrt_attendance_nonce')) {
        wp_send_json_error(null, 403);
    }

    if (empty($_POST['rsvp_id']) || ! isset($_POST['attended'])) {
        wp_send_json_error('Invalid RSVP', 400);
    }

    $rsvp_id  = (int)$_POST['rsvp_id'];
    $attended = ! empty($_POST['attended']);

    update_post_meta($rsvp_id, 'attended', $attended ? 'yes' : 'no');

    // Only send the email if the person is set as attended and the email hasn't already been sent
    if ( ! $attended || get_post_meta($rsvp_id, 'attended_email_sent')) {
        wp_send_json_success();
    }

    $event = get_rsvp_event($rsvp_id);
    if ($event === null) {
        wp_send_json_error('Invalid RSVP', 400);
    }

    $volunteer_name  = get_post_meta($rsvp_id, 'volunteer_name', true);
    $volunteer_email = get_post_meta($rsvp_id, 'volunteer_email', true);

    wp_mail(
        $volunteer_email,
        "Thank you for attending {$event->post_title}",
        sdrt_send_email([
            'option'      => 'sdrt_thanks_for_attending',
            'fname'       => trim(explode(',', $volunteer_name)[1] ?: ''),
            'event_title' => $event->post_title,
        ]),
        [
            'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>',
            'Content-Type: text/html',
        ]
    );

    add_post_meta($rsvp_id, 'attended_email_sent', 1, true);

    wp_send_json_success();
}

add_action('wp_ajax_sdrt_set_event_attendance', 'sdrt_rsvp_set_event_attendance');
