<?php

use SDRT\CustomFunctions\Checkr\Actions\CreateCandidate;
use SDRT\CustomFunctions\Checkr\Actions\CreateInvitation;

add_action('wp_ajax_sdrt_new_background_check', function () {
    if (empty($_GET['user_id'])) {
        wp_send_json_error('Invalid use of action');
    }

    $user = get_user_by('id', $_GET['user_id']);

    if ( ! $user instanceof WP_User) {
        wp_send_json_error('Invalid use of action');
    }

    $candidateId = get_user_meta($user->ID, 'background_check_candidate_id', true);

    if (empty($candidateId)) {
        $dateOfBirth = get_user_meta($user->ID, 'your_date_of_birth', true);
        if (empty($dateOfBirth)) {
            wp_send_json_error('User must have date of birth set');
        }

        $candidate = sdrt(CreateCandidate::class)(
            $user->first_name,
            $user->last_name,
            $user->user_email,
            new DateTime($dateOfBirth)
        );

        if (is_wp_error($candidate)) {
            wp_send_json_error("Failed to create Checkr Candidate: {$candidate->get_error_message()}");
        }

        $candidateId = $candidate->id;
        update_user_meta($user->ID, 'background_check_candidate_id', $candidate->id);
    }

    $invitation = sdrt(CreateInvitation::class)($candidateId);

    if (is_wp_error($invitation)) {
        wp_send_json_error("Failed to create Checkr Candidate: {$invitation->get_error_message()}");
    }

    update_user_meta($user->ID, 'background_check_invite_url', $invitation->invitation_url);

    wp_send_json_success([
        'candidate_id' => $candidateId,
        'invitation_url' => $invitation->invitation_url,
    ]);
});