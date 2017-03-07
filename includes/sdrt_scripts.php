<?php

/**
 *  Enqueues Scripts and Styles unique to the
 *  Give/Events Calendar Integration
 *
 */

add_action( 'wp_enqueue_scripts', 'sdrt_enqueue_tableExport_scripts' );

function sdrt_enqueue_tableExport_scripts() {

	if ( is_singular('tribe_events') && current_user_can('update_plugins') ) {
        wp_enqueue_script('sdrt-tableExport', SDRT_FUNCTIONS_URL . 'vendor/tableExport/tableExport.js', false);
        wp_enqueue_script('sdrt-base64-js', SDRT_FUNCTIONS_URL . 'vendor/tableExport/jquery.base64.js', false);
        wp_enqueue_script('sdrt-sprintf', SDRT_FUNCTIONS_URL . 'vendor/tableExport/sprintf.js', false);
        wp_enqueue_script('sdrt-jsPDF', SDRT_FUNCTIONS_URL . 'vendor/tableExport/jspdf.js', false);
        wp_enqueue_script('sdrt-jsPDF-base64', SDRT_FUNCTIONS_URL . 'vendor/tableExport/base64.js', false);
    }

	wp_enqueue_style('rsvp-styles', SDRT_FUNCTIONS_URL . 'assets/rsvp-styles.css', array('give-styles'), 'all');
}

