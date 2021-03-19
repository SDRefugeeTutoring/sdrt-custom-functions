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

function embed_rsvp_events_single()
{
    global $post;

    $get_limit    = get_post_meta(get_the_ID(), 'rsvps_limit', true);
    $rsvp_enabled = get_post_meta(get_the_ID(), 'enable_rsvps', true);
    $must_login   = get_post_meta(get_the_ID(), 'logged_in_status', true);
    $rsvp_form    = get_post_meta(get_the_ID(), 'rsvp_form', true);
    $rsvp_limit   = (! empty($get_limit) ? $get_limit : '');
    $eventdate    = get_post_meta($post->ID, '_EventStartDate', true);
    $rsvps        = get_event_rsvps($post->ID);
    $rsvpmeta     = get_current_rsvps_volids($post->ID);
    $userid       = get_current_user_id();
    $rsvp_total   = count($rsvps);

    // Only output if RSVPs are enabled for this Event
    if ($rsvp_enabled !== 'enabled') {
        return;
    }
    ?>

    <div class="tutoring-rsvp">
        <h2 class="give-title">RSVP HERE:</h2>

        <?php

        // Show RSVP form if login is not required
        if ($must_login === 'no') {
            // Show message if RSVP limit is reached
            if ($rsvp_limit > 0 && $rsvp_total >= $rsvp_limit) {
                sdrt_rsvp_limit_reached_output();
            } else {
                echo '<p>We currently need <strong>' . abs($rsvp_limit - $rsvp_total) . '</strong> more tutors.</p>';
                echo do_shortcode('[caldera_form id="' . $rsvp_form . '"]');
            }
            // Inform visitor to register if login is required
        } elseif ($must_login === 'yes') {
            $can_rsvp = sdrt_vol_can_rsvp();

            // Not logged in
            if ( ! is_user_logged_in()) {
                sdrt_rsvp_please_register_output();
            }

            if ( ! $can_rsvp) {
                sdrt_finish_reqs();
            }

            if ($can_rsvp && is_user_logged_in()) {
                // If already RSVP'd
                if (in_array($userid, $rsvpmeta)) {
                    sdrt_rsvp_already_rsvpd_output($post->ID);
                    // If RSVPs are full
                } elseif ($rsvp_limit > 0 && $rsvp_total >= $rsvp_limit) {
                    sdrt_rsvp_limit_reached_output();
                    // RSVPs are open and volunteer can RSVP
                } else {
                    echo '<p>We currently need <strong>' . abs($rsvp_limit - $rsvp_total) . '</strong> more tutors.</p>';
                    echo do_shortcode('[caldera_form id="' . $rsvp_form . '"]');
                }
            } elseif (is_user_logged_in() && ! current_user_can('can_rsvp')) {
                echo '<p>It looks like you\'ve completed your background check, but it has not yet cleared. Please come back later or contact our Webmaster via our <a href="' . get_home_url() . '/contact/">contact form</a> for questions.</p>';
            }
        }
        ?>
    </div><!-- end RSVP section -->

    <?php

    // Outputs the RSVP Registration Table
    // Volunteers can register their attendance
    // by clicking on the "X" next to their name
    // This is only viewable by a logged-in Admin account

    if (empty($rsvps) || ! current_user_can('can_view_rsvps')) {
        return;
    }

    $rsvp_nonce = wp_create_nonce('sdrt_attendance_nonce');
    $createDate = new DateTime($eventdate);
    $finaldate  = $createDate->format('F d, Y');
    ?>

    <script>
        var rsvpExports = <?= json_encode([
            'nonce'   => $rsvp_nonce,
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]) ?>;
    </script>

    <h2 class="give-title current_rsvps" id="rsvps">Current RSVPS:</h2>

    <button class="rsvp-download">Print RSVPs</button>

    <table class="rwd-table" id="rsvp-table" width="100%">
        <th colspan="4">
            Tutoring on <?php
            echo $finaldate; ?>
        </th>
        <tr class="labels">
            <td><strong>Name</strong></td>
            <td><strong>Email</strong></td>
            <td><strong>Attended?</strong></td>
            <td><strong>Actions</strong></td>
        </tr>
        <?php

        foreach ($rsvps as $rsvp) {
            $rsvp_id = $rsvp->ID;

            $name  = get_post_meta($rsvp_id, 'volunteer_name', true);
            $email = get_post_meta($rsvp_id, 'volunteer_email', true);

            $getname = explode(',', $name);

            $firstname = (strpos($name, ',') ? $getname[1] : $name);

            $attending      = get_post_meta($rsvp_id, 'attending', true);
            $attended       = get_post_meta($rsvp_id, 'attended', true);
            $eventUrl       = get_permalink(get_the_ID());
            $setAttendedUrl = "$eventUrl?rsvpid=$rsvp_id&attended=yes&_nonce=$rsvp_nonce&email=$email&fname=$firstname#rsvps";

            if ($attending !== 'no') { ?>
                <tr>
                    <td data-th="name" width="40%"><?= $name ?></td>
                    <td data-th="email" width="40%"><?= $email ?></td>
                    <td data-th="attended" width="20%">
                        <?php
                        if ($attended === 'no' || $attended === 'unknown'): ?>
                            <a href="<?= $setAttendedUrl ?>" class="button action attended attended-<?= $attended ?>">
                                <span class="dashicons dashicons-editor-help" style="color: gray; font-size: 2.8rem; overflow: visible;"
                                      title="Click to change to Yes"></span>
                            </a>
                        <?php
                        else: ?>
                            <span class="dashicons dashicons-yes"
                                  style="border-radius: 50%; background: forestgreen; color: white; padding: 6px;">Yes</span>
                        <?php
                        endif; ?>
                    </td>
                    <td data-th="actions" width="20%">
                        <a href="<?= get_delete_post_link($rsvp_id) ?>" data-name="<?= $name ?>" class="button action delete-rsvp js-delete-rsvp">
                                <span class="dashicons dashicons-no"
                                      style="border-radius: 50%; background: darkred; color: white; padding: 6px;"
                                      title="Click to delete RSVP">Delete</span>
                        </a>
                        <a href="#" data-rsvp-id="<?= $rsvp_id ?>" data-email="<?= $email ?>" class="button action attended-email">
                                <span class="dashicons dashicons-email-alt"
                                      style="border-radius: 50%; background: deepskyblue; color: white; padding: 6px;"
                                      title="Click to email attendee">Email</span>
                        </a>
                    </td>
                </tr>

                <?php
            }
        }
        wp_reset_postdata();

        ?>
    </table>

    <?php
}

