<?php
/* -------------------------------------
 *      MISSING WORDPRESS FUNCTIONS
 * ------------------------------------- */

/**
 * Retrieve last page modified date depending on timezone.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is just when the last post was modified. The
 * 'gmt' is when the last post was modified in GMT time.
 *
 * Adaptation of get_lastpostmodified defined in wp-includes/post.php since 1.2.0
 *
 * @uses $wpdb
 * @uses $blog_id
 * @uses apply_filters() Calls 'get_lastpagemodified' filter
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date the post was last modified.
 */
if( !function_exists('get_lastpagemodified') ) {
 function get_lastpagemodified($timezone = 'server') {
	global $wpdb;

	$add_seconds_server = date('Z');
	$timezone = strtolower( $timezone );

	$lastpagemodified = wp_cache_get( "lastpagemodified:$timezone", 'timeinfo' );
	if ( $lastpagemodified )
		return apply_filters( 'get_lastpagemodified', $lastpagemodified, $timezone );

	switch ( strtolower($timezone) ) {
		case 'gmt':
			$lastpagemodified = $wpdb->get_var("SELECT post_modified_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_modified_gmt DESC LIMIT 1");
			break;
		case 'blog':
			$lastpagemodified = $wpdb->get_var("SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_modified_gmt DESC LIMIT 1");
			break;
		case 'server':
			$lastpagemodified = $wpdb->get_var("SELECT DATE_ADD(post_modified_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_modified_gmt DESC LIMIT 1");
			break;
	}

	$lastpagedate = get_lastpagedate($timezone);
	if ( $lastpagedate > $lastpagemodified )
		$lastpagemodified = $lastpagedate;

	if ( $lastpagemodified )
		wp_cache_set( "lastpagemodified:$timezone", $lastpagemodified, 'timeinfo' );

	return apply_filters( 'get_lastpagemodified', $lastpagemodified, $timezone );
 }
}

/**
 * Retrieve the date that the last page was published.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * Adaptation of get_lastpostdate defined in wp-includes/post.php since 0.71
 *
 * @uses $wpdb
 * @uses $blog_id
 * @uses apply_filters() Calls 'get_lastpagedate' filter
 *
 * @global mixed $cache_lastpagedate Stores the last post date
 * @global mixed $pagenow The current page being viewed
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the last post.
 */
