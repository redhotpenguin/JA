<?php
/*
| ----------------------------------------------------
| File        : class-widgets.php
| Project     : Special Recent Posts plugin for Wordpress
| Version     : 1.9
| Description : This file contains the widget main class.
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress/
| ----------------------------------------------------
*/

class WDG_SpecialRecentPosts extends WP_Widget {

// Declaring global plugin values.
private $plugin_args;

/*
| ---------------------------------------------
| CLASS CONSTRUCTOR & DECONSTRUCTOR
| ---------------------------------------------
*/
	// Class Constructor.
	// In this section we define the widget global values.
	function WDG_SpecialRecentPosts() {
	
		// Setting up widget options.
        $widget_ops = array (
            'classname'   => __('Special Recent Posts', SRP_TRANSLATION_ID),
            'description' => __('The Special Recent Posts widget. Drag to configure.', SRP_TRANSLATION_ID)
        );
		
        // Assigning widget options.
		$this->WP_Widget('WDG_SpecialRecentPosts', __('Special Recent Posts', SRP_TRANSLATION_ID), $widget_ops);
		
		// Assigning global plugin option values to local variable.
		$this->plugin_args = get_option('srp_plugin_options');
	}
	
/*
| ---------------------------------------------
| WIDGET FORM DISPLAY METHOD
| ---------------------------------------------
*/
	// Main form widget method.
	function form($instance) {
	
		// Outputs the options form on widget panel.
		$this->buildWidgetForm($instance);
	}

/*
| ---------------------------------------------
| WIDGET UPDATE & MAIN METHODS
| ---------------------------------------------
*/
	// Main method for widget update process.
	function update($new_instance, $old_instance) {
	
		// Processes widget options to be saved.
		$instance = $old_instance;
		$instance["srp_post_type"]                =  $new_instance["srp_post_type"];
		$instance["srp_thumbnail_option"]         =  $new_instance["srp_thumbnail_option"];
		$instance["srp_thumbnail_rotation"]       =  $new_instance["srp_thumbnail_rotation"];
		$instance["srp_order_post_option"]        =  $new_instance["srp_order_post_option"];
		$instance["srp_orderby_post_option"]      =  $new_instance["srp_orderby_post_option"];
		$instance["srp_content_post_option"]      =  $new_instance["srp_content_post_option"];
		$instance["srp_post_status_option"]       =  $new_instance["srp_post_status_option"];
		$instance["srp_post_date_option"]         =  $new_instance["srp_post_date_option"];
		$instance["srp_widget_title_hide_option"] =  $new_instance["srp_widget_title_hide_option"];
		$instance["srp_add_nofollow_option"]      =  $new_instance["srp_add_nofollow_option"];
		$instance["srp_wdg_excerpt_length_mode"]  =  $new_instance["srp_wdg_excerpt_length_mode"];
		$instance["srp_wdg_title_length_mode"]    =  $new_instance["srp_wdg_title_length_mode"];
		$instance["srp_filter_cat_option"]        =  trim(strip_tags(strip_shortcodes($new_instance["srp_filter_cat_option"])));
		$instance["srp_exclude_option"]           =  trim(strip_tags(strip_shortcodes($new_instance["srp_exclude_option"])));
		$instance["srp_include_option"]           =  trim(strip_tags(strip_shortcodes($new_instance["srp_include_option"])));
		$instance["srp_widget_title"]             =  trim(strip_tags(strip_shortcodes($new_instance["srp_widget_title"])));
		$instance["srp_custom_post_type_option"]  =  trim(strip_tags(strip_shortcodes($new_instance["srp_custom_post_type_option"])));
		
		// Updating widget values.
		
		// Handling max number of posts option.
		if (is_numeric(strip_tags($new_instance["srp_number_post_option"])) && (strip_tags($new_instance["srp_number_post_option"]) != "0") )
			$instance["srp_number_post_option"] = trim(strip_tags(strip_shortcodes($new_instance["srp_number_post_option"])));
		
		// Handling thumbnail width option.
		if (is_numeric(strip_tags($new_instance["srp_thumbnail_wdg_width"])))
			$instance["srp_thumbnail_wdg_width"] = trim(strip_tags(strip_shortcodes($new_instance["srp_thumbnail_wdg_width"])));
			
		// Handling thumbnail height option.
		if (is_numeric(strip_tags($new_instance["srp_thumbnail_wdg_height"])))
			$instance["srp_thumbnail_wdg_height"] = trim(strip_tags(strip_shortcodes($new_instance["srp_thumbnail_wdg_height"])));
			
		if (is_numeric(strip_tags($new_instance["srp_wdg_excerpt_length"])) && (strip_tags($new_instance["srp_wdg_excerpt_length"]) != "0") )
			$instance["srp_wdg_excerpt_length"] = trim(strip_tags(strip_shortcodes($new_instance["srp_wdg_excerpt_length"])));
			
		if (is_numeric(strip_tags($new_instance["srp_wdg_title_length"])) && (strip_tags($new_instance["srp_wdg_title_length"]) != "0") )
			$instance["srp_wdg_title_length"] = trim(strip_tags(strip_shortcodes($new_instance["srp_wdg_title_length"])));
			
		// Handling thumbnail height option.
		if (is_numeric(strip_tags($new_instance["srp_filter_cat_option"])))
			$instance["srp_filter_cat_option"] = trim(strip_tags(strip_shortcodes($new_instance["srp_filter_cat_option"])));
		
		// Handling global post offset option.
		if ( (is_numeric(strip_tags($new_instance["srp_post_global_offset_option"]))) && ($new_instance["srp_post_global_offset_option"] > 0) ) {
			$instance["srp_post_global_offset_option"] = trim(strip_tags(strip_shortcodes($new_instance["srp_post_global_offset_option"])));
		} else {
		
			// If global post offset option has a blank value, set it to 0.
			$instance["srp_post_global_offset_option"] = 0;
		}
			
		// Return new widget instance.
		return $instance;
	}
	
