<?php

/*
Plugin Name: JA Related Questions
Plugin URI: http://ja.redhotpenguin.com/
Description: Widetized Related Questions for  Journalism Accelerator
Author: Jeremy and Fred
Version: 1.3
*/


	function split_to_list_q($content) {
		$lines = explode("|", $content);
		foreach ($lines as $val) {
			
			$parts = explode("\n", trim($val));
			$title = trim($parts[0]);
			$url = trim($parts[1]);
	
			echo "<h2><a href=\"$url\">$title</a></h2>";
	
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

function related_questions() {	
		global $post;
		if ( get_post_meta($post->ID, 'related_questions') ) {
			split_to_list_q(get_post_meta($post->ID, 'related_questions', 1));
	} 
}

function widget_related_questions($args) {
	global $post;
	if (is_single() && get_post_meta($post->ID, 'related_questions')) {
	  extract($args);
	  echo $before_widget;
		echo $before_title;?>Related Questions<?php echo $after_title;
	  related_questions();
	  echo $after_widget;
	  }
}

function widget_related_questions_init()
{
  register_sidebar_widget(__('Related Questions'), 'widget_related_questions');
}

add_action("plugins_loaded", "widget_related_questions_init");

?>
