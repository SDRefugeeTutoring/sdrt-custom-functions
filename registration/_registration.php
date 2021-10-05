<?php
/**
 * All functions necessary for volunteer registration are here
 *
 */

require_once SDRT_FUNCTIONS_DIR . 'registration/ajax.php';

/**
 * Create a candidate and trigger an invite on Checkr.io
 * This is all done via a Caldera Form
 */


/**
 *  Create the Checkr Webhook
 *  Then conditionally update the user if they passed the background check
 *  And send that user an email informing them they can now RSVP.
 */

add_action( 'init', 'sdrt_listen_for_checkr' );

function sdrt_listen_for_checkr() {

	// Listen for the webhook url: sdrefugeetutoring.com/?sdrt-listener=checkr
	if ( isset( $_GET['sdrt-listener'] ) && $_GET['sdrt-listener'] === 'checkr' ) {
		/**
		 * Fires when Checkr webhook is sent
		 *
		 * @since 1.0
		 */

		$webhook = file_get_contents( 'php://input' );
		$webhook_data = json_decode( $webhook );

		// Get the report type
		$report_type = $webhook_data->type;

		// Get the report status. Looking for "clear"
		$report_status = $webhook_data->data->object->status;
		$candidate_id = $webhook_data->data->object->candidate_id;

		// Find the existing WP user based on the "candidate id" that we created when they registered on the Caldera Form
		$user = get_users( array( 'meta_key'=>'background_check_candidate_id', 'meta_value'=>$candidate_id ) );

		// Get necessary user_meta values
		$user_id = $user[0]->ID;
		$user_role = $user[0]->roles;
		$user_email = $user[0]->data->user_email;
		$usermeta = get_user_meta($user_id);

		// If the report is "clear" and the user associated with that report is a "Volunteer Pending", update the user and send them a confirmation email.
        if ( $report_type === 'report.completed' && $report_status === 'clear' && $user_role[0] === 'volunteer_pending' ) {

			//Define arguments to send to the email copy
			$args = array(
				'fname' => $usermeta['first_name'][0],
				'option'    => 'sdrt_checkr_clear_email_copy'
			);

			// The update user function. We're only updating their user role
			wp_update_user( array( 'ID' => $user_id, 'role' => 'volunteer' ) );

			sdrt_mail($user_email, 'You can now RSVP for Refugee Tutoring Sessions!', $args);

		} elseif ( $report_type === 'report.completed' && $report_status === 'consider') {

			sdrt_mail( 'carolnarikim@gmail.com, matt@sdrefugeetutoring.com, melissa@sdrefugeetutoring.com', 'A Volunteers background check did not clear', sdrt_email_checkr_consider($user));
		}
        
		wp_die('','',array('response'=>200));
	}
}

