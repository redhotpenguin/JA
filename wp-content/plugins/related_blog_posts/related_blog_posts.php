<?php

/*
Plugin Name: Related Blog Posts
Plugin URI: http://jadev.redhotpenguin.com/
Description: Widgetized related blog posts
Author: Jeremy
Version: 0.1
*/

include_once(plugin_dir_path(__FILE__) . '../../themes/ja_buddypress/wpalchemy/MetaBox.php');

$related_blog_posts_box = new WPAlchemy_MetaBox(array
(
	'id' => '_related_blog_post',
	'title' => 'Related Blog Posts',
	'template' => plugin_dir_path(__FILE__) . 'related_posts_template.php',
	'priority' => 'high',
	'context' => 'side',
	'autosave' => false
));

function related_blog_posts()
{
	global $related_blog_posts_box;
	$posts = $related_blog_posts_box->get_the_value('posts');
	$post_list = explode(',', $posts);
	foreach ($post_list as $post) {
		$post = trim($post);
		echo '<div class="clearfix" style="margin-bottom: 12px;">';
		echo '<a href="' . get_permalink($post) . '">' . get_the_post_thumbnail($post, array(50,50), array('style' => 'float:left;')) . '</a>';
		echo '<h2 style="padding-left: 60px;"><a href="' . get_permalink($post) . '">' . get_the_title($post) . '</a></h2>';
		echo '</div>';
	}
}

function widget_related_blog_posts($args) 
{
	global $related_blog_posts_box;
	extract ($args);
	if ($related_blog_posts_box->get_the_value('posts')) {
		echo $before_widget;
		echo $before_title . 'Related Blog Posts' . $after_title;
		related_blog_posts();
		echo $after_widget;
	}
}

function widget_related_blog_posts_init()
{
	register_sidebar_widget(__('Related Blog Posts'), 'widget_related_blog_posts');
}

add_action('plugins_loaded', 'widget_related_blog_posts_init');

?>