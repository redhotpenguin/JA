<?php
/*
Plugin Name: Comment Flow
Plugin URI: 
Description: Load Comments Dynamically, Post comments with Ajax
Version: 0.1
Author: Jonas Palmero
Author URI: http://www.twitter.com/welldonejonas
*/

register_activation_hook( __FILE__, 'cflow_activate' );

function cflow_activate(){
	add_option('cflow_initial_threads_loaded', 5 );
	add_option('cfow_load_comment_step',  5);
}

add_action('template_redirect', 'cflow_loads_scripts'); 
function cflow_loads_scripts(){
	if( is_single() ){
		global $post;
		wp_enqueue_script('cflow_front', plugins_url('js/cflow_front.js', __FILE__), array('jquery'));
		wp_enqueue_script('jtextarea_expander', plugins_url('js/jquery.textarea-expander.js', __FILE__), array('jquery'));
		wp_enqueue_style('cflow_style', plugins_url('css/cflow_comment.css', __FILE__) );

		wp_localize_script( 'cflow_front', 'cflow', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'pid' => $post->ID
		));

	}
}

/* COMMENTS LOADING */

add_action('bp_after_blog_comment_list', 'cflow_more_wheel');
function cflow_more_wheel(){
	echo '<span class="cflow_more_wheel" style="display:none;"><img src="'.plugins_url('img/ajax-loader.gif', __FILE__).'"/></span>';
	echo '<span class="cflow_more_msg"   style="display:none;"></span>';
	echo '<span id="bottom_reply_link" style="display:none;" >Post a Comment</span>';
}

add_action('wp_ajax_cflow_more_c', 'cflow_more_callback');
add_action('wp_ajax_nopriv_cflow_more_c', 'cflow_more_callback');

function cflow_more_callback(){
	$p_id =  $_REQUEST['pid'] ;
	$last_comment_id = $_REQUEST['lastComment'];
	$load_all_comments = $_REQUEST['loadAll'];
	
	if( empty( $p_id) || !is_numeric( $p_id ) || $p_id < 1)
		die;
	
	if( empty( $last_comment_id) || !is_numeric( $last_comment_id ) || $last_comment_id < 1)
		die;
	
	if( $load_all_comments == 'true')
		$limit = -1;
	else
		$limit = get_option('cfow_load_comment_step');

	

	$top_comment_ids = cflow_get_post_top_comment_ids($p_id, $limit , $last_comment_id);
	
	$comment_n_replies = get_comments_n_replies	( $top_comment_ids );

	wp_list_comments(
				array( 'callback' => 'bp_dtheme_blog_comments',
				'reverse_top_level' => false,
				'reverse_children' => true, 
				'type' => 'comment',
		), $comment_n_replies);
		
	die();
}


/* AJAX COMMENT POSTING */
//todo: use check_ajax_referer
add_action('comment_duplicate_trigger', 'cflow_handle_dupe');

function cflow_handle_dupe(){ 
	error_log('CFLOW: DUPE');
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		echo 'dup';
		die();
	}
}

add_action('comment_post', 'cflow_post_comment', 100, 2);
function cflow_post_comment( $comment_ID, $comment_status ){
	error_log("CFLOW: POST COMMENT ID: $comment_ID, status: $comment_status");
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		error_log("CFLOW: ajax");
		if ($comment_status == 1 ){
		
			$comment = get_comment($comment_ID);
			$comment_parent = $comment->comment_parent;
			
			$args['max_depth'] = 2;
			
			if($comment_parent > 0)
				$comment_depth = 2;
				
			else
				$comment_depth = 1;
				
			ob_start();
			bp_dtheme_blog_comments($comment, $args, $comment_depth);
			$html_comment = ob_get_contents(); 
			ob_end_clean();
	
			$json = json_encode( array( "cpid" => $comment_parent, "comment" => $html_comment)  );
			header('Content-type: application/json');
			echo $json;
		}
		
		else{
			error_log('CFLOW ERROR status: $comment_status');
			echo 'CFLOW: ERROR';
		}
		error_log('CFLOW END, about to die()');
		die();
	}
}

function cflow_get_post_top_comment_ids($post_id, $limit, $from_cid = null){
	global $wpdb;
	
	$query = "SELECT comment_id FROM `wp_comments` WHERE comment_post_ID = $post_id
		AND comment_parent = 0 ";
		
	if( $from_cid )
		$query.= " AND comment_id < $from_cid ";
	
	 $query.= "AND comment_approved = 1 ORDER BY comment_date DESC";
	
	if( $limit != -1 )
		$query.= " LIMIT $limit";
	
	$comment_ids = $wpdb->get_results( $query, OBJECT  );
	
	return array_map( 'cflow_extract_first_el' , $comment_ids  );
}

function cflow_extract_first_el( $a ){
		return $a->comment_id;
}

function get_comments_n_replies( $arr_comment_ids ){
	global $wpdb;
	
	$top_comment_ids = implode(',', $arr_comment_ids);

	$query = " SELECT * from wp_comments 
	WHERE comment_id IN ( $top_comment_ids )
	OR comment_parent IN ($top_comment_ids )
	AND comment_approved = 1
	ORDER BY comment_date DESC";
	
	return $wpdb->get_results( $query, OBJECT  );
}


?>