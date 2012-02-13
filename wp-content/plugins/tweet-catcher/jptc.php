<?php
/*
Plugin Name: Tweet Catcher
Plugin URI: 
Description: For post and pages, allow the author to add a sidebar widget that displays saved and real-time Tweets. Tweet Catcher uses the permalink as a search query to grab new Tweets. 
Version: 0.1
Author: Jonas Palmero
Author URI: http://twitter.com/welldonejonas
*/

// PLUGIN ACTIVATION
register_activation_hook( __FILE__ , 'jptc_install' );
function jptc_install(){
	update_option( 'jptc_follow_btn', 'journaccel' );
	update_option( 'jptc_tweet_catching',  '');
	update_option( 'jptc_email_notification',  'on' );
	update_option( 'jptc_email',  get_option('admin_email') );		
}


// REGISTER SIDEBAR WIDGET (see jptc_widget.php)
include('src/jptc_metabox.php');
include_once('src/jptc_widget.php');
include_once('src/jptc_admin.php');
global $jptc_model;
$jptc_model = new Tweet_Manager_Model();
add_action( 'widgets_init', create_function( '', 'return register_widget("Tweet_Manager_Widget");' ) );

add_filter( 'jptc_tweet_s', 'jptc_tweet_s');

// DASHBOARD INTEGRATION (see jptc_admin.php and jpc_metabox.php)
if(is_admin()){
	define('JPTC_ADMIN_DISPLAY_N_TWEETS', 100);
	$plugin_url = WP_PLUGIN_URL.'/'.str_replace( basename( __FILE__), "" , plugin_basename(__FILE__) );
	new Tweet_Manager_Metabox($jptc_model,$plugin_url); // create admin setting page
	new Tweet_Manager_Admin($jptc_model);
}


//  ***************************************************************************************************//
// 												CRON STUFF											   //
//  ***************************************************************************************************//
if( get_option('jptc_tweet_catching') ) { // if tweet catching is enabled
	if (!wp_next_scheduled('jptc_hook')) {
		wp_schedule_event( time()  , 'hourly', 'jptc_hook' );
	}
	add_action( 'jptc_hook', 'jptc_catch_tweets' );
}
else{
	if (wp_next_scheduled('jptc_hook')){
			$timestamp = wp_next_scheduled( 'jptc_hook' );
			wp_unschedule_event($timestamp, 'jptc_hook' );
	}
}
 	

function jptc_catch_tweets(){
	error_log('Scheduled Tweet Search');
	$jptc_model = new Tweet_Manager_Model();
	$tweets_found = 0;
	$email_body = "";
	
	$exluded_users =  get_option('jptc_exluded_users') ;
	$exluded_users = str_replace (' ', '', $exluded_users);
	$exluded_users = explode (',', $exluded_users);

	$post_ids = $jptc_model->get_tc_posts();
		
	if( empty($post_ids) ) 
		return false;
		
	foreach($post_ids as $post_id){
		$permalink = get_permalink($post_id);
		
		$search_query = parse_url( $permalink);
		
		$host = $search_query['host'];

		$host = str_replace('www.', '', $host);

		$search_query = $host.$search_query['path']; 
			
		$new_tweets = $jptc_model->actually_search_twitter($search_query, get_option('jptc_tweet_search_number') );
		if($new_tweets == -1 || $new_tweets == false){ // no tweets found for this post, skip
			continue; 
		}
		
		$tmp_found = 0;
		foreach($new_tweets as $tweet){	
			if( array_search($tweet->get_tweet_username(), $exluded_users) !==false ) { 
				continue; // username present in the excluded list, skip
			}
			
			if( $jptc_model->is_tweet_saved( $tweet->get_tweet_id(), $post_id ) ){
				continue;  // tweet already saved, skip
			}
			
			else { // New tweet is found, Happily save it forever :D
			    $jptc_model->save_tweet($tweet, $post_id);
				$tweets_found_number++;
				$tmp_found++;
				$email_body .= $tweet;
				$email_body .= '<br/>';
			}
		}
		
		if($tmp_found >= 1){
			$email_body .= "<b> Added to post: <a href='$permalink' target='_blank' >$permalink</a></b>";
			$email_body .= '<br><a href="'.get_edit_post_link($post_id).'" target="_blank">Manage Tweets for this post</a><br><hr/><br>';
		}
			
	}
	
	if( $tweets_found_number >= 1 && get_option('jptc_email_notification') ){
		$email_headers = array("From: Journalism Accelerator <$admin_email>", "Content-Type: text/html");
		$email_to = get_option('jptc_email');
		
		$tweet_s = apply_filters('jptc_tweet_s' ,$tweets_found_number);
		
		if( empty($email_to) ) 
			$email_to = get_option('admin_email');
		
		$email_body .= '<a href="'.admin_url('options-general.php?page=tweet-catcher').'"> Go to Tweet Catcher </a>';
		wp_mail($email_to, "Tweet Catcher: $tweets_found_number new $tweet_s Added", $email_body, $email_headers);
	}
	
	return $tweets_found_number;
}


function jptc_tweet_s($n_tweets){
	if($n_tweets == 1){
		return 'Tweet';
	}
	elseif($n_tweets > 1)
		return 'Tweets';
		
	else
		return false;
}

?>