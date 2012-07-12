<?php

/*
Plugin Name: JA Related Resources
Plugin URI: http://ja.redhotpenguin.com/
Description: Widetized Related Resources for  Journalism Accelerator
Author: Jeremy and Fred
Version: 2
*/

/* PLUGIN FUNCTIONS */

function split_to_list($content) {
	$lines = explode("|", $content);
	foreach ($lines as $val) {
		
		$parts = explode("\n", trim($val));
		$title = trim($parts[0]);
		$url = trim($parts[1]);

		echo "<h2><a href=\"$url\" target=\"_blank\">$title</a></h2>";

		if (sizeof($parts) > 2) {
			$description = trim($parts[2]);
			if (strlen($description) > 200) {
				$shortdesc = substr($description, 0, 199);
				echo "<p>$shortdesc...</p>";
			} else {
			echo "<p>$description</p>";
			}
		}
	}
}

function split_and_shuffle($content) {
	$i = 0;
	$resources = array();
	$lines = explode("|", $content);
	foreach ($lines as $val) {
		
		$parts = explode("\n", trim($val));
		$title = trim($parts[0]);
		$url = trim($parts[1]);

		

		if (sizeof($parts) > 2) {
			$description = trim($parts[2]);
			if (strlen($description) > 200) {
				$shortdesc = substr($description, 0, 199);
				$resources[$i] = array( 'title' => $title, 'url' => $url, 'description' => $shortdesc );
			} else {
			$resources[$i] = array( 'title' => $title, 'url' => $url, 'description' => $description );
			}
		}
		$i++;
	}
	$length = count($resources) - 1;
	$picks = array();
	$j = 0;
	while (count($picks) < 2) {
		$x = mt_rand(0, $length);
		if ( !in_array($x, $picks) ) { 
			$picks[$j] = $x;
			$j++;
		}
	}
	foreach ($picks as $k) {
		echo "<h2><a href=\"" . $resources[$k]['url'] . "\" target=\"_blank\">" . $resources[$k]['title'] . "</a></h2><p>" . $resources[$k]['description'] . "</p>";
	}
}

function related_resources() {
	if (is_single()) {		
		global $post;
		if ( get_post_meta($post->ID, 'related_resources') ) {
			split_to_list(get_post_meta($post->ID, 'related_resources', 1));
		}
		else {
			featured_resources();
		}
	} 
	elseif (is_home() || is_archive() || is_search()) {
		featured_resources();
	}
	else { featured_resources(); }
}

function default_related_resources() {
	$default_resources = new WP_Query();
	$default_resources->query('&cat=25, 35, 34, 33, 26, 36, 32, 31, 30&showposts=3');
?>
		<?php while ( $default_resources->have_posts() ) : $default_resources->the_post(); ?>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php the_excerpt(); ?>
		<?php endwhile; ?>
<?php
}

function featured_resources() {
	if( get_option('resources_home') ) {
		split_and_shuffle(get_option('resources_home'));
	}
}

/* ADMIN STUFF */

function widget_related_resources($args) 
{
		global $post;		
		extract($args);
		echo $before_widget;
		if (is_single() && get_post_meta($post->ID, 'related_resources')) {
			echo $before_title;?>Related Resources <span class="read-more"><a href="/topics/resources/">More</a><?php echo $after_title;
		}
		else {
			echo $before_title;?>Featured <span class="read-more"><a href="/topics/resources/digest-resources/">More</a><?php echo $after_title;
		}
		related_resources();
		echo $after_widget;
}

function widget_related_resources_init()
{
  register_sidebar_widget(__('Related Resources'), 'widget_related_resources');
}

function related_resources_admin_actions() {
	add_options_page('Featured Resources Settings', 'Featured Resources', 'manage_options', 'featured-resources-settings', 'featured_resources_options');
}

function featured_resources_options() {
	include('related_resources_admin.php');
}

/* ACTIONS */

add_action("plugins_loaded", "widget_related_resources_init");
add_action('admin_menu', 'related_resources_admin_actions');

?>
