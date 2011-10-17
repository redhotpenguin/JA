<?php
/*
Plugin Name: Meta Keywords & Description
Plugin URI: http://dev.svil4ok.info/wordpress/wp-simple-meta-keywords-description/
Description: Get tags on every single post (is_single()) and put them in meta keywords tag in header (wp_head()). Otherwise it'll use the keywords define in $keywords. Get the_excerpt() and put it in meta description tag in header (wp_head()). Otherwise it'll use the description define in $description.
Version: 0.8
Author: Svilen Popov
Author URI: http://www.svil4ok.com/
*/

// Meta Keywords Usage
register_activation_hook(__FILE__,'meta_lang_install'); 
register_deactivation_hook( __FILE__, 'meta_lang_remove' );
function meta_lang_install() {
	add_option("meta-lang", 'en', '', 'yes');
}
function meta_lang_remove() {
	delete_option('meta-lang');
}

// Meta Keywords
register_activation_hook(__FILE__,'meta_keywords_install'); 
register_deactivation_hook( __FILE__, 'meta_keywords_remove' );
function meta_keywords_install() {
	add_option("meta-keywords", '', '', 'yes');
}
function meta_keywords_remove() {
	delete_option('meta-keywords');
}

// Meta Keywords Usage
register_activation_hook(__FILE__,'meta_keywords_usage_install'); 
register_deactivation_hook( __FILE__, 'meta_keywords_usage_remove' );
function meta_keywords_usage_install() {
	add_option("meta-keywords-usage", '0', '', 'yes');
}
function meta_keywords_usage_remove() {
	delete_option('meta-keywords-usage');
}

// Meta Description
register_activation_hook(__FILE__,'meta_description_install'); 
register_deactivation_hook( __FILE__, 'meta_description_remove' );
function meta_description_install() {
	add_option("meta-description", '', '', 'yes');
}
function meta_description_remove() {
	delete_option('meta-description');
}


$keywords = get_option('meta-keywords');
$description = get_option('meta-description');;


function cut($str, $len = 155) {
	if(function_exists('mb_strlen')) {
		return mb_strlen($str,'UTF-8')<$len ? $str : (mb_substr($str,0,$len-1,'UTF-8').'...');
	}
	if( function_exists('iconv_strlen') ) {
		return iconv_strlen($str,'UTF-8')<$len ? $str : (iconv_substr($str,0,$len-1,'UTF-8').'...');
	}
	return strlen($str)<2*$len ? $str : (substr($str,0,2*$len-2).'...');
}

function clean_tag($tag) {
	$tag = strtolower($tag);
	$tag = trim($tag);
	return $tag;
}

function tags_meta_keywords() {
	global $posts, $keywords;
	$keywords = explode(',', $keywords);
	if (is_single()) {
		$tags = get_the_tags($posts[0]->ID);
		if (!empty($tags)) {
			foreach($tags as $tag) {
				$post_tags[] = clean_tag($tag->name);
			}
			// combine
			if (get_option('meta-keywords-usage') == '2') {
				$array_unique = array_unique($post_tags);
				$keywords = implode(', ', $keywords) . ', ' . implode(', ', $array_unique);
			}
			// default
			else if (get_option('meta-keywords-usage') == '1') {
				$keywords = implode(', ', $keywords);
			}
			// tags
			else {
				$array_unique = array_unique($post_tags);
				$keywords = implode(', ', $array_unique);
			}
		}	
	}
	else {
		$keywords = implode(', ', $keywords);
	}
	echo "\n<meta name=\"keywords\" content=\"" . $keywords . "\" />\n";
}


function excerpt_meta_description() {
	global $post, $description;
	$description = $description;
	$title = "";
	if ((is_single() || is_page()) && have_posts()) {
		while ( have_posts() ) { 
			the_post(); 
			$title = get_the_title($post->ID) . " - ";
			$description = cut(trim(strip_tags(get_the_excerpt($post->parent))));
		}
	} elseif (is_tag() && tag_description()) {
		$description = cut(trim(strip_tags(tag_description())));
	}
	elseif (is_category() && category_description()) {
		$description = cut(trim(strip_tags(category_description())));
	}
	echo "<meta name=\"description\" content=\"".$title . $description . "\" />\n";
}

add_action('wp_head', 'tags_meta_keywords');
add_action('wp_head', 'excerpt_meta_description');

// Admin options
function meta_admin_include() {  
     include('meta_admin.php');  
}  
function meta_admin() {
	add_options_page("Meta Keywords & Description", "Meta Keywords & Description", 1, "meta-keywords-description", "meta_admin_include");
}
add_action('admin_menu', 'meta_admin');
?>
