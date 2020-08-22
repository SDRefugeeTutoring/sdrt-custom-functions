<?php

// Only load if the Events Calendar plugin is active
if ( ! class_exists( 'Tribe__Events__Main' ) ) {
	return false;
} else {
	require_once( SDRT_FUNCTIONS_DIR . '/plugin_customizations/tribe_events/tribe_events_customizations.php' );
	require_once( SDRT_FUNCTIONS_DIR . '/plugin_customizations/tribe_events/tribe_events_custom_fields.php' );
}

if ( ! class_exists( 'Give' ) ) {
	return false;
} else {
	require_once( SDRT_FUNCTIONS_DIR . '/plugin_customizations/givewp/givewp_functions.php' );
}
