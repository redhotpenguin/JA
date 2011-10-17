<?php

/*
Plugin Name: Better Recent Posts (Blog)
Plugin URI: http://ja.redhotpenguin.com/
Description: Widetized Recent Comments for  Journalism Accelerator
Author: Jeremy
Version: 1
*/

function widget_recent_questions_blog() {
		$recent_blog_posts = new WP_Query();
		$recent_blog_posts->query('cat=39');

		while ( $recent_blog_posts->have_posts() ) : $recent_blog_posts->the_post(); ?>

				<?php $thisID = get_the_ID(); ?>

				<p class="category">View all posts in <?php the_category(', '); ?></p>

				<p class="title"><a href="<?php the_permalink(); ?>"><?php if (get_post_meta($thisID, 'short_question_title')) { echo get_post_meta($thisID, 'short_question_title', 1); } else { the_title(); } ?></a></p>
				
			<?php
		endwhile;

	blog_post_comments();
} 

function widget_better_recent_questions_blog($args) {
  extract($args);
  echo $before_widget;
  echo $before_title;?>Recent Posts<?php echo $after_title;

  widget_recent_questions_blog();

  echo $after_widget;
}

function better_recent_questions_blog_init()
{
  register_sidebar_widget(__('Better Recent Posts (Blog)'), 'widget_better_recent_questions_blog');
}

add_action("plugins_loaded", "better_recent_questions_blog_init");

function blog_post_comments() {

	global $wpdb;

$request = "SELECT distinct wp_comments.* FROM wp_comments, wp_posts, wp_gdsr_data_comment WHERE wp_comments.comment_ID = wp_gdsr_data_comment.comment_ID AND comment_approved =  '1' AND wp_comments.comment_post_ID IN ( SELECT DISTINCT ID FROM wp_posts AS p INNER JOIN wp_term_relationships AS tr ON ( p.ID = tr.object_id AND tr.term_taxonomy_id IN ( 39 ) )  INNER JOIN wp_term_taxonomy AS tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id AND taxonomy =  'category' ) ) ORDER BY wp_gdsr_data_comment.user_votes DESC LIMIT 4";

	$comments = $wpdb->get_results($request);
	if ($comments) {
?>
				<p class="category">Blog Comments</p>
<?php
		foreach ($comments as $comment) { ?>
	
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

<!--				<div class="rating"><?php wp_gdsr_render_comment($comment, $read_only=true ); ?></div>-->
				<div class="clear"></div>
			<?php
		}
	}
}


?>
