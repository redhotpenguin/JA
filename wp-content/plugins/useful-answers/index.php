<?php

/*
Plugin Name: Useful Answers
Plugin URI: http://ja.redhotpenguin.com/
Description: Widgetized Useful Answers
Author: Jeremy
Version: 1
*/

function widget_useful_answers() {
	global $wpdb;
	$request = "SELECT wp_comments.* FROM $wpdb->comments";
	$request .= " LEFT JOIN wp_gdsr_data_comment on wp_comments.comment_id =  wp_gdsr_data_comment.comment_id";
	$request .= " WHERE comment_approved = '1'";
	$request .= " ORDER BY wp_gdsr_data_comment.user_votes DESC LIMIT 3";

	$comments = $wpdb->get_results($request);
	if ($comments) {
		foreach ($comments as $comment) { ?>
	
			<div class="sidebar-answer">
				<p class="category"><?php the_category(', ', null, $comment->comment_post_ID); ?></p>
	<p class="title"><a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>">
<?php

				// trim the string
				if (strlen($comment->comment_content) > 100) {
					echo strip_tags(substr(apply_filters('get_comment_text', $comment->comment_content), 0, 99)) . "...";
				}
				else {
					echo strip_tags($comment->comment_content);
				}
	
?>
</a></p>

				<div class="rating"><?php wp_gdsr_render_comment($comment, $read_only=true ); ?></div>
				<div class="clear"></div>
			</div><?php
		}
	}
}

function widget_show_useful_answers($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>Useful Answers <span class="read-more"><a href="/answers/">More</a></span><?php echo $after_title;
	widget_useful_answers();
	echo $after_widget;
}

function widget_show_useful_answers_init() {
	register_sidebar_widget(__('Useful Answers'), 'widget_show_useful_answers');
}

add_action('plugins_loaded', 'widget_show_useful_answers_init');

?>