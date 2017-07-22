<?php
/**
 *   Outputs the Give RSVP form
 *   and the RSVPs for that event
 *   to the end of the Event content
 *
 */

add_action('tribe_events_single_event_after_the_meta', 'embed_rsvp_events_single');

function embed_rsvp_events_single() {
    $get_limit      = get_post_meta( get_the_ID(), 'rsvps_limit', true );
    $rsvp_enabled   = get_post_meta( get_the_ID(), 'enable_rsvps', true );
    $rsvp_limit     = ( !empty($get_limit) ? $get_limit : '');

    if ( $rsvp_enabled == 'enabled' ) :

        $rsvp_total = get_rsvp_count();

        echo '<div class="tutoring-rsvp"><h2 class="give-title">RSVP HERE:</h2>';

        if ( $rsvp_limit > 0 && $rsvp_total >= $rsvp_limit ) : ?>
            <div class="rsvps-closed">
                <p><strong>Sorry!</strong></p>
                <p>We already have the max number of Volunteers we need for this session.</p>
                <p>Please see our <a href="<?php echo site_url(); ?>/events">full Calendar</a> for future Tutoring Opportunities.</p>
            </div>
        <?php else :

        echo do_shortcode('[caldera_form id="' . RSVP_FORM_ID . '"]');
        echo '</div>'; //end RSVP section

        /**
         *   CURRENT RSVPS
         */
        $args = array(
            'post_type'      => 'give_payment',
            'post_status' => array('pending', 'abandoned'),
        );

        global $post;
        $loop = new WP_Query( $args );

        if ( $loop->have_posts() && current_user_can('update_plugins') ) :
            $eventdate = get_post_meta($post->ID, '_EventStartDate', true);
            $createDate = new DateTime($eventdate);
            $finaldate = $createDate->format('F d, Y');
            ?>

            <h2 class="give-title current_rsvps">Current RSVPS:</h2>
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
                <td>Teacher?</td>
                <td>Referral</td>
                </tr>
                <?php

                global $post;

                $pageid = $post->ID;

                while ( $loop->have_posts() ) : $loop->the_post();
                    $meta      = get_post_meta( get_the_ID() );
                    $getmeta   = maybe_unserialize( $meta['_give_payment_meta'][0] );
                    $firstname = $getmeta['user_info']['first_name'];
                    $lastname  = $getmeta['user_info']['last_name'];
                    $email     = $getmeta['user_info']['email'];
                    $rsvp_id   = $getmeta['event_id'];
                    $teacher    = $meta['are_you_an_accredited_teacher'][0];
                    $referral    = $meta['how_did_you_hear_about_us'][0];

                    if ( $pageid == $rsvp_id ) {

                        ?>
                        <tr>
                            <td data-th="name"><?php echo $firstname . ' ' . $lastname; ?></td>
                            <td data-th="email"><?php echo $email; ?></td>
                            <td data-th="teacher"><?php echo $teacher; ?></td>
                            <td data-th="referral"><?php echo $referral; ?></td>
                        </tr>

                        <?php
                    }

                endwhile;
                wp_reset_postdata();

                ?>
            </table>

        <?php endif;
        wp_reset_query();
        endif;
    endif;
}

function get_rsvp_count() {
    $countargs = array(
        'post_type'      => 'give_payment',
        'post_status' => 'pending'
    );

    $countloop = new WP_Query( $countargs );
    $counter = 0;
    global $post;
    $pageid = $post->ID;

    if ( $countloop->have_posts() ) : while ( $countloop->have_posts() ) : $countloop->the_post();

        $meta      = get_post_meta( get_the_ID() );
        $getmeta   = maybe_unserialize( $meta['_give_payment_meta'][0] );
        $rsvp_id   = $getmeta['event_id'];

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