<?php
/**
 *   MAIN RSVP FORM FUNCTION
 *
 *   This hooks into the Events Calendar single template
 *   It conditionally shows the login/registration
 *   Then the RSVP form for logged-in users
 *   It also outputs the RSVP table for Registration
 *
 */

add_action('tribe_events_single_event_after_the_meta', 'embed_rsvp_events_single');

function embed_rsvp_events_single() {
	global $post;

	$get_limit      = get_post_meta( get_the_ID(), 'rsvps_limit', true );
	$rsvp_enabled   = get_post_meta( get_the_ID(), 'enable_rsvps', true );
	$must_login     = get_post_meta( get_the_ID(), 'logged_in_status', true );
	$rsvp_form      = get_post_meta( get_the_ID(), 'rsvp_form', true );
	$rsvp_limit     = ( !empty($get_limit) ? $get_limit : '');
	$eventdate      = get_post_meta($post->ID, '_EventStartDate', true);
	$rsvps          = get_current_rsvps($rsvpdate = $eventdate);
	$rsvpmeta       = get_current_rsvps_volids($rsvpdate = $eventdate);
	$userid         = get_current_user_id();
	$rsvp_total     = count($rsvps);

	// Only output if RSVPs are enabled for this Event
	if ( $rsvp_enabled == 'enabled' ) : ?>

        <div class="tutoring-rsvp">
            <h2 class="give-title">RSVP HERE:</h2>

			<?php

			// Show RSVP form if login is not required
			if ( $must_login == 'no' ) {

				// Show message if RSVP limit is reached
				if ( $rsvp_limit > 0 && $rsvp_total >= $rsvp_limit ) {

				    sdrt_rsvp_limit_reached_output();

				} else {

				    echo '<p>We currently need <strong>' . abs($rsvp_limit - $rsvp_total) . '</strong> more tutors.</p>';
					echo do_shortcode('[caldera_form id="' . $rsvp_form . '"]');

				}

            // Inform visitor to register if login is required
			} elseif ( $must_login == 'yes') {

				// Not logged in
				if ( ! is_user_logged_in() ) {
					sdrt_rsvp_please_register_output();

				}

				if ( is_user_logged_in() && current_user_can('can_rsvp') ) {

				    // If already RSVP'd
				    if ( in_array( $userid, $rsvpmeta ) ) {

					    sdrt_rsvp_already_rsvpd_output();

					// If RSVPs are full
				    } elseif ( $rsvp_limit > 0 && $rsvp_total >= $rsvp_limit ) {

				        sdrt_rsvp_limit_reached_output();

				    // RSVPs are open and volunteer can RSVP
				    }  else {
					    echo '<p>We currently need <strong>' . abs( $rsvp_limit - $rsvp_total ) . '</strong> more tutors.</p>';
					    echo do_shortcode( '[caldera_form id="' . $rsvp_form . '"]' );
				    }

				} elseif ( is_user_logged_in() && ! current_user_can('can_rsvp')) {
				    echo '<p>It looks like you\'ve completed your background check, but it has not yet cleared. Please come back later or contact our Webmaster via our <a href="' .  get_home_url() . '/contact/">contact form</a> for questions.</p>';
                }
			}
			?>
        </div><!-- end RSVP section -->

		<?php

		// Outputs the RSVP Registration Table
		// Volunteers can register their attendance
		// by clicking on the "X" next to their name
		// This is only viewable by a logged-in Admin account

		if ( !empty( $rsvps ) && current_user_can( 'can_view_rsvps' ) ) :

			$createDate = new DateTime($eventdate);
			$finaldate = $createDate->format('F d, Y');
			?>

            <h2 class="give-title current_rsvps" id="rsvps">Current RSVPS:</h2>

            <button class="rsvp-download">Print RSVPs</button>

            <table class="rwd-table" id="rsvp-table"  width="100%">
                <thead>
                <th colspan="4">
                    Tutoring on <?php echo $finaldate; ?>
                </th>
                </thead>
                <tr class="labels">
                    <td><strong>Name</strong></td>
                    <td><strong>Email</strong></td>
                    <td><strong>Attended?</strong></td>
                    <td><strong>Actions</strong></td>
                </tr>
				<?php

				foreach( $rsvps as $rsvp ) {

					$rsvpid = $rsvp->ID;

					$name = get_post_meta($rsvpid, 'volunteer_name', true);
					$email = get_post_meta($rsvpid, 'volunteer_email', true);

					$getname = explode(',', $name);

					$firstname = ( strpos($name, ',') ? $getname[1] : $name );

					//list($firstname)=explode(',', $name);

                    $attending = get_post_meta($rsvpid, 'attending', true);
					$attended = get_post_meta($rsvpid, 'attended', true);
					$attendancenonce = wp_create_nonce( 'sdrt_attendance_nonce' );
					if ($attending == 'no') {}
					else {
                        ?>
                        <tr>
                            <td data-th="name" width="40%"><?php echo $name; ?></td>
                            <td data-th="email" width="40%"><?php echo $email; ?></td>
                            <?php if ($attended == 'no') { ?>
                                <td data-th="attended" width="20%"><a
                                            href="<?php echo get_permalink(get_the_ID()) . '?rsvpid=' . $rsvpid . '&attended=yes&_nonce=' . $attendancenonce . '&email=' . $email . '&fname=' . $firstname . '#rsvps'; ?>"
                                            class="button attended-no"><span class="dashicons dashicons-no"
                                                                             style="border-radius: 50%; background: darkred; color: white; padding: 6px;"
                                                                             title="Click to change to Yes">No</span></a>
                                </td>
                            <?php } elseif ($attended == 'unknown') { ?>
                                <td data-th="attended" width="20%"><a
                                            href="<?php echo get_permalink(get_the_ID()) . '?rsvpid=' . $rsvpid . '&attended=yes&_nonce=' . $attendancenonce . '&email=' . $email . '&fname=' . $firstname . '#rsvps'; ?>>"
                                            class="button attended-unknown"><span class="dashicons dashicons-minus"
                                                                                  style="border-radius: 50%; background: #777777; color: white; padding: 6px;"
                                                                                  title="Click to change to Yes">Unknown</span></a>
                                </td>
                            <?php } else { ?>
                                <td data-th="attended" width="20%"><span class="dashicons dashicons-yes"
                                                                         style="border-radius: 50%; background: forestgreen; color: white; padding: 6px;">Yes</span>
                                </td>
                            <?php } ?>
                            <td><a href="<?php echo get_delete_post_link($rsvpid); ?>">Delete</a></td>
                        </tr>

                        <?php

                    }
				}
				wp_reset_postdata();

				?>
            </table>

			<?php
		endif;
	endif;
}

