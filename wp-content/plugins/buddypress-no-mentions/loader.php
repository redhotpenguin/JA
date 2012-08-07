<?php
/*
Plugin Name: BuddyPress No Mentions
Plugin URI: http://buddypress.org/community/groups/buddypress-no-mentions
Description: Disable the @mentions capability in BuddyPress. Perfect for users who don't understand Twitter!
Version: 1.0.1
Author: r-a-y
Author URI: http://buddypress.org/community/members/r-a-y
*/

function ray_bp_no_mentions_init() {
	require( dirname( __FILE__ ) . '/bp-no-mentions.php' );
}
add_action( 'bp_init', 'ray_bp_no_mentions_init' );

?>