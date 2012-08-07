<?php
/*
| ----------------------------------------------------
| File        : class-main.php
| Project     : Special Recent Posts plugin for Wordpress
| Version     : 1.9
| Description : This file contains the main plugin class
|               which handles all the important visualization processes.
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress/
| ----------------------------------------------------
*/

class SpecialRecentPosts {

/*
| ---------------------------------------------
| CLASS PROPERTIES
| ---------------------------------------------
*/

	// Declaring default plugin options array.
	private $plugin_args;
	
	// Declaring widget instance options array.
	private $widget_args;
	
	// Declaring Single Post ID (the current one displayed when in single post view).
	private $singleID;

/*
| ---------------------------------------------
| CLASS CONSTRUCTOR & DECONSTRUCTOR
| ---------------------------------------------
*/
	// Class Constructor.
	// In this section we define the plugin global admin values and assign the selected widget values.
	public function __construct($args = array()) {

		// Setting up plugin options to be available throughout the plugin.
		$this->plugin_args  = get_option('srp_plugin_options');
		
		// Including external widget values.
		global $srp_default_widget_values;
		
		// Double check if $args is an array.
		if (!is_array($args)) {
			$args = array();
		}
		
		// Setting up widget options to be available throughout the plugin.
		$this->widget_args = array_merge($srp_default_widget_values, $args);
		
		// Updating thumbnails sizes if these are not set on custom PHP call. Assign default value.
		if (!isset($this->widget_args["srp_thumbnail_wdg_width"])) {
			$this->widget_args["srp_thumbnail_wdg_width"] = $this->plugin_args["srp_thumbnail_width"];
		}
		
		if (!isset($this->widget_args["srp_thumbnail_wdg_height"])) {
			$this->widget_args["srp_thumbnail_wdg_height"] = $this->plugin_args["srp_thumbnail_height"];
		}
		
		if (is_single()) {
			global $post;
			$this->singleID = $post->ID;
		}
	}
	
	// Class Deconstructor.
	public function __deconstruct() {}

/*
| ---------------------------------------------
| STATIC METHODS
| ---------------------------------------------
*/

	// This method handles all the actions for the plugin to be initialized.
	static function install_plugin() {
		
		// Loading text domain for translations.
		load_plugin_textdomain(SRP_TRANSLATION_ID, false, dirname(plugin_basename(__FILE__)) . SRP_LANG_FOLDER);
		
		// Importing global default options array.
		global $srp_default_plugin_values;
		
		// Creating WP Option with default values.
		add_option('srp_plugin_options', $srp_default_plugin_values, '', 'no');
	}
	
	// This method handles all the actions for the plugin to be uninstalled.
	static function uninstall_plugin() {
		
		// Deleting main WP Option.
		delete_option('srp_plugin_options');
	}

/*
| ---------------------------------------------
| CLASS MAIN METHODS
| ---------------------------------------------
*/
	
	// Method to retrieve first post image url.
	private function getFirstImageUrl($thumb_width, $thumb_height, $post_title) {
	
		// Including global WP Enviroment.
		global $post, $posts;
		
		//Getting images attached to the post.
		$args = array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => -1,
			'order'          => 'ASC',
			'post_status'    => null,
			'post_parent'    => $post->ID
		);
		
		$attachments = get_posts($args);
		
