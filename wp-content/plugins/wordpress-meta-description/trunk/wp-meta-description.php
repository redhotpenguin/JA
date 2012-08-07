<?php
/*
Plugin Name: WordPress Meta Description
Plugin URI: http://www.destio.de/tools/wp-meta-description/
Description: Activates the excerpt for pages for global support as <code>meta description</code>.
Author: Designstudio, Philipp Speck
Version: 1.4
Author URI: http://www.destio.de/
*/

if ( !class_exists ('wp_meta_desc_plugin')) {
	class wp_meta_desc_plugin {

	function page_excerpt_init() {
		add_post_type_support( 'page', 'excerpt' );
	}
	
	function cutstr($string, $i) {
   		if (strlen($string) > $i) {
      		$string = substr($string, 0, $i);
      		$string .= "...";
   	}
	return $string;
	}	
	
	function add_meta_desc_tag() {
		global $post;
		$text = strip_tags($post->post_excerpt);
		$desc = wp_meta_desc_plugin::cutstr($text,160);
		if ( is_single() || is_page() && !empty($desc) ) {
		echo '<meta name="description" content="'.$desc.'" />'."\n";
		}
	}	
	
	} // class wp_meta_desc_plugin
}

add_action('init', array('wp_meta_desc_plugin','page_excerpt_init'));
add_action('wp_head', array('wp_meta_desc_plugin','add_meta_desc_tag'));
?>