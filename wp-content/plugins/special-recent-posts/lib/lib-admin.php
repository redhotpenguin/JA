<?php
/*
| --------------------------------------------------------
| File        : lib-admin.php
| Version     : 1.9
| Description : This file contains various functions
|               for plugin initialization and
|               admin panel building.
| Project     : Special Recent Posts plugin for Wordpress
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress/
| --------------------------------------------------------
*/

/*
| ---------------------------------------------
| PLUGIN INIT FUNCTIONS
| ---------------------------------------------
*/

// Main initializing function.
function srp_admin_init() {
	
	// Registering Plugin admin stylesheet.
	wp_register_style('srp-admin-stylesheet' , SRP_PLUGIN_URL . SRP_ADMIN_CSS);
	
	// Registering Plugin widget stylesheet.
	wp_register_style('srp-widget-stylesheet', SRP_PLUGIN_URL . SRP_WIDGET_CSS);
	
	// Registering Custom Js Init Script.
	wp_register_script('srp-custom-js-init'  , SRP_PLUGIN_URL . SRP_JS_INIT);
	
	// Forcing Loading jQuery.
	wp_enqueue_script('jquery');
	
	// Enqueuing plugin admin widget stylesheet.
	wp_enqueue_style('srp-widget-stylesheet');
	
	// Enqueuing Custom Js Init Script.
	wp_enqueue_script('srp-custom-js-init');
	
	// Adding a new action link.
	add_filter('plugin_action_links', 'srp_plugin_action_links', 10, 2);
}

// Function for adding new action links on plugin's page.
function srp_plugin_action_links($links, $file) {

	// Check if we're on the correct plugin file.
	if ($file == SRP_PLUGIN_MAINFILE) {
		$links[] = '<a href="options-general.php?page=special-recent-posts/lib/lib-admin.php">'.__('Settings').'</a>';
	}

	// Return new embedded link.
	return $links;
}

// Function for plugin widget registration.
function srp_install_widgets() {

	// Register widget.
	register_widget("WDG_SpecialRecentPosts");
}

// This function checks whether the plugin has been updated.
// If it's so, it performs several checks before updating the plugin db options.
function srp_plugin_init() {

	// Importing global default options array.
	global $srp_default_plugin_values;
	
	// Checking if plugin db options exist.
	if (get_option('srp_plugin_options')) {
	
		// Setting current db options.
		$srp_current_options = get_option('srp_plugin_options');		
		
		// Checking if plugin has a db version option or if this is minor than the current version declared through the updated code.
		if ( (!isset($srp_current_options["srp_version"]) && isset($srp_default_plugin_values["srp_version"]) ) || ( version_compare($srp_current_options["srp_version"], $srp_default_plugin_values["srp_version"], '<')) ) {
		
			// Plugin version is prior to 1.5 or is lower to the current updated files.
			// For first, let's check for new array keys and eventually put them in the current array option.
			$srp_diff_array = array_diff_key($srp_default_plugin_values, $srp_current_options);
			
			// Check if there are no new array keys. In this case, we need to update only the version option.
			if (!empty($srp_diff_array)) {
				
				// Merge current option array with new values.
				$srp_result_array = array_merge($srp_current_options, $srp_diff_array);
				
				// Update current plugin option version.
				$srp_result_array["srp_version"] = $srp_default_plugin_values["srp_version"];
				
				// Update db options.
				update_option('srp_plugin_options', $srp_result_array);
				
			} else {
			
				// Update current plugin option version.
				$srp_current_options["srp_version"] = $srp_default_plugin_values["srp_version"];
				
				// Update db options.
				update_option('srp_plugin_options', $srp_current_options);
			}

		} else {
			// Current bulk is updated. Do Nothing.
		}
	} else {
		// First Install. Do nothing.
	}
}

/*
| ---------------------------------------------
| PLUGIN COMPATIBILITY CHECK
| ---------------------------------------------
*/