	// Main widget method. Main logic here.
	function widget($args, $instance) {
	
		// Extracting Arguments.
		extract($args, EXTR_SKIP);
		
		// Print Before Widget stuff.
		echo $before_widget;
		
		if ('yes' != $instance["srp_widget_title_hide_option"]) {
		
			// Print before title.
			echo $before_title;
			
			// Check if SRP is displaying a category filter result and if it should use the linked category title.
			if ( ($instance["srp_filter_cat_option"] != '') && ($this->plugin_args["srp_category_title"] == "yes") ) {
				
				// Get Category Link.
				$srp_category_link = get_category_link($instance["srp_filter_cat_option"]);
				
				// Display Category Title.
				echo "<a title=\"" . get_cat_name($instance["srp_filter_cat_option"]) . "\" ";
				
				// Checking for nofollow option.
				if($instance["srp_add_nofollow_option"] == 'yes') {
					echo "rel=\"nofollow\" ";
				}
				
				echo "href=\"" . $srp_category_link . "\">" . get_cat_name($instance["srp_filter_cat_option"]) . "</a>";
				
			} else {
			
				// Display Widget Title.
				echo $instance["srp_widget_title"];
			}
			
			// Print after title.
			echo $after_title;
		}

		// Creating an instance of Special Posts Class.
		$srp = new SpecialRecentPosts($instance);
		
		// Display Posts.
		$srp->displayPosts(true, 'print');
		
		// Print After Widget stuff.
		echo $after_widget;
	}
	
/*
| ---------------------------------------------
|  METHODS
| ---------------------------------------------
*/
	
	// Build the widget admin form.
	function buildWidgetForm($instance) {
	
		if (empty($instance)) {
			
			// Loading default widget values.
			global $srp_default_widget_values;
			
			// Loading default plugin settings.
			$plugin_args = get_option('srp_plugin_options');
			
			// Merging default values with instance array, in case this is empty.
			$instance = wp_parse_args( (array) $instance, $srp_default_widget_values);
			
			// Setting up thumbnails width, height and global offset to default values if this is the first run.
			if (!isset($instance["srp_thumbnail_wdg_width"])) {
				$instance["srp_thumbnail_wdg_width"] = $plugin_args["srp_thumbnail_width"];
			}
			
			if (!isset($instance["srp_thumbnail_wdg_height"])) {
				$instance["srp_thumbnail_wdg_height"] = $plugin_args["srp_thumbnail_height"];
			}
			
			if ( (!isset($instance["srp_post_global_offset_option"])) || ('' == $instance["srp_post_global_offset_option"])) {
				$instance["srp_post_global_offset_option"] = 0;
			}
		}
?>
		<!-- BOF Widget Tabs -->
		<ul id="srp_widget_tabs">
			<li>
				<a onClick="javascript:srpTabsSwitcher('tobasic', <?php echo $this->number; ?>);" class="srp_tab_basic_link active" title="<?php _e('Basic Options', SRP_TRANSLATION_ID); ?>" href="#"><?php _e('Basic Options', SRP_TRANSLATION_ID); ?></a>
			</li>
			
			<li>
				<a onClick="javascript:srpTabsSwitcher('toadvanced', <?php echo $this->number; ?>);" class="srp_tab_advanced_link" title="<?php _e('Advanced Options', SRP_TRANSLATION_ID); ?>" href="#"><?php _e('Advanced Options', SRP_TRANSLATION_ID); ?></a>
			</li>
		</ul>
		<!-- EOF Widget Tabs -->
		
		<!-- BOF Widget Options -->
		<ul id="srp-widget-optionlist-basic-<?php echo $this->number; ?>" class="srp-widget-optionlist-basic">

			<!-- BOF Widget Title Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_widget_title'); ?>" class="srp-widget-label"><?php _e('Enter Widget Title', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_widget_title'); ?>" name="<?php echo $this->get_field_name('srp_widget_title'); ?>" value="<?php echo htmlspecialchars($instance["srp_widget_title"], ENT_QUOTES); ?>" size="30" />
			</li>
			<!-- EOF Widget Title Option. -->
			
