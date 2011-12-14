<?php
/*
Plugin Name: Ping HighRise
Plugin URI: 
Description: Update Highrise when user registers or post a comment.
Version: 0.1
Author: Jonas Palmero
Author URI: http://www.twitter.com/welldonejonas
*/
include_once('src/ping_highrise_core.php');
include_once('src/ping_highrise_business.php');

global $ph_verbose;
$ph_verbose = true;


/**** PLUGIN INITIALIZATION ****/

register_activation_hook( __FILE__ , 'ph_install' );
add_action('init', 'init_ph');

function ph_install(){ // executed when plugin is activated
	$post_highrise_url = get_site_url().'/wp-content/plugins/ping-highrise/post_highrise.php';
	update_option( 'post_highrise_url', $post_highrise_url);
	update_option( 'highrise_new_user_hook', 'rpx_user_register' );
	update_option( 'highrise_new_comment_hook', 'comment_post' );
}

function init_ph(){ // executed after WP has finished loading (before headers are sent)
	if(is_admin()){ 
		include_once('src/ping_highrise_setup.php');
		new Ping_Highrise_Setup();
	}
	
	$hr_core = new Ping_Highrise_Core();
	$hr_business = new ping_highrise_business($hr_core);

	if ( is_user_logged_in() ){
		$new_comment_hook = get_option('highrise_new_comment_hook');
		add_action($new_comment_hook, array(&$hr_business, 'new_comment_hook') );
	}
	else{
		$new_user_hook= get_option('highrise_new_user_hook');
		add_action($new_user_hook, array(&$hr_business, 'new_user_hook'));
	}
	 
}

add_action('bp_profile_header_meta', 'the_link_to_hr');
function the_link_to_hr(){
	if(current_user_can('administrator')){
		global $bp;
		$hr_url = get_option('highrise_url');
		$displayed_user_id =  $bp->displayed_user->id;
		$hr_user_id = get_user_meta($displayed_user_id, 'hr_user_id', true);
		if(empty($hr_user_id)) return false;
		$hr_link =  $hr_url.'/people/'.$hr_user_id;
		
		echo "<p class='website'><a href='$hr_link' class='website'>Highrise Profile</a></p>";
		
	}
}

function ph_log($message){
	global $ph_verbose;
	if($ph_verbose){
		error_log("Ping Highrise: $message");
	}
}

/*
add_action('wp_footer', 'phdebug');
function phdebug(){

if(empty($_GET['debug']))
	return false;

	 $hr_core = new Ping_Highrise_Core();
	 $new_user_hook= get_option('highrise_new_user_hook');
	 $hr_business = new ping_highrise_business($hr_core, $business_params);
	 // $hr_business->new_user_hook(485);
	    $hr_business->new_comment_hook(551);
}
*/


?>