/**
 *  Confirm Logged-in user Can RSVP
 *  Returns: true
 */

function sdrt_vol_can_rsvp(): bool
{
    $userid = get_current_user_id();

    $role        = current_user_can('can_rsvp');
    $orientation = get_user_meta($userid, 'sdrt_orientation_attended', true);
    $coc         = get_user_meta($userid, 'sdrt_coc_consented', true);
    $waiver      = get_user_meta($userid, 'sdrt_waiver_consented', true);

    return $role
           && $orientation !== 'No'
           && $coc !== 'No'
           && $waiver !== 'No';
}

/**
 *  OUTPUT: Please Finish Volunteer Requirements
 */

function sdrt_finish_reqs()
{
    echo '<p>Thank you for starting the process of volunteering with SD Refugee Tutoring. Currently you are not yet eligble to volunteer for tutoring events. Please go to your <a href="' . get_home_url() . '/my-profile">Profile Page</a> to review your status and finish your required items.</p>';
}

/**
 *   OUTPUT: ALREADY RSVP'D OUTPUT
 *
 * @param int $eventId
 */

function sdrt_rsvp_already_rsvpd_output($eventId)
{
    $rsvp      = get_my_rsvp($eventId);
    $attending = get_post_meta($rsvp->ID, 'attending', true);

    if ($attending === 'no') {
        ?>
        <div class="already-rsvpd-no">
            <p><strong>Thanks for the heads up</strong></p>
            <p>It looks like you've already RSVP'd for this event. Sorry to hear you can't make it this time</p>
            <p>Please take a look at <a href="<?php
                echo esc_url(Tribe__Events__Main::instance()->getLink()); ?>">all our tutoring sessions</a> for more
                opportunities to volunteer.</p>
        </div>
        <?php
    } else { ?>
        <div class="already-rsvpd-yes">
            <p><strong>Thanks!</strong></p>
            <p>It looks like you've already RSVP'd for this event. We look forward to seeing you there!</p>
            <p>Need to cancel? <a href="<?php
                echo get_delete_post_link($rsvp->ID); ?>">Click here</a>.</p>
        </div>
        <?php
    }
}

/**
 *  OUTPUT: RSVP LIMIT REACHED
 */
function sdrt_rsvp_limit_reached_output()
{ ?>
    <div class="rsvps-closed">
        <p><strong>Sorry!</strong></p>
        <p>We already have the max number of Volunteers we need for this session.</p>
        <p>Please see our <a href="<?php
            echo site_url(); ?>/events">full Calendar</a> for future Tutoring Opportunities.</p>
    </div>
    <?php
}

/**
 *  OUTPUT: PLEASE REGISTER OUTPUT
 */
function sdrt_rsvp_please_register_output()
{ ?>
    <div class="please-register">
        <p><strong>Please Register to RSVP</strong></p>
        <p>All volunteers must first be registered and have passed a background check in order to RSVP. Please visit the
            <a href="<?php
            echo site_url(); ?>/volunteer">Registration page</a> for details.</p>
        <p><strong>Already Registered? Please Login</strong></p>
        <?php
        echo do_shortcode('[caldera_form id="CF597578b115ae1"]'); ?>
    </div>
    <?php
}

/**
 * GET VOLUNTEER IDS OF CURRENT EVENT RSVPS
 *
 * @param int $eventId
 *
 * @return array
 */
function get_current_rsvps_volids($eventId)
{
    global $wpdb;
    $rsvps = get_event_rsvps($eventId);

    $rsvpIds      = wp_list_pluck($rsvps, 'ID');
    $placeholders = implode(',', array_fill(0, count($rsvpIds), '%d'));

    return $wpdb->get_col(
        $wpdb->prepare(
            "SELECT meta_value from {$wpdb->postmeta} WHERE meta_key = 'volunteer_user_id' AND post_id IN ($placeholders)",
            $rsvpIds
        )
    );
}

/**
 * GET VOLUNTEER IDS OF CURRENT EVENT RSVPS
 *
 * @param int $eventId
 *
 * @return WP_Post
 */

function get_my_rsvp($eventId)
{
    $rsvps  = get_event_rsvps($eventId);
    $userid = get_current_user_id();

    foreach ($rsvps as $rsvp) {
        $volunteer_id = get_post_meta($rsvp->ID, 'volunteer_user_id', true);

        if ($volunteer_id === $userid) {
            return $rsvp;
        }
    }
}