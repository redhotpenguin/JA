<?php
/*
Plugin Name: Tweet Catcher
Plugin URI: 
Description: Browse Twitter.com, Shuffle saved and live tweets.
Version: 0.1
Author: Jonas Palmero
Author URI: http://twitter.com/welldonejonas

*/


// REGISTER SIDEBAR WIDGET (see jptc_widget.php)

include('jptc_metabox.php');
include_once('jptc_widget.php');
global $jptc_model;
$jptc_model = new Tweet_Manager_Model();

add_action( 'widgets_init', create_function( '', 'return register_widget("Tweet_Manager_Widget");' ) );


if(is_admin()){ 
	$plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	new Tweet_Manager_Metabox($jptc_model,$plugin_url); // create admin setting page
}


?>