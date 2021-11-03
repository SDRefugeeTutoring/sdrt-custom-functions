<?php

add_filter('posts_join', function($join, WP_Query $query) {
    global $wpdb;

    if ($query->is_admin && $query->is_search() && $query->query_vars['post_type'] === 'rsvp') {
        $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}, 10, 2);

add_filter('posts_where', function($where, WP_Query $query) {
    global $wpdb;

    if ($query->is_admin && $query->is_search() && $query->query_vars['post_type'] === 'rsvp') {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)", $where);
    }

    return $where;
}, 10, 2);

add_filter('posts_groupby', function($groupBy, WP_Query $query) {
    global $wpdb;

    if ($query->is_admin && $query->is_search() && $query->query_vars['post_type'] === 'rsvp') {
        return " $wpdb->posts.ID";
    }

    return $groupBy;
}, 10, 2);