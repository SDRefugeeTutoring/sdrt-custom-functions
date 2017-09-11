<?php
/**
 * Updates the Attendance if specific query parameters are in the URL
 */

add_action( 'setup_theme', function() {

    $nonce = ( isset( $_GET['_nonce']) ? $_GET['_nonce'] : '' );
    $rsvpid = ( isset($_GET['rsvpid']) ? absint( $_GET['rsvpid'] ) : 0 );
    $attended = ( isset($_GET['attended']) ? $_GET['attended'] : '' );

    if ( 0 < $rsvpid && current_user_can('update_plugins') ) {
        if ( isset($rsvpid, $attended) && wp_verify_nonce( $nonce, 'sdrt_attendance_nonce' ) ) {
            update_post_meta( $rsvpid, 'attended', $attended );
        }
    }

});