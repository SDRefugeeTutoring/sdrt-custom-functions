<?php
/**
 * Custom Functions for the "Notifications" Plugin
 */

// Move action so Notifiations pulls CPTUI cpts correctly
// See here: https://github.com/Kubitomakita/Notification/issues/29

add_action('init', 'sdrt_tweak_actions', 1 );

function sdrt_tweak_actions() {
    remove_action( 'init', 'cptui_create_custom_post_types' );

    add_action( 'init', 'cptui_create_custom_post_types', 7 );
}

function sdrt_add_rsvp_yes_trigger() {

    register_trigger( array(
        'slug'     => 'sdrt/rsvp/yes',
        'name'     => __( 'RSVP changed to YES', 'sdrt' ),
        'group'    => __( 'Rsvp', 'sdrt' ),
        'template' => 'This is default template using {merge_tag}. It can accept <strong>HTML</strong>',
        'tags'     => array(
            'attended'    => 'string',
        )
    ) );

}
add_action( 'init', 'sdrt_add_rsvp_yes_trigger' );

add_action( 'sdrt_rsvp_yes', 'sdrt_trigger_rsvp_yes_email');

function sdrt_trigger_rsvp_yes_email() {

    $message = 'Dear {first_name},<br /><br />';
    $message .= 'Thanks so much for volunteering for {event_date} and attending.';

    $trigger_slug = 'sdrt/rsvp/yes';
    $merge_tags = array( 'attended'=> $message );

    if ( is_notification_defined( $trigger_slug ) ) {
        notification( $trigger_slug, array(
            'page_ID'    => '25',
            'page_url'   => get_permalink( '25' ),
            'user_email' => 'info@mattcromwell.com',
        ) );
    }

}