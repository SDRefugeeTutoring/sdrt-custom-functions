<?php
/**
 *   Outputs the Give RSVP form
 *   and the RSVPs for that event
 *   to the end of the Event content
 *
 */

add_action('tribe_events_single_event_after_the_meta', 'embed_rsvp_events_single');

function embed_rsvp_events_single() {
    global $post;

    $get_limit      = get_post_meta( get_the_ID(), 'rsvps_limit', true );
    $rsvp_enabled   = get_post_meta( get_the_ID(), 'enable_rsvps', true );
    $rsvp_limit     = ( !empty($get_limit) ? $get_limit : '');
    $eventdate = get_post_meta($post->ID, '_EventStartDate', true);
    $rsvps = get_current_rsvps($rsvpdate = $eventdate);
    $rsvpmeta = get_current_rsvps_volids($rsvpdate = $eventdate);
    $userid = get_current_user_id();

    if ( $rsvp_enabled == 'enabled' ) :

        $rsvp_total = get_rsvp_count(); ?>

        <div class="tutoring-rsvp">
            <h2 class="give-title">RSVP HERE:</h2>
            <?php
                // Show message if RSVP limit is reached
                if ( $rsvp_limit > 0 && $rsvp_total >= $rsvp_limit ) { ?>
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
                // Or finally show form
                } else {
                    echo do_shortcode('[caldera_form id="' . RSVP_FORM_ID . '"]');
                }
                ?>
        </div><!-- end RSVP section -->

        <?php

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
                <tr>
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
                                        class="button attended-<?php echo $attended; ?>"><?php echo ucfirst($attended); ?></a>
                            </td>
                        <?php } else { ?>
                            <td data-th="attended">Yes</td>
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

function get_rsvp_count() {
    $countargs = array(
        'post_type'      => 'rsvp',
        'post_status' => array('publish'),
    );

    $countloop = new WP_Query( $countargs );
    $counter = 0;
    global $post;
    $pageid = $post->ID;

    if ( $countloop->have_posts() ) : while ( $countloop->have_posts() ) : $countloop->the_post();

        $meta      = get_post_meta( get_the_ID() );

        $rsvp_id   = $meta['event_id'];

        if ( $pageid == $rsvp_id ) {
            $counter++;
        }

    endwhile;
        $rsvp_total = ( !empty($counter) ? $counter : true);
        wp_reset_postdata();
    endif;
    wp_reset_query();

    return $counter;
}