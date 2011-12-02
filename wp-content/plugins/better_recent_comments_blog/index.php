<?php

/*
Plugin Name: Better Recent Comments (Blog)
Plugin URI: http://www.journalismaccelerator.com/
Description: Widetized Recent Comments for  Journalism Accelerator
Author: Jeremy and Fred
Version: 1
*/

function widget_recent_comments_blog($no_comments = 10, $comment_len = 100) {
	global $wpdb;
	$request = "SELECT * FROM $wpdb->comments";
	$request .= " JOIN $wpdb->posts ON ID = comment_post_ID";
	$request .= " INNER JOIN $wpdb->term_relationships AS tr ON (p.ID = tr.object_id AND tr.term_taxonomy_id IN (39) ) INNER JOIN $wpdb->term_taxonomy AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy = 'category')";
	$request .= " WHERE comment_approved = '1' AND post_status = 'publish' AND post_password =''";
	$request .= " ORDER BY comment_date DESC LIMIT 3";
	$comments = $wpdb->get_results($request);
	if ($comments) {
		foreach ($comments as $mycomment) {
			ob_start();
			$myid = $mycomment->user_id;
			echo get_avatar($myid, 50);
			?><p class="answer"><a href="<?php comment_author_url($mycomment->comment_ID); ?>">
				<?php 
				echo comment_author($mycomment->comment_ID); ?></a><?php
				echo ' on ' ;
				$category = get_the_category($mycomment->comment_post_ID);
				?>
				<a href="<?php echo get_permalink($mycomment->comment_post_ID); ?>#comment-<?php echo $mycomment->comment_ID; ?>"><?php echo $category[0]->cat_name; ?></a>: <?php
				if (strlen($mycomment->comment_content) > 100) {
					echo strip_tags(substr(apply_filters('get_comment_text', $mycomment->comment_content), 0, 99)) . "...";
				}
				else {
					echo strip_tags($mycomment->comment_content);
				}
				?>
				</p>
				<div class="clear"></div>
			<?php
			ob_end_flush();
		}
	} else {
		echo '<p>Comments not found.</p>';
	}
}

function widget_dp_recent_comments_blog($args) {
  extract($args);
  echo $before_widget;
  echo $before_title;?>Recent Comments<?php echo $after_title;
  widget_recent_comments();
  echo $after_widget;
}

function dp_recent_comments_blog_init()
{
  register_sidebar_widget(__('Better Recent Comments (Blog)'), 'widget_dp_recent_comments_blog');
}

add_action("plugins_loaded", "dp_recent_comments_blog_init");

?>
