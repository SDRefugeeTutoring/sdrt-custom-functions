<?php

add_action('wp_enqueue_scripts', static function () {
    $assetsUrl  = SDRT_FUNCTIONS_URL . 'assets/';
    $assetsPath = SDRT_FUNCTIONS_DIR . '/assets/';

    wp_enqueue_script(
        'sdrt-event-single',
        "$assetsUrl/event-single.js",
        ['jquery'],
        filemtime("$assetsPath/event-single.js"),
        true
    );
});