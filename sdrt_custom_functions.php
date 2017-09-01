<?php
/**
 * Plugin Name: SDRT Custom Functions
 * Plugin URI: https://sdrefugeetutoring.com/
 * Description: Functionality plugin to test custom snippets for Give.
 * Author: Matt Cromwell
 * Author URI: https://www.mattcromwell.com
 * Version: 0.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin Folder Path
if ( ! defined( 'SDRT_FUNCTIONS_DIR' ) ) {
	define( 'SDRT_FUNCTIONS_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL
if ( ! defined( 'SDRT_FUNCTIONS_URL' ) ) {
	define( 'SDRT_FUNCTIONS_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'SDRT_FUNCTIONS_VERSION' ) && ( WP_DEBUG == true ) ) {
    define( 'SDRT_FUNCTIONS_VERSION', mt_rand() );
} elseif (! defined( 'SDRT_FUNCTIONS_VERSION' ) ) {
    define( 'SDRT_FUNCTIONS_VERSION', '1.0' );
}

// If we're debugging, hide admin all admin alerts

function load_custom_wp_admin_style() {
    wp_register_style( 'custom_wp_admin_css', SDRT_FUNCTIONS_URL . '/admin-style.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_css' );
}

if ( true == WP_DEBUG ) {
    add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');
}

add_action('admin_enqueue_scripts', 'sdrt_rsvp_admin_styles');

function sdrt_rsvp_admin_styles( $hook ) {
    global $post_type;

    if ( 'tribe_events' != $post_type )
        return;

    wp_register_style( 'sdrt_rsvp_admin_css', SDRT_FUNCTIONS_URL . 'assets/rsvp-admin-styles.css', false, mt_rand() );
    wp_enqueue_style( 'sdrt_rsvp_admin_css' );
}


// We want to check some things upon activation so we'll use this action to run everything first.
add_action( 'plugins_loaded', 'sdrt_includes' );

function sdrt_includes() {

	// Only load if the Events Calendar plugin is active
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		return false;
	} else {
		require_once( SDRT_FUNCTIONS_DIR . '/includes/tribe_events_customizations.php' );
		require_once( SDRT_FUNCTIONS_DIR . '/includes/tribe_events_custom_fields.php' );
	}

    // Includes Simple Send Email Class
	require_once( SDRT_FUNCTIONS_DIR . '/vendor/simple-send-email.php');
	require_once( SDRT_FUNCTIONS_DIR . '/includes/sdrt_emails.php');

	// Includes custom scripts/styles
	require_once( SDRT_FUNCTIONS_DIR . '/includes/sdrt_scripts.php');

	// Includes custom user meta functions
	require_once( SDRT_FUNCTIONS_DIR . '/includes/sdrt_user_meta.php');

    require_once( SDRT_FUNCTIONS_DIR . '/rsvps/rsvp_functions.php');

}