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
                    if ( $rsvp_limit > 0 && $rsvp_total >= $rsvp_limit ) { ?>
                        <div class="rsvps-closed">
                            <p><strong>Sorry!</strong></p>
                            <p>We already have the max number of Volunteers we need for this session.</p>
                            <p>Please see our <a href="<?php echo site_url(); ?>/events">full Calendar</a> for future Tutoring Opportunities.</p>
                        </div>
                        <?php

                        // Else if already RSVP'd show message
                    } else {

                        echo do_shortcode('[caldera_form id="' . $rsvp_form . '"]');
                    }

                // Inform visitor to register if login is required
                } elseif ( $must_login == 'yes' && ! is_user_logged_in() ) { ?>
                    <div class="please-register">
                        <p><strong>Please Register to RSVP</strong></p>
                        <p>All volunteers must first be registered and have passed a background check in order to RSVP. Please visit the Registration page for details.</p>
                        <p><strong>Already Registered? Please Login</strong></p>
                        <?php echo do_shortcode('[caldera_form id="CF597578b115ae1"]');?>

                    </div>
                <?php } elseif ( $rsvp_limit > 0 && $rsvp_total >= $rsvp_limit ) { ?>
                    <div class="rsvps-closed">
                        <p><strong>Sorry!</strong></p>
                        <p>We already have the max number of Volunteers we need for this session.</p>
                        <p>Please see our <a href="<?php echo site_url(); ?>/events">full Calendar</a> for future Tutoring Opportunities.</p>
                    </div>
                <?php

                // Else if already RSVP'd show message
                } elseif ( in_array($userid, $rsvpmeta ) ) { ?>
                    <div class="already-rsvpd">
                        <p><strong>Thanks!</strong></p>
                        <p>It looks like you've already RSVP'd for this event. We look forward to seeing you there!</p>
                    </div><?php

                // Or finally show the RSVP form
                } else {
                    echo do_shortcode('[caldera_form id="' . RSVP_FORM_ID . '"]');
                }
                ?>
        </div><!-- end RSVP section -->

        <?php

        // Outputs the RSVP Registration Table
        // Volunteers can register their attendance
        // by clicking on the "X" next to their name
        // This is only viewable by a logged-in Admin account

        if ( !empty($rsvps) && current_user_can('update_plugins') ) :

            $createDate = new DateTime($eventdate);
            $finaldate = $createDate->format('F d, Y');
            ?>

            <h2 class="give-title current_rsvps" id="rsvps">Current RSVPS:</h2>

            <button class="rsvp-download" onClick ="jQuery('#RSVPs').tableExport({type:'pdf',escape:'false', htmlContent:'true'});">Download RSVPs</button>

            <table class="rwd-table" id="RSVPs">
                <thead>
                    <th colspan="4">
                        Tutoring on <?php echo $finaldate; ?>
                    </th>
                </thead>
                <tr class="labels">
                    <td>Name</td>
                    <td>Email</td>
                    <td>Attended?</td>
                </tr>
                <?php

                foreach( $rsvps as $rsvp ) {

                    $rsvpid = $rsvp->ID;
                    $getmeta = get_fields($rsvpid);

                    $name = $getmeta['volunteer_name'];
                    $email = $getmeta['volunteer_email'];
                    $attended = $getmeta['attended'];

                    ?>
                    <tr>
                        <td data-th="name"><?php echo $name; ?></td>
                        <td data-th="email"><?php echo $email; ?></td>
                        <?php if ($attended == 'no') { ?>
                            <td data-th="attended"><a
                                        href="<?php echo get_permalink(get_the_ID()) . '?rsvpid=' . $rsvpid . '&attended=yes#rsvps'; ?>"
                                        class="button attended-no"><span class="dashicons dashicons-no" style="border-radius: 50%; background: darkred; color: white; padding: 6px;" title="Click to change to Yes"></span></a>
                            </td>
                        <?php } elseif ( $attended == 'unknown' ) { ?>
                            <td data-th="attended"><a
                                        href="<?php echo get_permalink(get_the_ID()) . '?rsvpid=' . $rsvpid . '&attended=yes#rsvps'; ?>"
                                        class="button attended-unknown"><span class="dashicons dashicons-minus" style="border-radius: 50%; background: #777777; color: white; padding: 6px;" title="Click to change to Yes"></span></a>
                            </td>
                        <?php } else { ?>
                            <td data-th="attended"><span class="dashicons dashicons-yes" style="border-radius: 50%; background: forestgreen; color: white; padding: 6px;"></span></td>
                        <?php } ?>
                    </tr>

                    <?php
                }
                wp_reset_postdata();

                ?>
            </table>

        <?php
        endif;
    endif;
}


/**
 * GET RSVPS FOR CURRENT EVENT
 *
 * @param string $rsvpdate
 * @return array
 */

function get_current_rsvps($rsvpdate = '') {

    $rsvps = array();

    $args = array(
        'post_type'   => 'rsvp',
        'post_status' => array('publish'),
        'meta_query' => array(
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

function get_current_rsvps_volids($rsvpdate = '') {
    global $post;
    $eventdate = get_post_meta($post->ID, '_EventStartDate', true);
    $rsvps = get_current_rsvps($rsvpdate = $eventdate);

    $volid = array();

    foreach( $rsvps as $rsvp ) {

        $ids = $rsvp->ID;
        $getmeta = get_fields($ids);
        $volid[] = $getmeta['volunteer_user_id'];

    }

    return $volid;

}