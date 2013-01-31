<?php
if ( !defined( 'PB_IMPORTBUDDY' ) ) { // NOT IN IMPORTBUDDY:
	pb_backupbuddy::load_script( 'admin.js' );
	
	
	
	pb_backupbuddy::$ui->title( __( 'Server Information', 'it-l10n-backupbuddy' ) . ' ' . pb_backupbuddy::video( '7NI7oePvxZg', __( 'Server information', 'it-l10n-backupbuddy' ), false ) );
	pb_backupbuddy::$classes['core']->versions_confirm();
	
	
	
	pb_backupbuddy::$ui->start_metabox( __( 'Server Configuration', 'it-l10n-backupbuddy' ) . ' ' .pb_backupbuddy::video( 'XfZy-7DdbS0#t=0m14s', __( 'Server Configuration', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%;' );
	require_once( 'server_info/server.php' );
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	pb_backupbuddy::$ui->start_metabox( __( 'Recent Remote File Transfers', 'it-l10n-backupbuddy' ) . ' ' . pb_backupbuddy::tip( 'Below is a list of recent file transfers to remote destinations. This is useful for verifying file sending and troubleshooting.', '', false ) . ' ' . pb_backupbuddy::video( '7NI7oePvxZg#36', __('Recent remote file transfers', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%;' );
	require_once( 'server_info/remote_sends.php' );
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	// This page can take a bit to run.
	// Runs AFTER server information is displayed so we can view the default limits for the server.
	pb_backupbuddy::set_greedy_script_limits();
	
	
	
	pb_backupbuddy::$ui->start_metabox( __( 'File Permissions', 'it-l10n-backupbuddy' ) . ' ' .pb_backupbuddy::video( '7NI7oePvxZg#55', __( 'File Permissions', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%;' );
	require_once( 'server_info/permissions.php' );
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	pb_backupbuddy::$ui->start_metabox( __( 'Site Size Map', 'it-l10n-backupbuddy' ) . ' ' .pb_backupbuddy::video( 'XfZy-7DdbS0#67', __( 'Site Size Map', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%;' );
	require_once( 'server_info/site_size.php' );
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	pb_backupbuddy::$ui->start_metabox( __( 'Database/table Size', 'it-l10n-backupbuddy' ) . ' ' .pb_backupbuddy::video( '7NI7oePvxZg#82', __( 'Database/table Size', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%;' );
	require_once( 'server_info/database.php' );
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	pb_backupbuddy::$ui->start_metabox( __( 'WordPress Scheduled Actions (CRON)', 'it-l10n-backupbuddy' ) . ' ' .pb_backupbuddy::video( '7NI7oePvxZg#94', __( 'WordPress Scheduled Actions (CRON)', 'it-l10n-backupbuddy' ), false ), true, 'width: 100%;' );
	require_once( 'server_info/cron.php' );
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	
	
	
	
	//require_once( pb_backupbuddy::plugin_path() . '/classes/serverinfo.php' );
	pb_backupbuddy::$ui->start_metabox( __( 'URLs & Paths', 'it-l10n-backupbuddy' ), true, 'width: 100%;' );
	//pb_backupbuddy_serverinfo::get_wp_urls();
	
	$wp_upload_dir = wp_upload_dir();
	$wp_settings = array();
	
	if ( isset( $wp_upload_dir['path'] ) ) {
		$wp_settings[] = array( 'Upload File Path', $wp_upload_dir['path'], 'wp_upload_dir()' );
	}
	if ( isset( $wp_upload_dir['url'] ) ) {
		$wp_settings[] = array( 'Upload URL', $wp_upload_dir['url'], 'wp_upload_dir()' );
	}
	if ( isset( $wp_upload_dir['subdir'] ) ) {
		$wp_settings[] = array( 'Upload Subdirectory', $wp_upload_dir['subdir'], 'wp_upload_dir()');
	}
	if ( isset( $wp_upload_dir['baseurl'] ) ) {
		$wp_settings[] = array( 'Upload Base URL', $wp_upload_dir['baseurl'], 'wp_upload_dir()' );
	}
	if ( isset( $wp_upload_dir['basedir'] ) ) {
		$wp_settings[] = array( 'Upload Base Directory', $wp_upload_dir['basedir'], 'wp_upload_dir()' );
	}
	$wp_settings[] = array( 'Site URL', site_url(), 'site_url()' );
	$wp_settings[] = array( 'Home URL', home_url(), 'home_url()' );
	
	// Multisite extras:
	$wp_settings_multisite = array();
	if ( is_multisite() ) {
		$wp_settings[] = array( 'Network Site URL', network_site_url(), 'network_site_url()' );
		$wp_settings[] = array( 'Network Home URL', network_home_url(), 'network_home_url()' );
	}
	// Display WP settings..
	pb_backupbuddy::$ui->list_table(
		$wp_settings,
		array(
			'action'					=>	pb_backupbuddy::page_url(),
			'columns'					=>	array(
												__( 'Setting', 'it-l10n-backupbuddy' ),
												__( 'Value', 'it-l10n-backupbuddy' ),
												__( 'Obtained via', 'it-l10n-backupbuddy' ),
											),
			'css'						=>		'width: 100%;',
		)
	);
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	
	
	echo '<a name="database_replace"></a>';
	pb_backupbuddy::$ui->start_metabox( 'Advanced: ' . __( 'Database Mass Text Replacement', 'it-l10n-backupbuddy' ), true, 'width: 100%;' );
	pb_backupbuddy::load_view( '_server_tools-database_replace' );
	pb_backupbuddy::$ui->end_metabox();
	
	
	
	
	
	
	
	// Handles thickbox auto-resizing. Keep at bottom of page to avoid issues.
	if ( !wp_script_is( 'media-upload' ) ) {
		wp_enqueue_script( 'media-upload' );
		wp_print_scripts( 'media-upload' );
	}
	
} else { // INSIDE IMPORTBUDDY:
	if ( pb_backupbuddy::_GET( 'skip_serverinfo' ) == '' ) { // Give a workaround to skip this.
		require_once( 'server_info/server.php' );
	} else {
		echo '{Skipping Server Info. section based on querystring.}';
	}
}
?>