<?php
/**
 * Updates the Attendance if specific query parameters are in the URL
 */

add_action( 'setup_theme', function() {

    $nonce = ( isset( $_GET['_nonce']) ? $_GET['_nonce'] : '' );
    $rsvpid = ( isset($_GET['rsvpid']) ? absint( $_GET['rsvpid'] ) : 0 );
    $attended = ( isset($_GET['attended']) ? $_GET['attended'] : '' );
	$email = ( isset($_GET['email']) ? $_GET['email'] : '' );
	$fname = ( isset($_GET['fname']) ? $_GET['fname'] : '' );

    if ( current_user_can('can_view_rsvps') ) {
        if ( isset($rsvpid, $attended) && wp_verify_nonce( $nonce, 'sdrt_attendance_nonce' ) ) {
            update_post_meta( $rsvpid, 'attended', $attended );
        }
    }

    $event_title = get_the_title($rsvpid);

	// The email we send the user to confirm they cleared and can now RSVP.
	$headers[] = 'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>';

    if ( isset( $nonce ) && $attended == 'yes') {
	   wp_mail( $email, 'Thank you for attending ' . $event_title, sdrt_email_thankyou($fname, $event_title), $headers );
    }

});