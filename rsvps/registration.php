<?php
add_action( 'setup_theme', function() {
    $rsvpid = $_GET['rsvpid'];
    $attended = $_GET['attended'];
    if ( current_user_can('update_plugins') ) {

        if ( isset($rsvpid, $attended) ) {
            update_post_meta( $rsvpid, 'attended', $attended );

        }
    }
});