		// Check for attachments.
		if ($attachments) {
			
			// Cycling through attachments.
			foreach ($attachments as $attachment) {
			
				// Retrieving image url.
				$imgabs = base64_encode(get_attached_file($attachment->ID));
				break;
			}
			
			//Return image tag using adaptive resize with PHPThumb.
			return "<img alt=\"" . $post_title . "\" class=\"srp-widget-thmb\" src=\"" . SRP_PLUGIN_URL . SRP_THUMBPHP_HANDLER . "?width=" . $thumb_width . "&height=" . $thumb_height . "&rotation=" . $this->widget_args["srp_thumbnail_rotation"] . "&file=" . $imgabs . "\" />";
			
		} else {
			// there are no attachment for the current post. Switch to default thumbnail image.
			return $this->displayDefaultThumb($this->widget_args["srp_thumbnail_wdg_width"], $this->widget_args["srp_thumbnail_wdg_height"]);
		}
	}
	
	// Method to display default thumbnail.
	private function displayDefaultThumb($thumb_width, $thumb_height) {
	
		// Check if a custom thumbnail url has been provided.
		if ($this->plugin_args['srp_thumbnail_url'] != '') {
			
			// Returning custom default thumbnail image.
			return "<img alt=\"" . __('No thumbnail available') . "\" class=\"srp-widget-thmb\" src=\"" . $this->plugin_args['srp_thumbnail_url'] . "\" width=\"" . $thumb_width . "\" height=\"" . $thumb_height . "\" />";
			
		} else {
			// Returning default thumbnail image.
			return "<img alt=\"" . __('No thumbnail available') . "\" class=\"srp-widget-thmb\" src=\"" . SRP_PLUGIN_URL . SRP_DEFAULT_THUMB . "\" width=\"" . $thumb_width . "\" height=\"" . $thumb_height . "\" />";
		}
	}
	
	// Main method to extract and elaborate post excerpt based on user choices.
	private function extractExcerpt($post) {
		
		// Loading default plugin values.
		$excerpt_length        = $this->plugin_args['srp_excerpt_length'];
		$excerpt_length_mode   = $this->plugin_args['srp_excerpt_length_mode'];
		
		// Check if widget values are overriding default ones.
		if ($excerpt_length != $this->widget_args['srp_wdg_excerpt_length'])
			$excerpt_length = $this->widget_args['srp_wdg_excerpt_length'];
			
		if ($excerpt_length_mode != $this->widget_args['srp_wdg_excerpt_length_mode'])
			$excerpt_length_mode = $this->widget_args['srp_wdg_excerpt_length_mode'];
			
		
		// Check for "cut mode".
		switch($excerpt_length_mode) {
		
			case 'words':
				
				// Check if excerpt is available.
				if ($post->post_excerpt) {
				
					// Return normal excerpt using 'words cut'.
					return $this->substrWords($this->srp_sanitize($post->post_excerpt), $excerpt_length);
					
				} else {
				
					// Normal excerpt not available. Retrieve text from post content using 'words cut'.
					return $this->substrWords($this->srp_sanitize($post->post_content), $excerpt_length);
				}
				
			break;
			
			case 'chars':
				
				// Check if excerpt is available.
				if ($post->post_excerpt) {
				
					// Return normal excerpt using 'characters cut'.
					return substr($this->srp_sanitize($post->post_excerpt), 0, $excerpt_length);
					
				} else {
				
					// Normal excerpt not available. Retrieve text from post content using 'characters cut'.
					return substr($this->srp_sanitize($post->post_content), 0, $excerpt_length);
				}
				
			break;
			
			case 'fullexcerpt':
				
				// Check if excerpt is available.
				if ($post->post_excerpt) {
				
					// Return normal excerpt using 'characters cut'.
					return $this->srp_sanitize($post->post_excerpt);
					
				} else {
				
					// Normal excerpt not available. Retrieve text from post content using 'characters cut'.
					return substr($this->srp_sanitize($post->post_content), 0, $excerpt_length);
				}
				
			break;
		}
	}
	
	// Main method to extract and elaborate post title based on user choices.
	private function extractTitle($post) {
		
		// Loading default plugin values.
		$title_length        = $this->plugin_args['srp_title_length'];
		$title_length_mode   = $this->plugin_args['srp_title_length_mode'];
		
		// Check if widget values are overriding default ones.
		if ($title_length != $this->widget_args['srp_wdg_title_length'])
			$title_length = $this->widget_args['srp_wdg_title_length'];
			
		if ($title_length_mode != $this->widget_args['srp_wdg_title_length_mode'])
			$title_length_mode = $this->widget_args['srp_wdg_title_length_mode'];
		
		// Check for "cut mode".
		switch($title_length_mode) {
		
			case 'words':
			
				// Return normal title using 'words cut'.
				return $this->substrWords($this->srp_sanitize($post->post_title), $title_length);
			break;
			
			case 'chars':
			
				// Return normal title using 'characters cut'.
				return substr($this->srp_sanitize($post->post_title), 0, $title_length);
			break;
			
			case 'fulltitle':
			
				// Return normal title using 'characters cut'.
				return $this->srp_sanitize($post->post_title);
			break;
		}
	}

	// Main method to retrieve posts.
	private function getPosts() {
		
		// Defining args array.
		$args = array (
			'post_type'   => $this->widget_args["srp_post_type"],
			'numberposts' => $this->widget_args["srp_number_post_option"],
			'order'       => $this->widget_args["srp_order_post_option"],
			'post_status' => $this->widget_args["srp_post_status_option"]
		);
		
		// Check for custom pot types option.
		if ($this->widget_args["srp_custom_post_type_option"] != '') {
			
			// Filter result posts by category ID.
			$args["post_type"] = $this->widget_args["srp_custom_post_type_option"];
		}
		
		// Check if category filter is applied.
		if ($this->widget_args["srp_filter_cat_option"] != '-1') {
			
			// Filter result posts by category ID.
			$args["category"] = $this->widget_args["srp_filter_cat_option"];
		}
		
		// Check if post offset option is enabled.
		if ($this->plugin_args["srp_post_offset"] == 'yes') {
		
			// Filter result posts by category ID.
			$args["exclude"] = $this->singleID;
		}
		
		// Check if global post offset option is enabled.
		if ($this->widget_args["srp_post_global_offset_option"] != 0) {
		
			// Filter result posts by category ID.
			$args["offset"] = $this->widget_args["srp_post_global_offset_option"];
		}
		
		// Check if exclude posts option is applied.
		if (!empty($this->widget_args["srp_exclude_option"])) {
			
			// Filter result posts by category ID.
			$args["exclude"] = $this->widget_args["srp_exclude_option"];
		}
		
		// Check if include posts option is applied.
		if (!empty($this->widget_args["srp_include_option"])) {
			
			// Filter result posts by category ID.
			$args["include"] = $this->widget_args["srp_include_option"];
		}

		// Calling built-in Wordpress 'get_posts' function.
		$result_posts = get_posts($args);
		
		// Checking if result posts array is empty.
		if (empty($result_posts)) {
		
			// No recent posts available. Return empty array.
			return $result_posts;
		}
		
		// Check if random posts option is checked.
		if ($this->widget_args["srp_orderby_post_option"] != "") {
			
			// Let's shuffle the result array.
			shuffle($result_posts);
		}
		
		// Fix issues that let included IDs override the max number of post displayed.
		$output_array = array_slice($result_posts, 0, $args["numberposts"]);

		// Return result array.
		return $output_array;
	}
	
	// Main method to display posts.
	public function displayPosts($widget_call = NULL, $return_mode) {
	
		// Declaring global $post variable.
		global $post;

		// Open Widget Container.
		$srp_content = "<div id=\"srp" . $this->plugin_args["srp_version"] . "\" class=\"srp-widget-container\">";
		
		// Check if this method has been called from a widget or from a direct PHP call.
		if (!$widget_call) {
		
			// Check for widget title hiding option.
			if ('yes' != $this->widget_args["srp_widget_title_hide_option"]) {
			
				// Check if SRP is displaying a category filter result and if it should use the linked category title.
				if ( ($this->widget_args["srp_filter_cat_option"] != '') && ($this->plugin_args["srp_category_title"] == "yes") ) {
					
					// Get Category Link.
					$srp_category_link = get_category_link($this->widget_args["srp_filter_cat_option"]);
					
					// Display Category Title.
					$srp_content .= "<h3 class=\"widget-title\"><a title=\"" . get_cat_name($this->widget_args["srp_filter_cat_option"]) . "\" ";
					
					// Checking for nofollow option.
					if($this->widget_args["srp_add_nofollow_option"] == 'yes') {
						$srp_content .= "rel=\"nofollow\" ";
					}
					
					$srp_content .= "href=\"" . $srp_category_link . "\">" . get_cat_name($this->widget_args["srp_filter_cat_option"]) . "</a></h3>";
					
				} else {
				
					// Display Widget Title.
					$srp_content .= "<h3 class=\"widget-title\">" . $this->srp_sanitize($this->widget_args["srp_widget_title"]) . "</h3>";
				}
			}
		}
		
		// Fetching recent posts.
		$recent_posts = $this->getPosts();
		
		// Checking if posts are available.
		if (empty($recent_posts)) {
		
			// No posts available. Display message.
			$srp_content .= "<p>" . $this->srp_sanitize($this->plugin_args['srp_noposts_message']) . "</p>";
			
		} else {
			
			// Recent posts are available. Cycle through result posts.
			foreach($this->getPosts() as $post) {
			
				// Prepare access to all post data.
				setup_postdata($post);
				
				// Single Post Container.
				$srp_content .= "<div class=\"srp-widget-singlepost\">";
				
				// Check if thumbnail option has been set to true.
				if ($this->widget_args["srp_thumbnail_option"] == 'yes') {
					
					// Thumbnail box.
					$srp_content .= "<div class=\"srp-thumbnail-box\">";
				
					// Check if 'Thumbnail Mode' is supported by the current theme and post has a related thumbnail.
					if (function_exists('has_post_thumbnail') && has_post_thumbnail()) {
					
						// Retrieving thumbnail src attribute.
						$thumbabs = base64_encode(get_attached_file(get_post_thumbnail_id($post->ID)));

						// Check for thumbnail link option.
						if ('yes' == $this->plugin_args['srp_thumbnail_link']) {
						
							// Display thumbnail with link to post.
							$srp_content .= "<a class=\"srp-widget-thmblink\" href=\"" . get_permalink($post->ID) . "\" title=\"" . the_title_attribute(array('echo' => 0)) . "\" ";
							
							// Checking for nofollow option.
							if($this->widget_args["srp_add_nofollow_option"] == 'yes') {
								$srp_content .= "rel=\"nofollow\"";
							}
							$srp_content .= ">";
							
							$srp_content .= "<img alt=\"" . the_title_attribute(array('echo' => 0)) . "\" class=\"srp-widget-thmb\" src=\"" . SRP_PLUGIN_URL . SRP_THUMBPHP_HANDLER . "?width=" . $this->widget_args["srp_thumbnail_wdg_width"] . "&height=" . $this->widget_args["srp_thumbnail_wdg_height"] . "&rotation=" . $this->widget_args["srp_thumbnail_rotation"] . "&file=" . $thumbabs . "\" />";
							$srp_content .= "</a>";
						
						} else {
						
							// Display post thumbnail without link.
							$srp_content .= "<img alt=\"" . the_title_attribute(array('echo' => 0)) . "\" class=\"srp-widget-thmb\" src=\"" . SRP_PLUGIN_URL . SRP_THUMBPHP_HANDLER . "?width=" . $this->widget_args["srp_thumbnail_wdg_width"] . "&height=" . $this->widget_args["srp_thumbnail_wdg_height"] . "&rotation=" . $this->widget_args["srp_thumbnail_rotation"] . "&file=" . $thumbabs . "\" />";
						}
						
					} else {
					
						// Current theme doesn't support post thumbnail or current post has no thumbnail associated. Let's switch the entire process on using extracted first post images.
						if ('yes' == $this->plugin_args['srp_thumbnail_link']) {
						
							// Display thumbnail with link to post.
							$srp_content .= "<a class=\"srp-widget-thmblink\" href=\"" . get_permalink($post->ID) . "\" title=\"" . the_title_attribute(array('echo' => 0)) . "\" ";
							
							// Checking for nofollow option.
							if($this->widget_args["srp_add_nofollow_option"] == 'yes') {
								$srp_content .= "rel=\"nofollow\"";
							}
							$srp_content .= ">";
							
							$srp_content .= $this->getFirstImageUrl($this->widget_args["srp_thumbnail_wdg_width"], $this->widget_args["srp_thumbnail_wdg_height"], the_title_attribute(array('echo' => 0)));
							$srp_content .= "</a>";
							
						} else {
						
							// Display post thumbnail without link.
							$srp_content .= $this->getFirstImageUrl($this->widget_args["srp_thumbnail_wdg_width"], $this->widget_args["srp_thumbnail_wdg_height"], the_title_attribute(array('echo' => 0)));
						}
					}
					
					// EOF Thumbnail box.
					$srp_content .= "</div>";
				}
				
				// BOF Content Box.
				$srp_content .= "<div class=\"srp-content-box\">";				
				
				// Checking for "no content at all" option. In this case, leave the content-box empty.
				if ('nocontent' != $this->widget_args['srp_content_post_option']) {
				
					// Print Post Title.
					$srp_content .= "<h4 class=\"srp-widget-title\">";
					$srp_content .= "<a class=\"srp-widget-title-link\" ";
					
					// Checking for nofollow option.
					if($this->widget_args["srp_add_nofollow_option"] == 'yes') {
						$srp_content .= "rel=\"nofollow\" ";
					}
					$srp_content .= "href=\"" . get_permalink($post->ID) . "\" title=\"" . the_title_attribute(array('echo' => 0)) . "\">" . $this->extractTitle($post) . "</a>";
					$srp_content .= "</h4>";
					
					// Check for post date option.
					if ('yes' == $this->widget_args["srp_post_date_option"]) {
					
						// Output post date with selected arguments.
						$srp_content .= "<span class=\"srp-widget-date\">" . get_the_time($this->plugin_args['srp_date_content']) . "</span>";
					}
					
					// Check for Post Content Option.
					if ('titleexcerpt' == $this->widget_args["srp_content_post_option"]) {
						
						// Print Excerpt.
						$srp_content .= "<p class=\"srp-widget-excerpt\">" . $this->extractExcerpt($post);
						
						// Check if there is an image stringbreak
						if ($this->plugin_args['srp_image_string_break'] != "") {
							
							// Display image button.
							$srp_content .= "<a class=\"srp-widget-stringbreak-link-image\" href=\"" . get_permalink($post->ID) . "\" title=\"" . the_title_attribute(array('echo' => 0)) . "\" ";
							
							// Checking for nofollow option.
							if($this->widget_args["srp_add_nofollow_option"] == 'yes') {
								$srp_content .= "rel=\"nofollow\" ";
							}
							$srp_content .= "><img class=\"srp-widget-stringbreak-image\" src=\"" . $this->srp_sanitize($this->plugin_args['srp_image_string_break']) . "\" /></a></p>";
						
						} elseif ($this->plugin_args['srp_string_break'] != "") {
						
							// Use a text stringbreak. Check if string break should be linked to post.
							if ('yes' == $this->plugin_args['srp_string_break_link']) {
							
								// Print string break with link to post.
								$srp_content .= "<a class=\"srp-widget-stringbreak-link\" href=\"" . get_permalink($post->ID) . "\" title=\"" . the_title_attribute(array('echo' => 0)) . "\" ";
								
								// Checking for nofollow option.
								if($this->widget_args["srp_add_nofollow_option"] == 'yes') {
									$srp_content .= "rel=\"nofollow\"";
								}
								$srp_content .= ">" . $this->srp_sanitize($this->plugin_args['srp_string_break']) . "</a></p>";
							} else {
								
								// Print string break without link to post.
								$srp_content .= "<span class=\"srp-widget-stringbreak\">" . $this->srp_sanitize($this->plugin_args['srp_string_break']) . "</span></p>";
							}
						}
					}
				}
				
				// EOF Content Box.
				$srp_content .= "</div>";
				
				// Closing Single Post Container.
				$srp_content .= "</div>";
				
			} // EOF foreach cycle.
			
			// Reset $post data array.
			wp_reset_postdata();
			
		} // EOF Empty posts check.
		
		$srp_content .= "</div>"; // Closing Widget Container.
		
		// Switch through display return mode
		switch($return_mode) {
		
			case"print":
				echo $srp_content;
			break;
			
			case "return":
				return $srp_content;
			break;
		}
	}
	
