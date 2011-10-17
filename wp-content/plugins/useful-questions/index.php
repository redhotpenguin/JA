<?php

/*
Plugin Name: Useful Questions
Plugin URI: http://ja.redhotpenguin.com/
Description: Widgetized Useful Questions
Author: Jeremy
Version: 1
*/

function widget_useful_questions() {
		rewind_posts();
		query_posts($query_string . '&showposts=3&gdsr_sort=rating&gdsr_order=desc');
		if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<?php $thisID = get_the_ID(); ?>
			<div class="sidebar-question">
				<p class="category"><?php the_category(', '); ?></p>
				<p class="title"><a href="<?php the_permalink(); ?>"><?php if (get_post_meta($thisID, 'short_question_title')) { echo get_post_meta($thisID, 'short_question_title', 1); } else { the_title(); } ?></a></p>


				<div class="rating"><?php wp_gdsr_render_article(null, $read_only=true); ?></div>
				<div class="clear"></div>
			</div><?php
		endwhile;
		endif;
} 

function widget_show_useful_questions($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>Useful Questions <span class="read-more"><a href="/questions/">More</a></span><?php echo $after_title;
	widget_useful_questions();
	echo $after_widget;
}

function widget_show_useful_questions_init() {
	register_sidebar_widget(__('Useful Questions'), 'widget_show_useful_questions');
}

add_action('plugins_loaded', 'widget_show_useful_questions_init');

?>