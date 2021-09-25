<?php
/*-------------------------------------------------------------------------------
	Custom Columns
-------------------------------------------------------------------------------*/

// Change the columns for the edit CPT screen
function sdrt_rsvp_change_columns( $cols ) {
	$cols = array(
		'cb'       => '<input type="checkbox" />',
		'event'      => __( 'Event', 'sdrt' ),
		'event_date' => __( 'Event Date', 'sdrt' ),
		'vol_name'     => __( 'Volunteer Name', 'sdrt' ),
		'attended'     => __( 'Attended?', 'sdrt' ),
	);
	return $cols;
}
add_filter( 'manage_rsvp_posts_columns', 'sdrt_rsvp_change_columns' );

function sdrt_rsvp_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'event':
			$eventid = get_post_meta( $post_id, 'event_id', true);
			$event_url = admin_url("post.php?post=$eventid&action=edit");
			echo '<a href="' . $event_url . '">' . get_the_title($eventid). '</a>';
			break;
		case 'event_date':
			$eventdate = strtotime( get_post_meta( $post_id, 'event_date', true) );

			echo date( 'F d, Y', $eventdate );
			break;
		case 'vol_name':
			echo get_post_meta( $post_id, 'volunteer_name', true);
			break;
		case 'attended':
			echo get_post_meta( $post_id, 'attended', true);
			break;
	}
}

add_action( 'manage_rsvp_posts_custom_column', 'sdrt_rsvp_column_content', 10, 2 );