/**
 *   OUTPUT: ALREADY RSVP'D OUTPUT
 */

function sdrt_rsvp_already_rsvpd_output() {

    $eventdate = get_post_meta( get_the_ID(), '_EventStartDate', true );
	$rsvp = get_my_rsvp( $rsvpdate = $eventdate );
	$rsvpmeta = get_post_meta($rsvp->ID);
	$attending = $rsvpmeta['attending'][0];

	if (isset($attending) && $attending == 'no') {
    ?>
        <div class="already-rsvpd-no">
            <p><strong>Thanks for the heads up</strong></p>
            <p>It looks like you've already RSVP'd for this event. Sorry to hear you can't make it this time</p>
            <p>Please take a look at <a href="<?php echo esc_url( Tribe__Events__Main::instance()->getLink() ); ?>">all our tutoring sessions</a> for more opportunities to volunteer.</p>
        </div>
    <?php } else { ?>
        <div class="already-rsvpd-yes">
            <p><strong>Thanks!</strong></p>
            <p>It looks like you've already RSVP'd for this event. We look forward to seeing you there!</p>
            <p>Need to cancel? <a href="<?php echo get_delete_post_link($rsvp->ID); ?>">Click here</a>.</p>
        </div>
    <?php }
}

/**
 *  OUTPUT: RSVP LIMIT REACHED
 */

function sdrt_rsvp_limit_reached_output() { ?>
    <div class="rsvps-closed">
        <p><strong>Sorry!</strong></p>
        <p>We already have the max number of Volunteers we need for this session.</p>
        <p>Please see our <a href="<?php echo site_url(); ?>/events">full Calendar</a> for future Tutoring Opportunities.</p>
    </div>
    <?php
}


/**
 *  OUTPUT: PLEASE REGISTER OUTPUT
 */

function sdrt_rsvp_please_register_output() { ?>
    <div class="please-register">
        <p><strong>Please Register to RSVP</strong></p>
        <p>All volunteers must first be registered and have passed a background check in order to RSVP. Please visit the <a href="<?php echo site_url(); ?>/volunteer">Registration page</a> for details.</p>
        <p><strong>Already Registered? Please Login</strong></p>
		<?php echo do_shortcode( '[caldera_form id="CF597578b115ae1"]' ); ?>
    </div>
	<?php
}

/**
 * GET RSVPS FOR CURRENT EVENT
 *
 * @param string $rsvpdate
 * @return array
 */

function get_current_rsvps( $rsvpdate = '' ) {

	$rsvps = array();

	$args = array(
		'post_type'     => 'rsvp',
		'post_status'   => array('publish'),
		'order'         => 'ASC',
		'orderby'       => 'meta_value',
		'meta_key'      => 'volunteer_name',
		'nopaging'		=> true,
		'posts_per_page'=>-1,
		'no_found_rows' => true,  // turn off pagination
		'meta_query'    => array(
			array(
				'key'     => 'event_date',
				'value'   => $rsvpdate,
			),
		),
	);

	global $post;
	$loop = new WP_Query( $args );
	$rsvps = $loop->get_posts();

	return $rsvps;
}

/**
 * GET VOLUNTEER IDS OF CURRENT EVENT RSVPS
 *
 * @param string $rsvpdate
 * @return array
 */

function get_current_rsvps_volids( $rsvpdate = '' ) {
	global $post;
	$eventdate = get_post_meta( $post->ID, '_EventStartDate', true );
	$rsvps = get_current_rsvps( $rsvpdate = $eventdate );

	$volid = array();

	foreach( $rsvps as $rsvp ) {

		$ids = $rsvp->ID;
		$volid[] = get_post_meta($ids, 'volunteer_user_id', true);

	}

	return $volid;

}

/**
 * GET VOLUNTEER IDS OF CURRENT EVENT RSVPS
 *
 * @param string $rsvpdate
 * @return array
 */

function get_my_rsvp( $rsvpdate = '' ) {

	$eventdate = get_post_meta( get_the_ID(), '_EventStartDate', true );
	$rsvps = get_current_rsvps( $rsvpdate = $eventdate );

	foreach( $rsvps as $rsvp ) {

		$ids = $rsvp->ID;
		$volid[] = get_post_meta($ids, 'volunteer_user_id', true);
		$userid  = get_current_user_id();

		if ( in_array( $userid, $volid ) ) {
		    return $rsvp;
        }

	}

}