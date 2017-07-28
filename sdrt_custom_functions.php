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


// We want to check some things upon activation so we'll use this action to run everything first.
add_action( 'plugins_loaded', 'sdrt_includes' );

function sdrt_includes() {

	// Only load if the Events Calendar plugin is active
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		return false;
	} else {
		include( SDRT_FUNCTIONS_DIR . '/includes/tribe_events_customizations.php' );
        include( SDRT_FUNCTIONS_DIR . '/includes/tribe_events_custom_fields.php' );
	}

    /*if ( ! defined( 'NOTIFICATION_URL' ) ) {
	    return false;
    } else {
        include( SDRT_FUNCTIONS_DIR . '/includes/sdrt_notifications.php' );
    }*/

    // Includes Simple Send Email Class
    include( SDRT_FUNCTIONS_DIR . '/vendor/simple-send-email.php');
    include( SDRT_FUNCTIONS_DIR . '/includes/sdrt_emails.php');

	// Includes custom scripts/styles
	include( SDRT_FUNCTIONS_DIR . '/includes/sdrt_scripts.php');

	// Includes custom user meta functions
	include( SDRT_FUNCTIONS_DIR . '/includes/sdrt_user_meta.php');

    if ( post_type_exists( 'rsvp' ) && class_exists('acf') ) {
        include( SDRT_FUNCTIONS_DIR . '/rsvps/rsvp_functions.php');
    }

    include( SDRT_FUNCTIONS_DIR . '/rsvps/registration.php');

}

/**
 *  WPUM Functions
 */
add_filter('wpum_get_user_profile_tabs', 'sdrt_rsvp_profile_tab');

function sdrt_rsvp_profile_tab()
{
    $tabs = array();

    $tabs['about'] = array(
        'id'       => 'profile_details',
        'title'    => __( 'Overview', 'wpum' ),
        'slug'     => 'about',
    );

    $tabs['posts'] = array(
        'id'       => 'profile_posts',
        'title'    => __( 'Posts', 'wpum' ),
        'slug'     => 'posts',
    );

    $tabs['comments'] = array(
        'id'       => 'profile_comments',
        'title'    => __( 'Comments', 'wpum' ),
        'slug'     => 'comments',
    );

    // Remove tabs if they're not active
    if ( !wpum_get_option( 'profile_posts' ) ) // remove posts tab
        unset( $tabs['posts'] );

    if ( !wpum_get_option( 'profile_comments' ) ) // Remove comments tab
        unset( $tabs['comments'] );

    $tabs['rsvp'] = array(
        'id' => 'profile_rsvps',
        'title' => __('RSVPs', 'wpum'),
        'slug' => 'rsvps',
    );

    return $tabs;
}

/**
 * Load content for the "posts" tab.
 *
 * @since 1.0.0
 * @access public
 * @param object $user_data holds WP_User object
 * @param array $tabs holds all the registered tabs
 * @param string $current_tab_slug the slug of the current tab
 * @return void
 */
function wpum_profile_tab_content_rsvps( $user_data, $tabs, $current_tab_slug ) {

    echo get_wpum_template( 'profile/profile-rsvps.php', array( 'user_data' => $user_data, 'tabs' => $tabs, 'slug' => $current_tab_slug ) );

}
add_action( 'wpum_profile_tab_content_rsvps', 'wpum_profile_tab_content_rsvps', 11, 3 );


