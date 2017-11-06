<?php
/**
 * Updates the Attendance if specific query parameters are in the URL
 */

add_action( 'setup_theme', function() {

	$event_id = ( isset($_GET['rsvpid']) ? absint( $_GET['rsvpid'] ) : 0 );

	$args = array(
		'nonce'     => ( isset( $_GET['_nonce']) ? $_GET['_nonce'] : '' ),
		'rsvpid'    => ( isset($_GET['rsvpid']) ? absint( $_GET['rsvpid'] ) : 0 ),
		'attended'  => ( isset($_GET['attended']) ? $_GET['attended'] : '' ),
		'email'     => ( isset($_GET['email']) ? $_GET['email'] : '' ),
		'fname'     =>( isset($_GET['fname']) ? $_GET['fname'] : '' ),
		'event_title'   => get_the_title($event_id),
		'option'    => 'sdrt_thanks_for_attending'
	);

    if ( current_user_can('can_view_rsvps') ) {
        if ( isset($args['rsvpid'], $args['attended']) && wp_verify_nonce( $args['nonce'], 'sdrt_attendance_nonce' ) ) {
            update_post_meta( $args['rsvpid'], 'attended', $args['attended'] );
        }
    }

	if ( current_user_can('can_rsvp') ) {
		if ( isset($args['rsvpid'], $args['attended']) && wp_verify_nonce( $args['nonce'], 'sdrt_cancellation_nonce' ) ) {
			update_post_meta( $args['rsvpid'], 'attended', $args['attended'] );
		}
	}

	// The email we send the user to confirm they cleared and can now RSVP.
	$headers[] = 'From: SD Refugee Tutoring <info@sdrefugeetutoring.com>';

    if ( isset( $args['nonce'] ) && $args['attended'] == 'yes') {
	   wp_mail( $args['email'], 'Thank you for attending ' . $args['event_title'], sdrt_send_email($args), $headers );
    }

});