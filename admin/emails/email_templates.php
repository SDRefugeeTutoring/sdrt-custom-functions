<?php

/**
 *  Volunteer Confirmed Emails
 *  This email is sent when a volunteer's background check
 *  get approved.
 *
 *  @param $user_id
 *
 *  @return string
 */

function sdrt_email_checkr_clear($user_id) {
	ob_start();
	$usermeta = get_user_meta($user_id);
	$first = $usermeta['first_name'];
	?>
	<p>Dear <?php echo $first; ?>,</p>

	<p>Congratulations! We received an all clear on your background check. Thank you for taking that time and effort. Background checks ensure that our students are always in safe hands.</p>

	<p>We would love to have you begin volunteering as soon as possible. Please go to our Calendar to find the upcoming tutoring sessions that you are now eligble to RSVP for.</p>

	<p><strong>NOTE:</strong> You must login in order to RSVP for tutoring sessions. This ensures that only those who have passed background checks can tutor. Your password was emailed to you when you filled out your registration form. If you have lost that, feel free <a href="<?php echo wp_lostpassword_url( get_home_url('', '/events/') ); ?>" title="Lost Password">reset your password here</a>.</p>

	<p>Lastly, in order for our students to receive the dedicated attention they need and deserve, we ask all volunteers to RSVP for at least 3 tutoring sessions a month.</p>

	<p>Thank you again for your generosity in volunteering your valuable time for the benefit of our refugee students. We could not do this without you.</p>

	<p>Sincerely,<br />Your SD Refugee Tutoring Leadership Team</p>
	<?php
	$body = ob_get_clean();

	return $body;
}

/**
 *
 */

function sdrt_email_thankyou($fname, $event_title) {
	ob_start(); ?>

	<p>Hi <?php echo $fname; ?>,</p>

	<p>Thanks for attending our "<strong><?php echo $event_title; ?></strong>" event. Your attendance and volunteerism helps keep us going.</p>

	<p>Please review all our <a href="https://sdrefugeetutoring.com/events">upcoming events</a> for more opportunities. We encourage all tutors to aim for 3 tutoring sessions a month.</p>

	<p>With much appreciation,</p>
	<p><em>The SD Refugee Tutoring Leadership</em></p>

	<?php

	$body = ob_get_clean();
	return $body;
}