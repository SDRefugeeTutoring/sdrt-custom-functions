<?php

add_filter( 'posts_join', 'segnalazioni_search_join' );
function segnalazioni_search_join ( $join ) {
	global $pagenow, $wpdb;

	// I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni".
	if ( is_admin() && 'edit.php' === $pagenow && 'rsvp' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
		$join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
	}
	return $join;
}

add_filter( 'posts_where', 'segnalazioni_search_where' );
function segnalazioni_search_where( $where ) {
	global $pagenow, $wpdb;

	// I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni".
	if ( is_admin() && 'edit.php' === $pagenow && 'rsvp' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
		$where = preg_replace(
			"/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
			"(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)", $where );
	}
	return $where;
}