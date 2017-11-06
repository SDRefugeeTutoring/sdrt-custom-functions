<?php

$example_options = array(
	'page_title'  => 'RSVP Email Settings',
	'menu_title'  => 'Email Settings',
	'parent_slug' => 'edit.php?post_type=rsvp',
	'menu_slug'   => 'sdrt_email_settings',
	'capability'  => 'manage_options',
	'icon'        => 'dashicons-admin-generic',
);

$example_fields = array(
	'sdrt_email_section_1' => array(
		'title'   => 'Background Check Cleared Message',
		'type'    => 'section',
		'desc'    => 'This is the email that is sent to the volunteer when we get the all clear from Checkr that their background check cleared.',
	),
    'sdrt_checkr_clear_email_copy' => array(
		'title'   => 'Message',
		'type'    => 'textarea',
		'default' => 'Hello World!',
		'desc'    => 'The following email tags are supported: <ul ><li><code>{first_name}</code></li><li><code>{lost_password_link}</code></li></ul>',
		'sanit'   => 'html',
	),
	'sdrt_email_section_2' => array(
		'title'   => 'Thank you for attending Message',
		'type'    => 'section',
		'desc'    => 'This is sent to the volunteers as soon as their attendance is registered on a tutoring event.',
	),
	'sdrt_thanks_for_attending' => array(
		'title'   => 'Message',
		'type'    => 'textarea',
		'default' => 'Hello World!',
		'desc'    => 'The following email tags are supported: <ul ><li><code>{first_name}</code></li><li><code>{event_title}</code></li></ul>',
		'sanit'   => 'html',
	),

);

$example_settings = new HD_WP_Settings_API( $example_options, $example_fields );

add_action( 'admin_footer', 'sdrt_auto_expand_textarea' );

function sdrt_auto_expand_textarea() {
    $screen = get_current_screen();

    if ($screen->id == 'rsvp_page_sdrt_email_settings') {
        ?>
        <style>
            textarea.sdrt_textarea {
                display: block;
                overflow: hidden;
                padding:10px;
                margin: 0 0 10px 0;
                border-radius:3px;
                box-shadow:1px 1px 12px rgba(0,0,0,.1);
                border:0;
            }
        </style>
        <script type="text/javascript">
            jQuery(document).ready(function( $ ) {
                autosize($('textarea.sdrt_textarea'));
            });
        </script>

        <?php
    }
}

function pw_load_scripts($hook) {

	wp_register_script( 'sdrt_autoexpand', SDRT_FUNCTIONS_URL . 'assets/autosize.min.js', array('jquery'
    ) );

    $screen = get_current_screen();

	if ($screen->id == 'rsvp_page_sdrt_email_settings') {
		wp_enqueue_script('sdrt_autoexpand');
    }

}

add_action('admin_enqueue_scripts', 'pw_load_scripts');