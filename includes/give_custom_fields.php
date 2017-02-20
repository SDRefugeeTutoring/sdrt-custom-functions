<?php

/**
 * Adds Custom fields to the Give RSVP form
 */

/**
 * Add the hidden field
 * Populated by the Event ID the form is embedded on
 * @param $form_id
 */

add_action( 'give_after_donation_levels', 'sdrt_give_hidden_form_fields', 10, 1 );

function sdrt_give_hidden_form_fields( $form_id ) {

	global $post;
	$forms = array(400);
	$eventdate = get_post_meta($post->ID, '_EventStartDate', true);
	$createDate = new DateTime($eventdate);
	$finaldate = $createDate->format('F d, Y');

	if ( in_array($form_id, $forms) ) {
		?>
		<input type="hidden" name="event_id" value="<?php echo $post->ID; ?>">
        <input type="hidden" name="event_date" value="<?php echo $finaldate; ?>">
		<?php
	}
}

/**
 * Saves the hidden custom field to $payment_meta
 * @param $payment_meta
 *
 * @return mixed
 */

add_filter( 'give_payment_meta', 'sdrt_give_save_hidden_fields' );

function sdrt_give_save_hidden_fields( $payment_meta ) {

	$payment_meta['event_id'] = isset( $_POST['event_id'] ) ? implode( "n", array_map( 'sanitize_text_field', explode( "n", $_POST['event_id'] ) ) ) : '';

	$payment_meta['event_date'] = isset( $_POST['event_date'] ) ? implode( "n", array_map( 'sanitize_text_field', explode( "n", $_POST['event_date'] ) ) ) : '';

	return $payment_meta;
}

/**
 * Outputs the Event ID in the Donation Details screen
 *
 * @param $payment_meta
 * @param $user_info
 */

add_action( 'give_payment_personal_details_list', 'sdrt_pass_eventid_to_donation_details_screen', 10, 2 );

function sdrt_pass_eventid_to_donation_details_screen( $payment_meta, $user_info ) {

	if ( ! isset( $payment_meta['event_date'] ) ) {
		return;
	}

	$eventdate = $payment_meta['event_date'];

	?>

	<div class="event_id column-container" style="margin: 12px 0;">
		<p><strong><?php echo __( 'Event:', 'give' ); ?></strong>
			<?php echo $eventdate; ?></p>
	</div>

	<?php
}

/**
 * Registers the custom {Event} Give Email Tag
 * @param $payment_id
 */

add_action( 'give_add_email_tags', 'sdrt_give_event_title_email_tag' );

function sdrt_give_event_title_email_tag( $payment_id ) {

	give_add_email_tag( 'Event', 'This outputs the date of the Event the volunteer is RSVPing for', 'sdrt_give_event_title_email_data' );

}

/**
 * Passes data to the {Event} Give Email Tag
 * @param $payment_id
 * @param $payment_meta
 *
 * @return string|void
 */

function sdrt_give_event_title_email_data( $payment_id, $payment_meta ) {

	$payment_meta = give_get_payment_meta( $payment_id );

	$output       = __( 'No referral data found.', 'give' );

	if ( ! empty( $payment_meta['event_date'] ) ) {

		$output = $payment_meta['event_date'];

	}
	return $output;
}

/**
 * A custom redirect for the Give RSVP form
 * This allows the "donor" to go to a custom page
 * Instead of the standard Give Donation Confirmation page
 *
 * @param $success_page
 *
 * @return string
 */

add_filter( 'give_get_success_page_uri', 'sdrt_give_events_redirects', 10, 1 );

function sdrt_give_events_redirects( $success_page ) {

	$form_id = isset( $_POST['give-form-id'] ) ? $_POST['give-form-id'] : 0;

	if ( $form_id == 400 ) {
		$success_page = esc_url( get_permalink( 413 ) );
	}

	return $success_page;
}
