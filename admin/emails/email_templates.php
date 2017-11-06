<?php

/**
 *
 * The THANK YOU FOR ATTENDING email
 *
 * @param $fname
 * @param $event_title
 *
 * @return string
 */

function sdrt_send_email( $args ) {

	$body = isset( $args['option']) ? get_option( $args['option'] ) : '';

	$registered_tags = array(
		'{first_name}'    => isset( $args['fname']) ? $args['fname'] : '',
		'{event_title}'   => isset( $args['event_title']) ? $args['event_title'] : '',
        '{lost_password_link}'  => '<a href="' . wp_lostpassword_url( get_home_url('', '/events/') ) . '" title="Lost Password">Reset Your Password Here</a>',
	);

	$get_tags = preg_match_all( "/{([A-z0-9\-\_]+)}/s", $body, $existing_tags );

	$final_array = array_intersect_key( $registered_tags, array_flip($existing_tags[0]) );

	$new_content = str_replace( $existing_tags[0], $final_array, $body );

	return wpautop( $new_content );
}

function sdrt_email_checkr_consider( $user ) {
    ob_start();
	$user_email = $user[0]->data->user_email;
    ?>
    <p>Dear SDRT Leadership,</p>

    <p>This email is simply to inform you that a volunteer attempted to do a background check and Checkr returned the results as "consider", which means there are some items on their report that we need to take into account.</p>

    <p>For security purposes, this email will not include those details. But you can login to dashboard.checkr.com and search for them by their email address (<?php echo $user_email;?>).</p>

    <p>Here are a few things to keep in mind in this regard:
        <ul style="margin-left: 25px; text-indent: -10px; margin-bottom: 25px;">
            <li>&#8226; They cannot yet RSVP for any sessions.</li>
            <li>&#8226; Sometimes these reports are overly cautious. It might be worth following up with the volunteer for clarification.</li>
            <li>&#8226; You can manually enable the users account to RSVP if you choose to. Simply go to their profile in the SDRT website, and change their user role to "Volunteer". Then email them of the change.</li>
        </ul>
    </p>

    <p>Thank you for all you do!</p>

    <p>Sincerely,<br />
    <em>Your SDRT website auto-bot</em> :-)
    </p>

	<?php

	$body = ob_get_clean();
	return $body;
}