function check_plugin_compatibility() {
	
	// Checking for PHP version.
	$current_ver = phpversion();
    switch(version_compare($current_ver, SRP_REQUIRED_PHPVER)) {
		case -1:
			$error = new WP_Error('broke', __("<strong>Error!</strong> You're running an old version of PHP. In order for this plugin to work, you must enable your server with PHP support version 5.0.0+. Please contact your hosting/housing company support, and check how to enable it.</a>"));
			if (is_wp_error($error)) {
				echo "<div id=\"message\" class=\"error\"><p>" . $error->get_error_message() . "</p></div>";
			}
		break;
			
        case 0:
        case 1:
		break;
    }
	
	// Check for GD support.
	if (!function_exists("gd_info")) {
		$error = new WP_Error('broke', __("<strong>Error!</strong> GD libraries are not supported by your server. Please contact your hosting/housing company support, and check how to enable it. Without these libraries, thumbnails can't be properly resized and displayed."));
		if (is_wp_error($error)) {
		   echo "<div id=\"message\" class=\"error\"><p>" . $error->get_error_message() . "</p></div>";
		}
	}
	
	// Check for thumbnail option enabled theme.
	if (!current_theme_supports('post-thumbnails')) {
		$error = new WP_Error('broke', __("Warning! Your theme doesn't support post thumbnail. The plugin will keep on working with first post images only. To enable post thumbnail support, please check the <a href='http://codex.wordpress.org/Post_Thumbnails'> Wordpress Documentation</a>"));
		if (is_wp_error($error)) {
		   echo "<div id=\"message\" class=\"warning\"><p>" . $error->get_error_message() . "</p></div>";
		}
	}
}

/*
| ---------------------------------------------
| AMIN MENUS PAGE AND STYLESHEETS
| ---------------------------------------------
*/

// Main Admin setup function.
function srp_admin_setup() {
	
	// Adding SubMenu Page.
	$page = add_submenu_page('options-general.php', __('Special Recent Posts - Settings Page', 'Special Recent Posts - Settings Page'), __('Special Recent Posts', 'Special Recent Posts'), 'administrator', __FILE__, 'srp_admin_menu_options');
	
    // Using registered $page handle to hook stylesheet loading.
    add_action('admin_print_styles-' . $page, 'srp_admin_plugin_add_style');
}

// Main function to add admin stylesheet.
function srp_admin_plugin_add_style() {
	
	// Enqueuing plugin admin stylesheet.
	wp_enqueue_style('srp-admin-stylesheet');
}

// Main function to add widget stylesheet into current theme.
function srp_theme_css() {
	
	// Printing spcific stylesheet for widgets in current theme.
	$theme_css =  get_option('srp_plugin_options');
	echo "<style type=\"text/css\" media=\"screen\">" . stripslashes($theme_css['srp_themecss']) . "</style>";
	
	// Adding IE7 Fix.
	echo "<!--[if IE 7]>";
	echo "<link rel='stylesheet' id='css-ie-fix' href='" . SRP_PLUGIN_URL . SRP_IEFIX_CSS . "' type='text/css' media='all' /> ";
	echo "<![endif]-->";
}

/*
| ---------------------------------------------
| BUILDING PLUGIN OPTION PAGE
| ---------------------------------------------
*/

