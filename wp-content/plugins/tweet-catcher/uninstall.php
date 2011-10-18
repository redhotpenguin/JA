<?php
if (!function_exists ('is_admin')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
if( !defined( 'WP_UNINSTALL_PLUGIN') ) 	exit ();
	
	tweet_catcher_uninstall();
	function tweet_catcher_uninstall() { //delete any options, tables, etc the plugin created
			global $wpdb;
			$table_name = $wpdb->prefix . "postmeta";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$wpdb->query("DELETE FROM `wp_postmeta` WHERE meta_key LIKE 'jptc%'");
	}
?>