if( !function_exists('get_lastpagedate') ) {
 function get_lastpagedate($timezone = 'server') {
	global $cache_lastpagedate, $wpdb, $blog_id;
	$add_seconds_server = date('Z');
	if ( !isset($cache_lastpagedate[$blog_id][$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$lastpagedate = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'blog':
				$lastpagedate = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
			case 'server':
				$lastpagedate = $wpdb->get_var("SELECT DATE_ADD(post_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_date_gmt DESC LIMIT 1");
				break;
		}
		$cache_lastpagedate[$blog_id][$timezone] = $lastpagedate;
	} else {
		$lastpagedate = $cache_lastpagedate[$blog_id][$timezone];
	}
	return apply_filters( 'get_lastpagedate', $lastpagedate, $timezone );
 }
}

/**
 * Retrieve first post modified date depending on timezone.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * Reverse of get_lastpostmodified defined in wp-includes/post.php since WP 1.2.0
 *
 * @uses $wpdb
 * @uses apply_filters() Calls 'get_firstpostmodified' filter
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the oldest modified post.
 */
if( !function_exists('get_firstpostmodified') ) {
 function get_firstpostmodified($timezone = 'server') {
	global $wpdb;

	$add_seconds_server = date('Z');
	$timezone = strtolower( $timezone );

	$firstpostmodified = wp_cache_get( "firstpostmodified:$timezone", 'timeinfo' );
	if ( $firstpostmodified )
		return apply_filters( 'get_firstpostmodified', $firstpostmodified, $timezone );

	switch ( strtolower($timezone) ) {
		case 'gmt':
			$firstpostmodified = $wpdb->get_var("SELECT post_modified_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_modified_gmt ASC LIMIT 1");
			break;
		case 'blog':
			$firstpostmodified = $wpdb->get_var("SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_modified_gmt ASC LIMIT 1");
			break;
		case 'server':
			$firstpostmodified = $wpdb->get_var("SELECT DATE_ADD(post_modified_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_modified_gmt ASC LIMIT 1");
			break;
	}

	$firstpostdate = get_firstpostdate($timezone);
	if ( $firstpostdate > $firstpostmodified )
		$firstpostmodified = $firstpostdate;

	if ( $firstpostmodified )
		wp_cache_set( "firstpostmodified:$timezone", $firstpostmodified, 'timeinfo' );

	return apply_filters( 'get_firstpostmodified', $firstpostmodified, $timezone );
 }
}

/**
 * Retrieve first page modified date depending on timezone.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * Adaptation of get_firstpostmodified defined in this file
 *
 * @uses $wpdb
 * @uses apply_filters() Calls 'get_firstpagemodified' filter
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the oldest modified page.
 */
if( !function_exists('get_firstpagemodified') ) {
 function get_firstpagemodified($timezone = 'server') {
	global $wpdb;

	$add_seconds_server = date('Z');
	$timezone = strtolower( $timezone );

	$firstpagemodified = wp_cache_get( "firstpagemodified:$timezone", 'timeinfo' );
	if ( $firstpagemodified )
		return apply_filters( 'get_firstpagemodified', $firstpagemodified, $timezone );

	switch ( strtolower($timezone) ) {
		case 'gmt':
			$firstpagemodified = $wpdb->get_var("SELECT post_modified_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_modified_gmt ASC LIMIT 1");
			break;
		case 'blog':
			$firstpagemodified = $wpdb->get_var("SELECT post_modified FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_modified_gmt ASC LIMIT 1");
			break;
		case 'server':
			$firstpagemodified = $wpdb->get_var("SELECT DATE_ADD(post_modified_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_modified_gmt ASC LIMIT 1");
			break;
	}

	$firstpagedate = get_firstpagedate($timezone);
	if ( $firstpagedate > $firstpagemodified )
		$firstpagemodified = $firstpagedate;

	if ( $firstpagemodified )
		wp_cache_set( "firstpagemodified:$timezone", $firstpagemodified, 'timeinfo' );

	return apply_filters( 'get_firstpagemodified', $firstpagemodified, $timezone );
 }
}

/**
 * Retrieve the date that the first post was published.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * Reverse of get_lastpostdate defined in wp-includes/post.php since 0.71
 *
 * @uses $wpdb
 * @uses $cache_firstpostdate
 * @uses $blog_id
 * @uses apply_filters() Calls 'get_firstpostdate' filter
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the last post.
 */
if( !function_exists('get_firstpostdate') ) {
 function get_firstpostdate($timezone = 'server') {
	global $cache_firstpostdate, $wpdb, $blog_id;
	$add_seconds_server = date('Z');
	if ( !isset($cache_firstpostdate[$blog_id][$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$firstpostdate = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date_gmt ASC LIMIT 1");
				break;
			case 'blog':
				$firstpostdate = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date_gmt ASC LIMIT 1");
				break;
			case 'server':
				$firstpostdate = $wpdb->get_var("SELECT DATE_ADD(post_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date_gmt ASC LIMIT 1");
				break;
		}
		$cache_firstpostdate[$blog_id][$timezone] = $firstpostdate;
	} else {
		$firstpostdate = $cache_firstpostdate[$blog_id][$timezone];
	}
	return apply_filters( 'get_firstpostdate', $firstpostdate, $timezone );
 }
}

/**
 * Retrieve the date that the first post was published.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * Adaptation of get_firstpostdate defined in this file
 *
 * @uses $wpdb
 * @uses $cache_firstpagedate
 * @uses $blog_id
 * @uses apply_filters() Calls 'get_firstpagedate' filter
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the last post.
 */
if( !function_exists('get_firstpagedate') ) {
 function get_firstpagedate($timezone = 'server') {
	global $cache_firstpagedate, $wpdb, $blog_id;
	$add_seconds_server = date('Z');
	if ( !isset($cache_firstpagedate[$blog_id][$timezone]) ) {
		switch(strtolower($timezone)) {
			case 'gmt':
				$firstpagedate = $wpdb->get_var("SELECT post_date_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_date_gmt ASC LIMIT 1");
				break;
			case 'blog':
				$firstpagedate = $wpdb->get_var("SELECT post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_date_gmt ASC LIMIT 1");
				break;
			case 'server':
				$firstpagedate = $wpdb->get_var("SELECT DATE_ADD(post_date_gmt, INTERVAL '$add_seconds_server' SECOND) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY post_date_gmt ASC LIMIT 1");
				break;
		}
		$cache_firstpagedate[$blog_id][$timezone] = $firstpagedate;
	} else {
		$firstpagedate = $cache_firstpagedate[$blog_id][$timezone];
	}
	return apply_filters( 'get_firstpagedate', $firstpagedate, $timezone );
 }
}

/**
 * Retrieve first post/page modified date depending on timezone.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * Combination of get_firstpostmodified and get_firstpagemodified
 * defined in this file
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the oldest modified post or page.
 */
if( !function_exists('get_firstmodified') ) {
 function get_firstmodified($timezone = 'server') {
	$firstpostmodified = get_firstpostmodified($timezone);
	$firstpagemodified = get_firstpagemodified($timezone);
	if ( mysql2date('U',$firstpostmodified) < mysql2date('U',$firstpagemodified) )
		return $firstpostmodified;
	else
		return $firstpagemodified;
 }
}

/**
 * Retrieve last post/page modified date depending on timezone.
 *
 * The server timezone is the default and is the difference between GMT and
 * server time. The 'blog' value is the date when the last post was posted. The
 * 'gmt' is when the last post was posted in GMT formatted date.
 *
 * Combination of get_lastpostmodified and get_lastpagemodified
 * defined in wp-includes/post.php since WP 1.2.0
 *
 * @param string $timezone The location to get the time. Can be 'gmt', 'blog', or 'server'.
 * @return string The date of the oldest modified post.
 */
if( !function_exists('get_lastmodified') ) {
 function get_lastmodified($timezone = 'server') {
	$lastpostmodified = get_lastpostmodified($timezone);
	$lastpagemodified = get_lastpagemodified($timezone);
	if ( mysql2date('U',$lastpostmodified) > mysql2date('U',$lastpagemodified) )
		return $lastpostmodified;
	else
		return $lastpagemodified;
 }
}