			<!-- BOF Widget Title Hide Option. -->
			<li>
				<input type="checkbox" id="<?php echo $this->get_field_id('srp_widget_title_hide_option'); ?>" name="<?php echo $this->get_field_name('srp_widget_title_hide_option'); ?>" value="yes" <?php checked($instance["srp_widget_title_hide_option"], 'yes'); ?> />
				<label for="<?php echo $this->get_field_id('srp_widget_title_hide_option'); ?>" class="srp-widget-label-inline"><?php _e('Hide Widget Title', SRP_TRANSLATION_ID); ?></label>
			</li>
			<!-- EOF Widget Title Hide Option. -->
			
			<!-- BOF Thumbnail Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_thumbnail_option'); ?>" class="srp-widget-label"><?php _e('Display Thumbnails?', SRP_TRANSLATION_ID); ?></label>
				<select id="<?php echo $this->get_field_id('srp_thumbnail_option'); ?>" name="<?php echo $this->get_field_name('srp_thumbnail_option'); ?>" class="srp-widget-select">
					<option value="yes" <?php selected($instance["srp_thumbnail_option"], 'yes'); ?>><?php _e('Yes', SRP_TRANSLATION_ID); ?></option>
					<option value="no" <?php selected($instance["srp_thumbnail_option"], 'no'); ?>><?php _e('No', SRP_TRANSLATION_ID); ?></option>
				</select>
			</li>
			<!-- EOF Thumbnail Option. -->
			
			<!-- BOF Thumbnail Width. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_thumbnail_wdg_width'); ?>" class="srp-widget-label"><?php _e('Thumbnail Width', SRP_TRANSLATION_ID); ?></label>
				<small><?php _e('will override default settings'); ?></small>
				<input type="text" id="<?php echo $this->get_field_id('srp_thumbnail_wdg_width'); ?>" name="<?php echo $this->get_field_name('srp_thumbnail_wdg_width'); ?>" value="<?php echo htmlspecialchars($instance["srp_thumbnail_wdg_width"], ENT_QUOTES); ?>" size="8" />px
			</li>
			<!-- EOF Thumbnail Width. -->
			
			<!-- BOF Thumbnail Height. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_thumbnail_wdg_height'); ?>" class="srp-widget-label"><?php _e('Thumbnail Height', SRP_TRANSLATION_ID); ?></label>
				<small><?php _e('will override default settings'); ?></small>
				<input type="text" id="<?php echo $this->get_field_id('srp_thumbnail_wdg_height'); ?>" name="<?php echo $this->get_field_name('srp_thumbnail_wdg_height'); ?>" value="<?php echo htmlspecialchars($instance["srp_thumbnail_wdg_height"], ENT_QUOTES); ?>" size="8" />px
			</li>
			<!-- EOF Thumbnail Height. -->
			
