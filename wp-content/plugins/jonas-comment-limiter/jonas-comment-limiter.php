<?php
/*
Plugin Name: JP Comment Limiter
Plugin URI: 
Description: Defines a maximum length for comment with a character counter
Version: 1.0
Author: Jonas Palmero
Author URI: 
License: GPLv2
*/

/*  Copyright 2011  Jonas Palmero  (email : jonas.palmero@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook( __FILE__, 'jp_comment_limiter_install' );
global $post;
function jp_comment_limiter_install() { // Executed when plugin is activated
    if ( version_compare( get_bloginfo( 'version' ), '3.1', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
    }

	$jp_comment_limiter_options = array(
		'max_character' => 1200,
	);
	update_option( 'jp_comment_limiter_options', $jp_comment_limiter_options );
}




add_action( 'wp_head', 'jp_add_script',100 );
function jp_add_script(){
	$plugin_url  = plugin_dir_url(__FILE__); 
	global $post;
	if($post->comment_status === "open"  && is_single() && is_user_logged_in()) {
			$options = get_option( 'jp_comment_limiter_options' );	
			$msg = 'Have more to say?  Post another comment, please!';
			echo "<script type='text/javascript' src='http://www.journalismaccelerator.com/wp-content/plugins/jonas-comment-limiter/js/script.js?ver=3.1'></script>";
			echo '<script type="text/javascript">  jQuery(document).ready(function($) { jQuery(\'#comment\').limit(\''.$options['max_character'].'\',\'#jp_limit_span\', \'#jp_msg_span\', \''.$msg.'\');  }) </script>';
	}

 }

add_action( 'admin_menu', 'jp_admin_add_page' );
function jp_admin_add_page(){
	add_options_page(
		'JP Comment Limiter',
		'JP Comment Limiter',
		'manage_options',
		'jp_admin_settings',
		'jp_admin_option_page'
	);
}
	
function jp_admin_option_page(){
?>	
		<div class="wrap" >
		<?php screen_icon(); ?>
		<h2>JP Comment Limiter</h2>
		<form action="options.php" method="post" >
			<?php
				settings_fields('jp_comment_limiter_options');
				do_settings_sections('jp_comment_limiter');
			?>
			<input name="Submit" type="submit" value="Save Changes" class="button-primary" />
	
		</form> 
	</div>

<?php	
}// end of jp_admin_option_page
add_action('admin_init', 'jp_comment_limiter_admin_init');
function jp_comment_limiter_admin_init(){
		register_setting(
			'jp_comment_limiter_options',
			'jp_comment_limiter_options',
			'jp_comment_limiter_validate_options'
		);

		add_settings_section(
			'jp_comment_limiter_main',
			'JP Comment Limiter Settings',
			'jp_comment_limiter_section_text',
			'jp_comment_limiter'
		);
		add_settings_field(
			'jp_comment_limiter_text_string',
			'Maximum comment characters:',
			'jp_comment_limiter_setting_input',
			'jp_comment_limiter',
			'jp_comment_limiter_main'
		);
}

function jp_comment_limiter_section_text(){
	echo '<p>Add the following HTML code wherever you want the counter to appears: ';
	echo '  <b>&lt;span id="jp_limit_span"&gt;&lt;/span&gt; </b></p>';
}

function jp_comment_limiter_setting_input(){ //set fields for the form (input text)
	$options = get_option( 'jp_comment_limiter_options' );
	$max_length = $options['max_character'];
	// echo the field
	echo " <input id='max_character' name='jp_comment_limiter_options[max_character]' type='text' value='$max_length' / > ";
}

function jp_comment_limiter_validate_options($input){ // make sure the inputs are numbers only
	$valid = array();
	$valid['max_character'] = preg_replace(
		'/[^\d]/i',
		'',
		$input['max_character']
	);
	
	return $valid;
}

/*
	//Update our plugin link in the panel plugin section
	$location_local = plugin_basename(__FILE__);
	add_filter( array('plugin_action_links', 'jonas-comment-limiter'), array('plugin_settings_link'),10, 2);
	function plugin_settings_link($links, $file) {
		$settings_link = '<a href="themes.php?page=pull-quotes">Settings</a>';
		$links[] = $settings_link; // ... or after other links


	
		return $links;
	} // end settings link
*/

?>