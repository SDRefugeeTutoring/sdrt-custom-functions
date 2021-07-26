<?php

declare(strict_types=1);

/**
 * Add the "Clear Background Check" to the Users bulk actions
 */
add_filter('bulk_actions-users', static function (array $actions): array {
    if ( ! current_user_can('manage_volunteers')) {
        return $actions;
    }

    return array_merge($actions, [
        'sdrt_clear_background_check' => 'Clear Background Check',
    ]);
});

/**
 * Handle the "Clear Background Check" bulk action
 */
add_filter('handle_bulk_actions-users', static function (string $redirectUrl, string $action, array $userIds): string {
    if ($action !== 'sdrt_clear_background_check' || ! current_user_can('manage_volunteers')) {
        return $redirectUrl;
    }

    foreach ($userIds as $userId) {
        update_user_meta($userId, 'background_check', 'No');
        delete_user_meta($userId, 'background_check_invite_url');
    }

    return add_query_arg('sdrt-cleared-background', count($userIds), $redirectUrl);
}, 10, 3);

add_action('admin_notices', static function () {
    if (empty($_GET['sdrt-cleared-background'])) {
        return;
    }

    $usersCleared = (int)$_GET['sdrt-cleared-background'];

    echo "
        <div id='message' class='updated notice is-dismissible'><p>
            Background checks cleared for $usersCleared users
        </p></div>
    ";
});