			<!-- BOF Title Max Text Size. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_wdg_title_length'); ?>" class="srp-widget-label"><?php _e('Cut title text after:', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_wdg_title_length'); ?>" name="<?php echo $this->get_field_name('srp_wdg_title_length'); ?>" value="<?php echo htmlspecialchars($instance["srp_wdg_title_length"], ENT_QUOTES); ?>" size="8" />
				<select id="<?php echo $this->get_field_id('srp_wdg_title_length_mode'); ?>" name="<?php echo $this->get_field_name('srp_wdg_title_length_mode'); ?>" class="srp-widget-select">
					<option value="words" <?php selected($instance["srp_wdg_title_length_mode"], 'words'); ?>><?php _e('Words', SRP_TRANSLATION_ID); ?></option>
					<option value="chars" <?php selected($instance["srp_wdg_title_length_mode"], 'chars'); ?>><?php _e('Characters', SRP_TRANSLATION_ID); ?></option>
					<option value="fulltitle" <?php selected($instance["srp_wdg_title_length_mode"], 'fulltitle'); ?>><?php _e('Use Full Title', SRP_TRANSLATION_ID); ?></option>
				</select><br />
				<small><?php _e('will override default settings'); ?></small>
			</li>
			<!-- EOF Title Max Text Size. -->
			
			<!-- BOF Post Excerpt Max Text Size. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_wdg_excerpt_length'); ?>" class="srp-widget-label"><?php _e('Cut post text after:', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_wdg_excerpt_length'); ?>" name="<?php echo $this->get_field_name('srp_wdg_excerpt_length'); ?>" value="<?php echo htmlspecialchars($instance["srp_wdg_excerpt_length"], ENT_QUOTES); ?>" size="8" />
				<select id="<?php echo $this->get_field_id('srp_wdg_excerpt_length_mode'); ?>" name="<?php echo $this->get_field_name('srp_wdg_excerpt_length_mode'); ?>" class="srp-widget-select">
					<option value="words" <?php selected($instance["srp_wdg_excerpt_length_mode"], 'words'); ?>><?php _e('Words', SRP_TRANSLATION_ID); ?></option>
					<option value="chars" <?php selected($instance["srp_wdg_excerpt_length_mode"], 'chars'); ?>><?php _e('Characters', SRP_TRANSLATION_ID); ?></option>
					<option value="fullexcerpt" <?php selected($instance["srp_wdg_excerpt_length_mode"], 'fullexcerpt'); ?>><?php _e('Use Full Excerpt', SRP_TRANSLATION_ID); ?></option>
				</select><br />
				<small><?php _e('will override default settings'); ?></small>
			</li>
			<!-- EOF Post Excerpt Max Text Size. -->
			
			<!-- BOF Max number of posts Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_number_post_option'); ?>" class="srp-widget-label"><?php _e('Max number of posts/pages to display?', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_number_post_option'); ?>" name="<?php echo $this->get_field_name('srp_number_post_option'); ?>" value="<?php echo stripslashes($instance['srp_number_post_option']); ?>" size="5" />
			</li>
			<!-- EOF Max number of posts Option. -->
			
			<!-- BOF Post Order Display Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_order_post_option'); ?>" class="srp-widgetLabel"><?php _e('Select posts/pages order:', SRP_TRANSLATION_ID); ?></label>
				<select id="<?php echo $this->get_field_id('srp_order_post_option'); ?>" name="<?php echo $this->get_field_name('srp_order_post_option'); ?>" class="srp-widget-select">
					<option value="DESC" <?php selected($instance["srp_order_post_option"], 'DESC'); ?>><?php _e('Latest first (DESC)', SRP_TRANSLATION_ID); ?></option>
					<option value="ASC" <?php selected($instance["srp_order_post_option"], 'ASC'); ?>><?php _e('Oldest first (ASC)', SRP_TRANSLATION_ID); ?></option>
				</select>
			</li>
			<!-- EOF Post Order Display Option. -->
			
			<!-- BOF Random Posts Option. -->
			<li>
				<input type="checkbox" id="<?php echo $this->get_field_id('srp_orderby_post_option'); ?>" name="<?php echo $this->get_field_name('srp_orderby_post_option'); ?>" value="rand" <?php checked($instance["srp_orderby_post_option"], 'rand'); ?> />
				<label for="<?php echo $this->get_field_id('srp_orderby_post_option'); ?>" class="srp-widget-label-inline"><?php _e('Enable Random mode', SRP_TRANSLATION_ID); ?></label>
			</li>
			<!-- EOF Random Posts Option. -->
			
			<!-- BOF Display Content Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_content_post_option'); ?>" class="srp-widget-label"><?php _e('Content Display Mode', SRP_TRANSLATION_ID); ?></label>
				<select id="<?php echo $this->get_field_id('srp_content_post_option'); ?>" name="<?php echo $this->get_field_name('srp_content_post_option'); ?>" class="srp-widget-select">
					<option value="titleonly" <?php selected($instance["srp_content_post_option"], 'titleonly'); ?>><?php _e('Title only', SRP_TRANSLATION_ID); ?></option>
					<option value="titleexcerpt" <?php selected($instance["srp_content_post_option"], 'titleexcerpt'); ?>><?php _e('Title and Excerpt', SRP_TRANSLATION_ID); ?></option>
					<option value="nocontent" <?php selected($instance["srp_content_post_option"], 'nocontent'); ?>><?php _e('No content', SRP_TRANSLATION_ID); ?></option>
				</select>
			</li>
			<!-- EOF Display Content Option. -->
			
			<!-- BOF Display Date Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_post_date_option'); ?>" class="srp-widget-label"><?php _e('Display post date?', SRP_TRANSLATION_ID); ?></label>
				<select id="<?php echo $this->get_field_id('srp_post_date_option'); ?>" name="<?php echo $this->get_field_name('srp_post_date_option'); ?>" class="srp_widget-select">
					<option value="yes" <?php selected($instance["srp_post_date_option"], 'yes'); ?>><?php _e('Yes', SRP_TRANSLATION_ID); ?></option>
					<option value="no" <?php selected($instance["srp_post_date_option"], 'no'); ?>><?php _e('No', SRP_TRANSLATION_ID); ?></option>
				</select>
			</li>
			<!-- EOF Display Date Option. -->
		</ul>
		<!-- EOF Widget Options -->
		
		<ul id="srp-widget-optionlist-advanced-<?php echo $this->number; ?>" class="srp-widget-optionlist-advanced">
			
			<!-- BOF Post Type Display. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_post_type'); ?>" class="srp-widget-label"><?php _e('Display Posts or Pages?', SRP_TRANSLATION_ID); ?></label>
				<select id="<?php echo $this->get_field_id('srp_post_type'); ?>" name="<?php echo $this->get_field_name('srp_post_type'); ?>" class="srp-widget-select">
					<option value="post" <?php selected($instance["srp_post_type"], 'post'); ?>><?php _e('Posts', SRP_TRANSLATION_ID); ?></option>
					<option value="page" <?php selected($instance["srp_post_type"], 'page'); ?>><?php _e('Pages', SRP_TRANSLATION_ID); ?></option>
				</select>
			</li>
			<!-- EOF Post Type Display. -->
			
			<!-- BOF Custom Post Types Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_custom_post_type_option'); ?>" class="srp-widget-label"><?php _e('Specify a custom post type.', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_custom_post_type_option'); ?>" name="<?php echo $this->get_field_name('srp_custom_post_type_option'); ?>" value="<?php echo stripslashes($instance['srp_custom_post_type_option']); ?>" size="30" /><br />
				<small><?php _e('If you specify a custom post type, all post type options will be overrided.'); ?></small>
			</li>
			<!-- EOF Custom Post Types Option. -->
			
			<!-- BOF Post Status Mode. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_post_status_option'); ?>" class="srp-widget-label"><?php _e('Post Status?', SRP_TRANSLATION_ID); ?></label>
				<select id="<?php echo $this->get_field_id('srp_post_status_option'); ?>" name="<?php echo $this->get_field_name('srp_post_status_option'); ?>" class="srp-widget-select">
					<option value="publish" <?php selected($instance["srp_post_status_option"], 'publish'); ?>><?php _e('Published (Default)', SRP_TRANSLATION_ID); ?></option>
					<option value="private" <?php selected($instance["srp_post_status_option"], 'private'); ?>><?php _e('Private', SRP_TRANSLATION_ID); ?></option>
					<option value="inherit" <?php selected($instance["srp_post_status_option"], 'inherit'); ?>><?php _e('Inherit', SRP_TRANSLATION_ID); ?></option>
					<option value="pending" <?php selected($instance["srp_post_status_option"], 'pending'); ?>><?php _e('Pending', SRP_TRANSLATION_ID); ?></option>
					<option value="future" <?php selected($instance["srp_post_status_option"], 'future'); ?>><?php _e('Future', SRP_TRANSLATION_ID); ?></option>
					<option value="draft" <?php selected($instance["srp_post_status_option"], 'draft'); ?>><?php _e('Draft', SRP_TRANSLATION_ID); ?></option>
					<option value="trash" <?php selected($instance["srp_post_status_option"], 'trash'); ?>><?php _e('Trash', SRP_TRANSLATION_ID); ?></option>
				</select>
			</li>
			<!-- EOF Post Status Mode. -->
			
			<!-- BOF Thumbnail Display Mode. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_thumbnail_rotation'); ?>" class="srp-widget-label"><?php _e('Rotate Thumbnail?', SRP_TRANSLATION_ID); ?></label>
				<select id="<?php echo $this->get_field_id('srp_thumbnail_rotation'); ?>" name="<?php echo $this->get_field_name('srp_thumbnail_rotation'); ?>" class="srp-widget-select">
					<option value="no" <?php selected($instance["srp_thumbnail_rotation"], 'adaptive'); ?>><?php _e('No (Default)', SRP_TRANSLATION_ID); ?></option>
					<option value="rotate-cw" <?php selected($instance["srp_thumbnail_rotation"], 'rotate-cw'); ?>><?php _e('Rotate CW', SRP_TRANSLATION_ID); ?></option>
					<option value="rotate-ccw" <?php selected($instance["srp_thumbnail_rotation"], 'rotate-ccw'); ?>><?php _e('Rotate CCW', SRP_TRANSLATION_ID); ?></option>
				</select>
			</li>
			<!-- EOF Thumbnail Display Mode. -->
			
			<!-- BOF Posts Offset Option.. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_post_global_offset_option'); ?>" class="srp-widget-label"><?php _e('Post Offset?', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_post_global_offset_option'); ?>" name="<?php echo $this->get_field_name('srp_post_global_offset_option'); ?>" value="<?php echo stripslashes($instance['srp_post_global_offset_option']); ?>" size="5" /><br />
				<small><?php _e('Enter the number of posts to be skipped from the beginning. Leave blank or zero for default.'); ?></small>
			</li>
			<!-- EOF Posts Offset Option.. -->
			
			<!-- BOF Category Filter Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_filter_cat_option'); ?>" class="srp-widget-label"><?php _e('Category Filter:', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_filter_cat_option'); ?>" name="<?php echo $this->get_field_name('srp_filter_cat_option'); ?>" value="<?php echo htmlspecialchars($instance["srp_filter_cat_option"], ENT_QUOTES); ?>" size="30" /><br />
				<small><?php _e('Enter a comma separated list of categories IDs. Leave blank for no specific filtering.', SRP_TRANSLATION_ID); ?></small>
			</li>
			<!-- EOF Category Filter Option. -->
			
			<!-- BOF Include Posts Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_include_option'); ?>" class="srp-widget-label-inline"><?php _e('Include Posts/Pages IDs', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_include_option'); ?>" name="<?php echo $this->get_field_name('srp_include_option'); ?>" value="<?php echo htmlspecialchars($instance["srp_include_option"], ENT_QUOTES); ?>" size="30" /><br />
				<small><?php _e('Enter a comma separated list of posts/pages IDs. Leave blank for no specific inclusion.', SRP_TRANSLATION_ID); ?></small>
			</li>
			<!-- EOF Include Posts Option. -->
			
			<!-- BOF Exclude Posts Option. -->
			<li>
				<label for="<?php echo $this->get_field_id('srp_exclude_option'); ?>" class="srp-widget-label-inline"><?php _e('Exclude Posts/Pages IDs', SRP_TRANSLATION_ID); ?></label>
				<input type="text" id="<?php echo $this->get_field_id('srp_exclude_option'); ?>" name="<?php echo $this->get_field_name('srp_exclude_option'); ?>" value="<?php echo htmlspecialchars($instance["srp_exclude_option"], ENT_QUOTES); ?>" size="30" /><br />
				<small><?php _e('Enter a comma separated list of posts/pages IDs. Leave blank for no exclusion.', SRP_TRANSLATION_ID); ?></small>
			</li>
			<!-- EOF Exclude Posts Option. -->
			
			<!-- BOF No-Follow option link switcher. -->
			<li>
				<input type="checkbox" id="<?php echo $this->get_field_id('srp_add_nofollow_option'); ?>" name="<?php echo $this->get_field_name('srp_add_nofollow_option'); ?>" value="yes" <?php checked($instance["srp_add_nofollow_option"], 'yes'); ?> />
				<label for="<?php echo $this->get_field_id('srp_add_nofollow_option'); ?>" class="srp-widget-label-inline"><?php _e('Add nofollow attribute to links?', SRP_TRANSLATION_ID); ?></label>
			</li>
			<!-- EOF No-Follow option link switcher. -->
		</ul>
<?php
	}
}
