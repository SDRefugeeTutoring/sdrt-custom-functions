<?php

declare(strict_types=1);

use SDRT\CustomFunctions\Checkr\Actions\CreateCandidate;
use SDRT\CustomFunctions\Checkr\Actions\CreateInvitation;

/**
 * Add the "Clear Background Check" to the Users bulk actions
 */
add_filter('bulk_actions-users', static function (array $actions): array {
    if ( ! current_user_can('manage_volunteers')) {
        return $actions;
    }

    return array_merge($actions, [
        'sdrt_reset_volunteer_requirements' => 'Reset Volunteer Requirements',
    ]);
});

/**
 * Handle the "Clear Background Check" bulk action
 */
add_filter('handle_bulk_actions-users', static function (string $redirectUrl, string $action, array $userIds): string {
    if ($action !== 'sdrt_reset_volunteer_requirements' || ! current_user_can('manage_volunteers')) {
        return $redirectUrl;
    }

    foreach ($userIds as $userId) {
        update_user_meta($userId, 'sdrt_orientation_attended', 'No');
        update_user_meta($userId, 'sdrt_coc_consented', 'No');
        update_user_meta($userId, 'sdrt_waiver_consented', 'No');
        update_user_meta($userId, 'background_check', 'Cleared');
        delete_user_meta($userId, 'background_check_invite_url');
    }

    return add_query_arg('sdrt-cleared-background', count($userIds), $redirectUrl);
}, 10, 3);

add_action('admin_notices', static function () {
    if ( ! empty($_GET['sdrt-cleared-background'])) {
        $usersCleared = (int)$_GET['sdrt-cleared-background'];

        echo "
        <div id='message' class='updated notice is-dismissible'><p>
            Background checks cleared for $usersCleared users
        </p></div>
    ";
    }

    if ( !empty($_GET['sdrt-request-new-invitation']) ) {
        $problem = true;
        switch($_GET['sdrt-request-new-invitation']) {
            case 'no-date-of-birth':
                $message = 'A date of birth is required to register for a new background check.';
                break;

            case 'invalid-candidate':
                $message = 'There was an issue creating a Checkr Candidate, please try again and contact a site administrator if the problem persists.';
                break;

            case 'invalid-invitation':
                $message = 'There was an issue creating a Checkr Invitation, please try again and contact a site administrator if the problem persists.';
                break;

            default:
                $message = 'You have successfully registered for a new background check. A new invitation will be sent to your email.';
                $problem = false;
                break;
        }

        $noticeClass = $problem ? 'error notice-error' : 'updated';
        echo "<div id='message' class='notice is-dismissible $noticeClass'><p>$message</p></div>";
    }
});

add_action('admin_init', static function () {
    if (empty($_GET['sdrt-request-new-invitation'])) {
        return;
    }

    /** @var WP_User $user */
    $user = wp_get_current_user();
    $candidateId = get_user_meta($user->ID, 'background_check_candidate_id', true);

    if (empty($candidateId)) {
        $dateOfBirth = get_user_meta($user->ID, 'your_date_of_birth', true);

        if (empty($dateOfBirth)) {
            $_GET['sdrt-request-new-invitation'] = 'no-date-of-birth';

            return;
        }

        $candidate = sdrt(CreateCandidate::class)(
            $user->first_name,
            $user->last_name,
            $user->user_email,
            new DateTime($dateOfBirth)
        );

        if (is_wp_error($candidate)) {
            $_GET['sdrt-request-new-invitation'] = 'invalid-candidate';

            return;
        }

        $candidateId = $candidate->id;
        update_user_meta($user->ID, 'background_check_candidate_id', $candidateId);
    }

    $inviteUrl = get_user_meta($user->ID, 'background_check_invite_url', true);
    if ( ! empty($inviteUrl)) {
        return;
    }

    $invitation = sdrt(CreateInvitation::class)($candidateId);

    if (is_wp_error($invitation)) {
        $_GET['sdrt-request-new-invitation'] = 'invalid-invitation';

        return;
    }

    update_user_meta($user->ID, 'background_check_invite_url', $candidateId);

    wp_safe_redirect(remove_query_arg('sdrt-request-new-invitation'));
});