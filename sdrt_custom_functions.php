<?php
/**
 * Plugin Name: SDRT Custom Functions
 * Plugin URI: https://sdrefugeetutoring.com/
 * Description: Functionality plugin to test custom snippets for Give.
 * Author: Matt Cromwell
 * Author URI: https://www.mattcromwell.com
 * Version: 0.1
 */

// Plugin Folder Path
if ( ! defined( 'SDRT_FUNCTIONS_DIR' ) ) {
	define( 'SDRT_FUNCTIONS_DIR', plugin_dir_path( __FILE__ ) );
}

// Plugin Folder URL
if ( ! defined( 'SDRT_FUNCTIONS_URL' ) ) {
	define( 'SDRT_FUNCTIONS_URL', plugin_dir_url( __FILE__ ) );
}


// We want to check some things upon activation so we'll use this action to run everything first.
add_action( 'plugins_loaded', 'sdrt_includes' );

function sdrt_includes() {

	// Only load if the Give Plugin is active
	if ( ! class_exists( 'Give' ) ) {
		return false;
	} else {
		include( SDRT_FUNCTIONS_DIR . '/includes/give_custom_fields.php' );
	}

	// Only load if the Events Calendar plugin is active
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		return false;
	} else {
		include( SDRT_FUNCTIONS_DIR . '/includes/tribe_events_customizations.php' );
	}

	// Includes custom scripts/styles
	include( SDRT_FUNCTIONS_DIR . '/includes/sdrt_scripts.php');

	// Includes custom user meta functions
	include( SDRT_FUNCTIONS_DIR . '/includes/sdrt_user_meta.php');

}

