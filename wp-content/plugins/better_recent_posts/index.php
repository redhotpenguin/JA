<?php

/*
Plugin Name: Better Recent Questions
Plugin URI: http://ja.redhotpenguin.com/
Description: Widetized Recent Comments for  Journalism Accelerator
Author: Jeremy
Version: 1
*/

function widget_recent_questions() {
		rewind_posts();
		query_posts($query_string . '&showposts=3&cat=-25,-27,-39,-40');
		if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php $thisID = get_the_ID(); ?>
			
				<p class="category"><?php the_category(', '); ?></p>
				<p class="title"><a href="<?php the_permalink(); ?>"><?php if (get_post_meta($thisID, 'short_question_title')) { echo get_post_meta($thisID, 'short_question_title', 1); } else { the_title(); } ?></a></p>
				
			<?php
		endwhile;
		endif;
} 

function widget_better_recent_questions($args) {
if (!is_category(array(25, 35, 34, 33, 26, 36, 32, 31, 30))) {
  extract($args);
  echo $before_widget;
  echo $before_title;?>Recent Questions <span class="read-more"><a href="/questions/">More</a></span><?php echo $after_title;
  widget_recent_questions();
  echo $after_widget;
}
}

function better_recent_questions_init()
{
  register_sidebar_widget(__('Better Recent Questions'), 'widget_better_recent_questions');
}

add_action("plugins_loaded", "better_recent_questions_init");

?>