<?php

add_action('wp_ajax_sdrt_new_background_check', function () {
    global $user;

    if ( ! $user instanceof WP_User) {
        wp_send_json_error('Invalid use of action');
    }

    $dateOfBirth = get_user_meta($user->ID, 'your_date_of_birth', true);
    if (empty($dateOfBirth)) {
        wp_send_json_error('User must have date of birth set');
    }

    $candidate = sdrtCreateCheckrCandidate(
        $user->first_name,
        $user->last_name,
        $user->email,
        new DateTime($dateOfBirth)
    );

    if (is_wp_error($candidate)) {
        wp_send_json_error("Failed to create Checkr Candidate: {$candidate->get_error_message()}");
    }

    update_user_meta($user->ID, 'background_check_candidate_id', $candidate->id);

    $invitation = sdrtCreateCheckrInvitation($candidate->id);

    if (is_wp_error($invitation)) {
        wp_send_json_error("Failed to create Checkr Candidate: {$invitation->get_error_message()}");
    }

    update_user_meta($user->ID, 'background_check_invite_url', $invitation->invitation_url);
});