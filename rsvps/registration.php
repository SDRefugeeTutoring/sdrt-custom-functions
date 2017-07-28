<?php
add_action( 'setup_theme', function() {
    $rsvpid = $_GET['rsvpid'];
    $attended = $_GET['attended'];

    $rsvpmeta = get_post_meta($rsvpid, '', true);
    $volname = $rsvpmeta['volunteer_name'][0];
    $volemail = $rsvpmeta['volunteer_email'][0];
    //var_dump($rsvpmeta);

    ob_start(); ?>
    <h2>Hi <?php echo $volname; ?></h2>

    <p>Thanks so much for attending tutoring yesterday.</p>

    <?php
    $message = ob_get_clean();

    if ( current_user_can('update_plugins') ) {

        if ( isset($rsvpid, $attended) ) {
            update_post_meta( $rsvpid, 'attended', $attended );

            $emailargs = array(
                'toemail'       => $volemail,
                'tosubject'     => 'New Email!!',
                'emailcontent'  => $message
            );

            sdrt_trigger_email( $emailargs );
        }
    }
});