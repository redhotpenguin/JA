<?php
if (!function_exists ('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
if( !defined( 'WP_UNINSTALL_PLUGIN') ) 	exit ();
	
	tweet_catcher_uninstall();
	function tweet_catcher_uninstall() { // Aren't we all taught to always clean our mess? 
			global $wpdb;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			// delete all traces of Tweet Catcher from _postmeta
			$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key LIKE 'jptc%'");
			
			// delete all options set by Tweet Catcher
			delete_option('jptc_tweet_catching');
			delete_option('jptc_follow_btn');
			delete_option('jptc_exluded_users');
			delete_option('jptc_email');
			delete_option('jptc_email_notification');

			
			
			// delete the scheduled task
			$timestamp = wp_next_scheduled( 'jptc_hook' );
			wp_unschedule_event($timestamp, 'jptc_hook' );
	}
?>