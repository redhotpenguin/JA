<?php
/*
Plugin Name: Conversations
Plugin URI: 
Description: Add a Conversation widget in the sidebar
Version: 0.2
Author: Jonas Palmero
Author URI: http://www.twitter.com/welldonejonas
*/
include_once('src/widget.php');

register_activation_hook( __FILE__ , 'jpconv_install' );
function jpconv_install(){
	update_option( 'jpconv_display_comment_number', 4 );
}


add_action('init', 'jpconv_init');
function jpconv_init(){
       wp_register_style( 'jpconv', plugins_url('style.css', __FILE__) );
	   wp_enqueue_style( 'jpconv' );
}


add_action( 'widgets_init', create_function( '', 'register_widget("JPCONV_Widget");' ) );



?>