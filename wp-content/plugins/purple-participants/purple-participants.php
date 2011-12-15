<?php
/*
Plugin Name: Purple Participants
Plugin URI: 
Description: Splash screen of Comments and Sidebar widget
Version: 0.1
Author: Jonas Palmero
Author URI: http://www.twitter.com/welldonejonas
*/

include_once('src/helpers.php');

/************************************************************************/
/* 								/participants 							*/
/************************************************************************/
add_filter( 'query_vars','purple_p_thumbs_query_var' );
function purple_p_thumbs_query_var( $vars ){
    array_push($vars, 'participants');
    return $vars;
}

add_filter( 'generate_rewrite_rules', 'purple_p_rewrite' );
function purple_p_rewrite( $wp_rewrite ) {
    $new_rules = array(
        '(.+)/(participants)' => 'index.php?participants=$matches[1]'
    );
	
    return $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}


add_action( 'wp_loaded','purple_p_flush_rules' );
function purple_p_flush_rules(){
	$rules = get_option( 'rewrite_rules' );
	if ( ! isset( $rules['(.+)/(participants)'] ) ) {
		global $wp_rewrite;
	   	$wp_rewrite->flush_rules();
	}
}


add_action("template_redirect", 'purple_p_template_redirect');

// Template selection
function purple_p_template_redirect() {
    global $wp_query;
	global $participant_ids;
	
	$post_id =  url_to_postid( $wp_query->query_vars['participants'] );
	$thumbs_var = get_query_var('participants');
	$participant_ids = get_post_participants($post_id, '', true);
	
	$participants_n = count($participant_ids);
	
	// could be better, we want to make sure we are only loading the template for posts
	if( !empty($thumbs_var) && $post_id > 0  &&  get_post_type($post_id) == 'post'  && $participants_n >= 3){
		locate_template( array( 'participants.php' ), true ) ;
		exit;
	}	
}

?>