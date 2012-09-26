<?php

/*
  Plugin Name: Comment Flow
  Plugin URI:
  Description: Load Comments Dynamically, Post comments with Ajax.  Support up to 3 nested comments.
  Version: 0.2
  Author: Jonas Palmero
  Author URI: http://www.twitter.com/welldonejonas
 */

register_activation_hook(__FILE__, 'cflow_activate');

function cflow_activate() {
    add_option('cflow_initial_threads_loaded', 5);
    add_option('cfow_load_comment_step', 5);
}

add_action('template_redirect', 'cflow_loads_scripts');

function cflow_loads_scripts() {
    if (is_single()) {
        global $post;
        wp_enqueue_script('cflow_front', plugins_url('js/cflow_front.js', __FILE__), array('jquery'), 5);
        wp_enqueue_script('jtextarea_expander', plugins_url('js/jquery.textarea-expander.js', __FILE__), array('jquery'));
        wp_enqueue_style('cflow_style', plugins_url('css/cflow_comment.css', __FILE__));

        wp_localize_script('cflow_front', 'cflow', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'pid' => $post->ID
        ));
    }
}

/* COMMENTS LOADING */

add_action('bp_after_blog_comment_list', 'cflow_more_wheel');

function cflow_more_wheel() {
    echo '<span class="cflow_more_wheel" style="display:none;"><img src="' . plugins_url('img/ajax-loader.gif', __FILE__) . '"/></span>';
    echo '<span class="cflow_more_msg"   style="display:none;"></span>';
//	echo '<span id="bottom_reply_link">Post a Comment</span>';
}

add_action('wp_ajax_cflow_more_c', 'cflow_more_callback');
add_action('wp_ajax_nopriv_cflow_more_c', 'cflow_more_callback');

function cflow_more_callback() {
    $p_id = $_REQUEST['pid'];
    $last_comment_id = $_REQUEST['lastComment'];
    $load_all_comments = $_REQUEST['loadAll'];

    if (empty($p_id) || !is_numeric($p_id) || $p_id < 1)
        die;

    if (empty($last_comment_id) || !is_numeric($last_comment_id) || $last_comment_id < 1)
        die;

    if ($load_all_comments == 'true')
        $limit = -1;
    else
        $limit = get_option('cfow_load_comment_step');


    $top_comment_ids = cflow_get_post_top_comment_ids($p_id, $limit, $last_comment_id);

    $comment_n_replies = get_comments_n_replies($top_comment_ids);

    wp_list_comments(
            array('callback' => 'bp_dtheme_blog_comments',
        'reverse_top_level' => false,
        'reverse_children' => true,
        'type' => 'comment',
        'max_depth' => get_option('thread_comments_depth'),
            ), $comment_n_replies);

    die();
}

/* AJAX COMMENT POSTING */
add_action('comment_duplicate_trigger', 'cflow_handle_dupe');

function cflow_handle_dupe() {
    error_log('CFLOW: DUPE');
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo 'dup';
        die();
    }
}

add_action('comment_post', 'cflow_post_comment', 100);

function cflow_post_comment($comment_ID) {
    error_log("CFLOW BEGIN: POST COMMENT ID: $comment_ID X_REQUESTED: " . $_SERVER['HTTP_X_REQUESTED_WITH']);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        error_log("CFLOW: ajax");

        $comment = get_comment($comment_ID);
        $comment_parent = $comment->comment_parent;
        error_log("CFLOW CPARENT: $comment_parent");

        $args['max_depth'] = get_option('thread_comments_depth');

        // get  comment depth
        $comment_depth = get_comment_depth($comment);

        error_log("CFLOW parameters: $comment->comment_author, args: " . print_r($args, true) . ", depth: $comment_depth");

        ob_start();
        bp_dtheme_blog_comments($comment, $args, $comment_depth);
        $html_comment = ob_get_contents();
        ob_end_clean();

        if (empty($html_comment))
            error_log("CFLOW BUG: EMPTY HTML");

        $json = json_encode(array("cpid" => $comment_parent, "comment" => $html_comment, 'comment_id' => $comment_ID, "depth" => $comment_depth));
        header('Content-type: application/json');
        echo $json;

        error_log('CFLOW END, about to die()');
        die();
    }
    else {
        error_log('CFLOW BUG: Not an AJAX REQUEST');
    }
}

function cflow_get_post_top_comment_ids($post_id, $limit, $from_cid = null) {
    global $wpdb;

    $query = "SELECT comment_id FROM `wp_comments` WHERE comment_post_ID = $post_id
		AND comment_parent = 0 ";

    if ($from_cid)
        $query.= " AND comment_id < $from_cid ";

    $query.= "AND comment_approved = 1 ORDER BY comment_date DESC";

    if ($limit != -1)
        $query.= " LIMIT $limit";

    $comment_ids = $wpdb->get_results($query, OBJECT);

    return array_map('cflow_extract_first_el', $comment_ids);
}

function cflow_extract_first_el($a) {
    return $a->comment_id;
}

function get_comments_n_replies($arr_comment_ids) {
    global $wpdb;

    if (empty($arr_comment_ids))
        return false;

    $top_comment_ids = implode(',', $arr_comment_ids);

    $query = "SELECT * from {$wpdb->prefix}comments
	WHERE comment_id IN (
		SELECT comment_id from wp_comments 
		WHERE comment_id IN ( $top_comment_ids )
		OR comment_parent IN ($top_comment_ids )
		AND comment_approved = 1
	 )
	OR comment_parent IN (
		SELECT comment_id from wp_comments 
		WHERE comment_id IN ( $top_comment_ids )
		OR comment_parent IN ($top_comment_ids )
		AND comment_approved = 1
	 )
	AND comment_approved = 1
	ORDER BY comment_date DESC";

    return $wpdb->get_results($query, OBJECT);
}

/**
 * Returns the comment depth. Works up to 3 nested comments
 * @param object $comment
 * @return integer comment depth
 */
function get_comment_depth($comment) {
    if ($comment->comment_parent == 0)
        return 1;

    $comment_parent = get_comment($comment->comment_parent);

    if ($comment_parent->comment_parent == 0)
        return 2;

    else
        return 3;
}

