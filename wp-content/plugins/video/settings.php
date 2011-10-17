<?php
/**
 * VideoPress settings. Customize player functionality.
 *
 * @package video
 * @category video
 * @author VideoPress
 * @link http://videopress.com/ VideoPress
 * @version 1.1.3
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Add video player settings to the blog's media settings page
 */
function video_player_customization_init() {
	if ( current_user_can('upload_files') ) {
		add_settings_section( 'video_player_customization', __('VideoPress player', 'video'), 'video_player_customization_callback', 'media' );
		add_settings_field( 'video_player_freedom', __('Free formats', 'video'), 'video_player_freedom_callback', 'media', 'video_player_customization' );
		register_setting( 'media', 'video_player_freedom', 'video_player_freedom_sanitize' );
	}
}
add_action( 'admin_init', 'video_player_customization_init' );


/**
 * Fulfill the settings section callback requirement by returning nothing
 */
function video_player_customization_callback() {
	return;
}


/**
 * Display videos in only Freedom-loving formats.
 */
function video_player_freedom_callback() {
	if ( current_user_can('upload_files') ) {
		echo '<fieldset><legend class="screen-reader-text"><span>' . __('Only display video in formats free of patent claims and other restrictions on distribution and playback.') . '</span></legend>';
		echo '<div><input type="checkbox" name="video_player_freedom" id="video_player_freedom" value="1" ';
		if ( get_option( 'video_player_freedom', false ) )
			echo 'checked="checked" ';
		echo '/> <label for="video_player_freedom">' . __('Only display videos in free software formats', 'video') . '</label></div>';
		echo '<div><small><em>' . __('Ogg file container with Theora video and Vorbis audio', 'video') . '</em></small></div>';
	}
}

/**
 * Convert checkbox value from int to bool
 *
 * @return bool true if checked, else false
 */
function video_player_freedom_sanitize( $checked ) {
	if ( $checked==1 )
		return true;
	else
		return false;
}
?>