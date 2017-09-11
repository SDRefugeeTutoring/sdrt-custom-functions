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
	$first = $usermeta['first_name'][0];
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
 * The THANK YOU FOR ATTENDING email
 *
 * @param $fname
 * @param $event_title
 *
 * @return string
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

function sdrt_email_checkr_consider($user) {
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