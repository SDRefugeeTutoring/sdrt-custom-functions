<?php
/**
 * Plugin Name: SDRT Custom Functions
 * Plugin URI: https://sdrefugeetutoring.com/
 * Description: Functionality plugin to test custom snippets for Give.
 * Author: Matt Cromwell
 * Author URI: https://www.mattcromwell.com
 * Version: 1.1
 */

// Exit if accessed directly.
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use SDRT\CustomFunctions\Boot;

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
    define( 'SDRT_FUNCTIONS_VERSION', '1.1' );
}

define('SDRT_ASSETS_DIR', SDRT_FUNCTIONS_DIR . 'assets/');
define('SDRT_ASSETS_URL', SDRT_FUNCTIONS_URL . 'assets/');

define('SDRT_ORIENTATION_FORM', 166);

// We want to check some things upon activation so we'll use this action to run everything first.
add_action( 'plugins_loaded', 'sdrt_includes' );

function sdrt_includes() {
	// Bootstrapper for all admin functions
	require_once( SDRT_FUNCTIONS_DIR . '/admin/_admin.php');

	// Bootstrapper for all custom includes
	require_once( SDRT_FUNCTIONS_DIR . '/includes/_inc.php');

	// Bootstrapper for all plugin customizations
	require_once( SDRT_FUNCTIONS_DIR . '/plugin_customizations/_plugins.php');

	// Bootstrapper for all registration functions
	require_once( SDRT_FUNCTIONS_DIR . '/registration/_registration.php');

	// Bootstrapper for the RSVP functionality
    require_once( SDRT_FUNCTIONS_DIR . '/rsvps/_rsvp.php');
}

/**
 * The app's service container helper function.
 *
 * @param string|null $concrete Class to retrieve from service container.
 *
 * @return Container|object The service container or class instance.
 * @throws DependencyException
 * @throws NotFoundException
 */
function sdrt(?string $concrete = null): object {
    static $container = null;

    if ($container === null) {
        $container = new Container();
    }

    return $concrete === null ? $container : $container->get($concrete);
}

require_once 'vendor/autoload.php';

sdrt(Boot::class)->begin();