// Main function that builds the plugin admin page.
function srp_admin_menu_options() {

	// Checking if we have the manage option permission enabled.
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
?>
	<!-- Generating Option Page HTML. -->
	<div class="wrap">
		<div id="srp-admin-container">
			<?php
			
				// For first, let's check if there is some kind of compatibility error.
				check_plugin_compatibility();
				
				// Updating and validating data/POST Check.
				srp_update_data($_POST, get_option('srp_plugin_options'));
				
				// Importing global default options array.
				$srp_current_options = get_option('srp_plugin_options');
			?>
			
			<!-- BOF Title and Description section. -->
			<h2><?php _e('Special Recent Posts v' . SRP_PLUGIN_VERSION . '- Settings Page', SRP_TRANSLATION_ID); ?></h2>
			<div class="srp_option_header_l1">
			<?php _e('In this page you can configure the main settings for the Special Recent Posts plugin. 
				Keep in mind that these are basic options provided for any plugin instance. 
				Special options apply for each widget instance or PHP function call, to ensure maximum customization. 
				Go to Widget Page and drag the Special Recent Posts widget to see additional options available.<p><strong>(*) Required Field</strong></p>', SRP_TRANSLATION_ID); ?>
			</div>
			<div class="srp_option_header_l2">
				<a class="donate-logo" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PZD4ACBRFR9GS" title="Feel free to donate for this plugin. I'll be grateful if you will :)">
					<img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="" />
				</a>
			</div>
			<br style="clear:both;" />
			<!-- EOF Title and Description section. -->
			
				<!--  Open Form. -->
				<form id="srp_admin_form" name="srp_admin_form" action="" method="POST">
				
					<!-- BOF Thumbnail Section. -->
					<div class="metabox-holder">
						<div class="postbox">
							
							<h3><?php _e('Thumbnails Section', SRP_TRANSLATION_ID);?></h3>
							
							<!-- BOF Left Box. -->
							<div id="srp-admin-leftcontent">
								<p>
									<?php  _e('<p>Thumbnails are generated through an automatic process which tries to retrieve each post featured image;
										in case this step fails or the thumbnail is unavailable, the plugin will try to fetch the first image from the post content. 
										Adaptive resize is applied to display images at their best quality without stretching.
										If no images are available, a default thumbnail is displayed.
										Here are the basic options for this section:</p>
										<dl><dt><strong>Thumbnail Width</strong>:</dt>
											<dd>Set the preferred thumbnail width.<br /><i>Note: This setting could be overrided by a specific widget value.</i></dd>
											<dt><strong>Thumbnail Height</strong>:</dt>
											<dd>Set the preferred thumbnail height.<br /><i>Note: This setting could be overrided by a specific widget value.</i></dd>
											<dt><strong>Link to Post</strong>:</dt>
											<dd>Check this option if you want the thumbnails to be linked to post pages.</dd>
											<dt><strong>Default Thumbnail URL</strong>:</dt>
											<dd>Paste here the full URL of a custom image for the thumbnail placeholder. This will be displayed when no post images are available.
										</dd></dl>', SRP_TRANSLATION_ID); ?>
								</p>
							</div>
							<!-- EOF Left Box. -->
							
							<!-- BOF Right Box. -->
							<div id="srp-admin-rightcontent">
								<ul>
									
									<!--BOF Thumbnail Size -->
									<li>
										<label for="srp_thumbnail_width"><?php _e('Set thumbnail width (*)', SRP_TRANSLATION_ID); ?></label>
										<input type="text" id="srp_thumbnail_width" name="srp_thumbnail_width" value="<?php echo stripslashes($srp_current_options['srp_thumbnail_width']); ?>" size="8" /> px
										
										<label for="srp-thumbnail-height"><?php _e('Set thumbnail height (*)', SRP_TRANSLATION_ID); ?></label>
										<input type="text" id="srp_thumbnail_height" name="srp_thumbnail_height" value="<?php echo stripslashes($srp_current_options['srp_thumbnail_height']); ?>" size="8" /> px
									</li>
									<!--EOF Thumbnail Size -->
									
									<!--BOF Thumbnail Link Mode -->
									<li>
										<input type="checkbox" id="srp_thumbnail_link" name="srp_thumbnail_link" value="yes" <?php checked($srp_current_options['srp_thumbnail_link'], 'yes'); ?> />
										<span class="srp-smalltext"><?php _e('Link thumbnail to post', SRP_TRANSLATION_ID); ?></span>
									</li>
									<!--EOF Thumbnail Link Mode -->
									
									<!--BOF Thumbnail Custom URL -->
									<li>
										<label for="srp_thumbnail_url"><?php _e('Insert here your default thumbnail image URL (leave the default value for SRP placeholder) Default size: 100px x 100px', SRP_TRANSLATION_ID); ?></label>
										<input type="text" id="srp_thumbnail_url" name="srp_thumbnail_url" value="<?php echo stripslashes($srp_current_options['srp_thumbnail_url']); ?>" size="120" />
									</li>
									<!--EOF Thumbnail Custom URL -->
								</ul>
							</div>
							<!-- EOF Right Box. -->
							
							<div class="clearer"></div>
							
						</div><!-- EOF postbox. -->
					</div><!-- EOF metabox-holder. -->
					<!-- EOF Thumbnail section. -->
					
					<!-- BOF Posts Content Section. -->
					<div class="metabox-holder">
						<div class="postbox">

						<h3><?php _e('Posts Content Section', SRP_TRANSLATION_ID); ?></h3>
						
						<!-- BOF Left Box. -->
						<div id="srp-admin-leftcontent">
							<p>
							<?php _e('By default every post is displayed with its excerpt. If this is unavaiable, the plugin will try to generate an excerpt from the plain post content data.
								Here are the basic options:
								<dl>
									<dt><strong>Max Text Size to Display</strong>:</dt>
									<dd>Insert here after how many characters/words the post excerpt should be \'cut\'.</dd>
									<dt><strong>Excerpt Allowed Tags</strong>:</dt>
									<dd>Insert here a list of tags you\'d like to keep displayed inside the generated text output. The list must be written without quotes, dots or commas.<br />Example: &lt;a&gt;&lt;img&gt;&lt;i&gt;</dd>
									<dt><strong>Max Title Size to Display</strong>:</dt>
									<dd>Insert here after how many characters/words the post title should be \'cut\'.</dd>
									<dt><strong>Set String Break</strong>:</dt>
									<dd>Insert here the suffix string to be appended after the post excerpt and if it should be linked to the post page.</dd>
									<dt><strong>Set Image String Break</strong>:</dt>
									<dd>Insert here the absolute URL of a custom image to use as graphic stringbreak. This will override the textual one.</dd>
									<dt><strong>Format Post Date.</strong>:</dt>
									<dd>Insert here how to format the post date field. Check the legend for each parameter. For further options see the <a href=\'http://php.net/manual/en/function.date.php\'>PHP documentation</a> online.</dd>
									<dt><strong>Post Offset</strong></dt>
									<dd>Check this field if you want to hide from the recent posts the current post displayed (usually on single post view).</dd>
									<dt><strong>Use Category Title</strong></dt>
									<dd>Check this field if you want to display the category name as widget title instead of the custom one. It will be linked to the category archive page.</dd>
									<dt><strong>No Posts Available Text</strong></dt>
									<dd>This is the text that will be displayed when no posts are available.</dd>
								</dl>', SRP_TRANSLATION_ID); ?>
							</p>
						</div>
						<!-- EOF Left Box. -->
						
						<!-- BOF Right Box -->
						<div id="srp-admin-rightcontent">
							<ul>
								<!-- BOF Excerpt length option. -->
								<li>
									<label for="srp_excerpt_length"><?php _e('Max text size to display (*)', SRP_TRANSLATION_ID); ?></label>
									<span class="srp-smalltext"><?php _e('Cut post text after: ', SRP_TRANSLATION_ID); ?></span>
									<input type="text" name="srp_excerpt_length" id="srp_excerpt_length" value="<?php echo stripslashes($srp_current_options['srp_excerpt_length']); ?>" size="10" />
									<input type="radio" name="srp_excerpt_length_mode" id="srp_excerpt_length_mode" value="words" <?php checked($srp_current_options['srp_excerpt_length_mode'], 'words'); ?> /> <span class="srp-smalltext radiotext"><?php _e('Words', SRP_TRANSLATION_ID); ?></span>
									<input type="radio" name="srp_excerpt_length_mode" id="srp_excerpt_length_mode" value="chars" <?php checked($srp_current_options['srp_excerpt_length_mode'], 'chars'); ?> /> <span class="srp-smalltext radiotext"><?php _e('Characters', SRP_TRANSLATION_ID); ?></span>
									<input type="radio" name="srp_excerpt_length_mode" id="srp_excerpt_length_mode" value="fullexcerpt" <?php checked($srp_current_options['srp_excerpt_length_mode'], 'fullexcerpt'); ?> /> <span class="srp-smalltext radiotext"><?php _e('Use full excerpt', SRP_TRANSLATION_ID); ?></span>
								</li>
								<!-- EOF Excerpt length option. -->
								
								<!-- BOF Excerpt allowed tags. -->
								<li>
									<label for="srp_excerpt_allowed_tags"><?php _e('Excerpt Allowed Tags? (Leave blank for clean text without any markup)', SRP_TRANSLATION_ID); ?></label>
									<input type="text" name="srp_excerpt_allowed_tags" id="srp_excerpt_allowed_tags" value="<?php echo htmlspecialchars_decode(stripslashes($srp_current_options['srp_excerpt_allowed_tags'])); ?>" size="40" />
								</li>
								<!-- EOF Excerpt allowed tags. -->
								
								<!-- BOF Title length option. -->
								<li>
									<label for="srp_title_length"><?php _e('Max title size to display (*)', SRP_TRANSLATION_ID); ?></label>
									<span class="srp-smalltext"><?php _e('Cut title text after: ', SRP_TRANSLATION_ID); ?></span>
									<input type="text" name="srp_title_length" id="srp_title_length" value="<?php echo stripslashes($srp_current_options['srp_title_length']); ?>" size="10" />
									<input type="radio" name="srp_title_length_mode" id="srp_title_length_mode" value="words" <?php checked($srp_current_options['srp_title_length_mode'], 'words'); ?> /> <span class="srp-smalltext radiotext"><?php _e('Words', SRP_TRANSLATION_ID); ?></span>
									<input type="radio" name="srp_title_length_mode" id="srp_title_length_mode" value="chars" <?php checked($srp_current_options['srp_title_length_mode'], 'chars'); ?> /> <span class="srp-smalltext radiotext"><?php _e('Characters', SRP_TRANSLATION_ID); ?></span>
									<input type="radio" name="srp_title_length_mode" id="srp_title_length_mode" value="fulltitle" <?php checked($srp_current_options['srp_title_length_mode'], 'fulltitle'); ?> /> <span class="srp-smalltext radiotext"><?php _e('Use full title', SRP_TRANSLATION_ID); ?></span>
								</li>
								<!-- EOF Title length option. -->
								
								<!-- BOF String Break Option. -->
								<li>
									<label for="srp_string_break"><?php _e('Set String Break', SRP_TRANSLATION_ID); ?></label>
									<input type="text" name="srp_string_break" id="srp_string_break" value="<?php echo stripslashes($srp_current_options['srp_string_break']); ?>" size="40" />
									<input type="checkbox" id="srp_string_break_link" name="srp_string_break_link" value="yes" <?php checked($srp_current_options['srp_string_break_link'], 'yes'); ?> />
									<span class="srp-smalltext"><?php _e('Link to post', SRP_TRANSLATION_ID); ?></span>
								</li>
								<!-- EOF String Break Option. -->
								
								<!-- BOF Image String Break. -->
								<li>
									<label for="srp_image_string_break"><?php _e('Set Image String Break (Fill in with the absolute URL of your button image)', SRP_TRANSLATION_ID); ?></label>
									<input type="text" name="srp_image_string_break" id="srp_image_string_break" value="<?php echo stripslashes($srp_current_options['srp_image_string_break']); ?>" size="120" />
								</li>
								<!-- EOF Image String Break. -->
								
								<!-- BOF Date Content option. --->
								<li>
									<label for="srp_date_content"><?php _e('Format post date (*)', SRP_TRANSLATION_ID); ?></label>
									<input type="text" name="srp_date_content" id="srp_date_content" value="<?php echo stripslashes($srp_current_options['srp_date_content']); ?>" size="30" />
									<span class="srp-smalltext"><?php _e('(F = Month name | j = Day of the month | S = ordinal suffix for the day of the month | Y = Year)', SRP_TRANSLATION_ID); ?></span>
								</li>
								<!-- EOF Date Content option. -->
								
								<!-- BOF Post Offset option. --->
								<li>
									<label for="srp_post_offset"><?php _e('Post Offset', SRP_TRANSLATION_ID); ?></label>
									<input type="checkbox" id="srp_post_offset" name="srp_post_offset" value="yes" <?php checked($srp_current_options['srp_post_offset'], 'yes'); ?> />
									<span class="srp-smalltext"><?php _e('Check this box if you want to hide the current post from SRP list when in single post view.', SRP_TRANSLATION_ID); ?></span>
								</li>
								<!-- EOF Post Offset option. --->
								
								<!-- BOF Category Title option. --->
								<li>
									<label for="srp_category_title"><?php _e('Use Category Title?', SRP_TRANSLATION_ID); ?></label>
									<input type="checkbox" id="srp_category_title" name="srp_category_title" value="yes" <?php checked($srp_current_options['srp_category_title'], 'yes'); ?> />
									<span class="srp-smalltext"><?php _e('Check this box if you want to use the category title instead of the custom one when category filter is on.', SRP_TRANSLATION_ID); ?></span>
								</li>
								<!-- EOF Category Title option. --->
								
								<!-- BOF No posts message. --->
								<li>
									<label for="srp_noposts_message"><?php _e('No posts available text', SRP_TRANSLATION_ID); ?></label>
									<input type="text" name="srp_noposts_message" id="srp_noposts_message" value="<?php echo stripslashes($srp_current_options['srp_noposts_message']); ?>" size="40" />
								</li>
								<!-- EOF No posts message. --->
							</ul>
						</div>
						<!-- EOF Right Box. -->
						
						<div class="clearer"></div>
					</div> <!-- EOF postbox. -->
				</div> <!--EOF metabox-holder. -->
				<!-- BOF Posts Content Section. -->
				
				<!-- BOF Thumbnail Section. -->
					<div class="metabox-holder">
						<div class="postbox">
							
							<h3><?php _e('Appearance Section', SRP_TRANSLATION_ID);?></h3>
							
							<!-- BOF Left Box. -->
							<div id="srp-admin-leftcontent">
								<p><?php  _e('This is the stylesheet that handles the widget visualization on your theme. Basic properties are applied. Feel free to modify it to suite your needs.', SRP_TRANSLATION_ID); ?></p>
							</div>"
							<!-- EOF Left Box. -->
							
							<!-- BOF Right Box. -->
							<div id="srp-admin-rightcontent">
								<ul>
									
									<!--BOF Thumbnail Size -->
									<li>
										<label for="srp_themecss"><?php _e('Theme CSS', SRP_TRANSLATION_ID); ?></label>
										<textarea id="srp_themecss" name="srp_themecss" rows="20" cols="80" /><?php echo stripslashes($srp_current_options['srp_themecss']); ?></textarea>
									</li>
									<!--EOF Thumbnail Size -->
								</ul>
							</div>
							<!-- EOF Right Box. -->
							
							<div class="clearer"></div>
							
						</div><!-- EOF postbox. -->
					</div><!-- EOF metabox-holder. -->
					<!-- EOF Thumbnail section. -->
				
				<input type="submit" name="submit" class="button-primary" value="<?php _e('Save Options', SRP_TRANSLATION_ID); ?>" />
			</form> <!--EOF Form. -->
		</div> <!-- EOF srp_adm_container -->
	</div> <!-- EOF Wrap. -->
<?php
}

// Main function to update form option data.
function srp_update_data($data, $srp_current_options) {

	// Checking if form has been submitted.
	if (isset($_POST['submit'])) {
		
		// Remove the "submit" $_POST entry.
		unset($data['submit']);
		
		// Handling null values for checkboxes - 1
		if (!isset($data['srp_thumbnail_link'])) {
			$data['srp_thumbnail_link'] = 'no';
		}
		
		// Handling null values for checkboxes - 2
		if (!isset($data['srp_string_break_link'])) {
			$data['srp_string_break_link'] = 'no';
		}
		
		// Handling null values for checkboxes - 3
		if (!isset($data['srp_post_offset'])) {
			$data['srp_post_offset'] = 'no';
		}
		
		if (!isset($data["srp_category_title"])) {
			$data['srp_category_title'] = 'no';
		}
		
		// Validating text fields.		
		foreach ($data as $k => $v) {
			
			// Assigning previous default value if field is empty. String break field excluded.
			if ((empty($v)) && ( ($k != "srp_string_break") && ($k != "srp_noposts_message") && ($k != "srp_excerpt_allowed_tags") && ($k != "srp_image_string_break"))) {
				$data[$k] = $srp_current_options[$k];
			}
		}
		
		// Handling special html tags in "srp_excerpt_allowed_tags".
		$data['srp_excerpt_allowed_tags'] = htmlspecialchars($data['srp_excerpt_allowed_tags'], ENT_QUOTES);
		
		// Updating WP Option with new $_POST data.
		update_option('srp_plugin_options', $data);
		
		// Displaying "save settings" message.
		echo "<div id=\"message\" class=\"updated\"><p><strong>" . __('Settings Saved', SRP_TRANSLATION_ID) . "</strong></p></div>";
	}
}
