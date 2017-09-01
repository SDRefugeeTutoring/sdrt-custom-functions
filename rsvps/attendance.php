<?php
/**
 * Updates the Attendance if specific query parameters are in the URL
 */

add_action( 'setup_theme', function() {

    $nonce = ( isset( $_GET['_nonce']) ? $_GET['_nonce'] : '' );
    $rsvpid = ( isset($_GET['rsvpid']) ? $_GET['rsvpid'] : '' );
    $attended = ( isset($_GET['attended']) ? $_GET['attended'] : '' );

    if ( current_user_can('update_plugins') ) {
        if ( isset($rsvpid, $attended) && wp_verify_nonce( $nonce, 'sdrt_attendance_nonce' ) ) {
            update_post_meta( $rsvpid, 'attended', $attended );
        }
    }
});