/*
| ---------------------------------------------
| UTILITY METHODS
| In this section we collect several utility
| general methods.
| ---------------------------------------------
*/
	// This method sanitize strings output.
	private function srp_sanitize($string) {
		
		// We need to remove all the exceeding stuff. Remove shortcodes and slashes.
		$temp_output = trim(stripslashes(strip_shortcodes($string)));
		
		// Applying qTranslate Filter if this exists.
		if (function_exists('qtrans_useCurrentLanguageIfNotFoundShowAvailable')) {
			$temp_output = qtrans_useCurrentLanguageIfNotFoundShowAvailable($temp_output);
		}
		
		// If there is a tag filter, keep them separated from strip_tags.
		if (!empty($this->plugin_args["srp_excerpt_allowed_tags"])) {
		
			// Strip tags except the ones specified.
			return strip_tags($temp_output, htmlspecialchars_decode($this->plugin_args["srp_excerpt_allowed_tags"]));
			
		} else {
		
			// Otherwise completely strip tags from text.
			return strip_tags($temp_output);
		}
	}
	
	// This method uses the same logic of PHP function 'substr', but works with words instead of characters.
	private function substrWords($str, $n) {
		
		// Check if max length is equal to original string length. In that case, return the string without making any 'cut'.
		if (str_word_count($str, 0) > $n) {

			// Uses PHP 'str_word_count' function to extract total words and put them into an array.
			$w = explode(" ", $str);
			
			// Lets' cut the array using our max length variable ($n).
			array_splice($w, $n);
			
			// Re-convert array to string and return.
			return implode(" ", $w);
			
		} else {
			
			// Return string as it is, without making any 'cut'.
			return $str;
		}
	}
} // EOF Class.
