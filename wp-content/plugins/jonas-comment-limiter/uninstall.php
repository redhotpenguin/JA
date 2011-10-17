<?php
	// If uninstall not called from WordPress exit
	if( !defined( 'WP_UNINSTALL_PLUGIN') )
	exit ();
	

	register_activation_hook( __FILE__, 'jp_comment_limiter_activate' );
	function jp_comment_limiter_activate() { //register the uninstall function
		register_uninstall_hook( __FILE__, 'jp_comment_limiter_uninstaller' );
	}
	
	function jp_comment_limiter_uninstaller() { //delete any options, tables, etc the plugin created
		delete_option( 'jp_comment_limiter_options' );
	}

	
	
?>