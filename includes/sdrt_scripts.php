<?php

/**
 *  FRONT-END SCRIPTS
 * Enqueues Scripts and Styles for front-end display and functionality
 *
 *
 */

add_action( 'wp_enqueue_scripts', 'sdrt_enqueue_tableExport_scripts' );

function sdrt_enqueue_tableExport_scripts() {

	if ( is_singular('tribe_events') && current_user_can('can_view_rsvps') ) {
		wp_enqueue_script('sdrt-exportHTML', SDRT_FUNCTIONS_URL . 'assets/exportHTML.js', false);
		wp_register_style('rsvp-styles', SDRT_FUNCTIONS_URL . 'assets/rsvp-styles.css', array(), SDRT_FUNCTIONS_VERSION, 'all');

		wp_enqueue_style('rsvp-styles');
    }


}


/**
 *  BACK-END SCRIPTS
 *  Enqueues scripts for wp-admin purposes only
 */


// If we're debugging, hide admin all admin alerts

if ( true == WP_DEBUG ) {
	add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');
}

function load_custom_wp_admin_style() {
	wp_register_style( 'custom_wp_admin_css', SDRT_FUNCTIONS_URL . '/assets/admin-style.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );
}

//Enqueue admin scripts
add_action('admin_enqueue_scripts', 'sdrt_rsvp_admin_styles');

function sdrt_rsvp_admin_styles( $hook ) {
    $screen = get_current_screen();
	
	wp_register_style( 'custom_wp_admin_css', SDRT_FUNCTIONS_URL . '/assets/admin-style.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );
	wp_enqueue_style( 'font-awesome-5', 'https://use.fontawesome.com/releases/v5.9.0/css/all.css', array(), FL_THEME_VERSION );

	if ( $screen->base === 'user-edit' ) {
        wp_enqueue_script('sdrt_user_edit', SDRT_FUNCTIONS_URL . '/assets/admin-user-edit.js', [], '', true);
    }

	global $post_type;

	// Only for the Events Calendar Edit pages
	if ( 'tribe_events' != $post_type )
		return;

	wp_register_style( 'sdrt_rsvp_admin_css', SDRT_FUNCTIONS_URL . 'assets/rsvp-admin-styles.css', false, mt_rand() );
	wp_enqueue_style( 'sdrt_rsvp_admin_css' );
}

