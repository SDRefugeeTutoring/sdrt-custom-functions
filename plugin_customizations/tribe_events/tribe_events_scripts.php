<?php

add_action(
    'wp_enqueue_scripts',
    static function () {
        if (is_admin() || !is_singular('tribe_events') || !current_user_can('can_view_rsvps')) {
            return;
        }

        $assetsUrl = SDRT_FUNCTIONS_URL . 'assets/';
        $assetsPath = SDRT_FUNCTIONS_DIR . '/assets/';

        wp_enqueue_style('dashicons');

        wp_enqueue_style(
            'sdrt-event-single',
            "$assetsUrl/event-single.css",
            [],
            filemtime("$assetsPath/event-single.css"),
        );

        wp_enqueue_script(
            'sdrt-event-single',
            "$assetsUrl/event-single.js",
            ['jquery'],
            filemtime("$assetsPath/event-single.js"),
            true
        );
    }
);