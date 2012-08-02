<?php

/*
Plugin Name: JA Recent Resources
Plugin URI: http://ja.redhotpenguin.com/
Description: Widgetized Recent Resources (only 2) for Journalism Accelerator
Author: Bert Mahoney
Version: 1.0
*/


//.............................................................................
//	Function for Recent Resources
//.............................................................................


function recent_two_resources() {
		global $post;
		query_posts('cat=25,-324,-445&posts_per_page=2');
		while (have_posts()) : the_post(); ?>
		
		<div class="srp-widget-singlepost">
		<div class="srp-content-box">
		<h4 class="srp-widget-title"><a class="srp-widget-title-link" href="<?php the_permalink(); ?>" data-bitly-type="bitly_hover_card"><?php the_title(); ?></a></h4><?php the_excerpt(); ?></div>
		</div>
		
		<?php endwhile;
}

function widget_recent_two_resources($args) {
	global $post;
	extract($args);
	echo $before_widget;
	echo $before_title;?>Recent Resources<span class="read-more"><a href="/topics/resources/">More</a><?php echo $after_title;
	echo '<div class="srp-widget-container">';
	recent_two_resources();
	echo '</div>';
	echo $after_widget;
}

function widget_recent_two_resources_init()
{
  register_sidebar_widget(__('Recent Two Resources'), 'widget_recent_two_resources');
}

add_action("plugins_loaded", "widget_recent_two_resources_init");

?>