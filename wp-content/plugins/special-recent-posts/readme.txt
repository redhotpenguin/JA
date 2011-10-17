=== Special Recent Posts ===
Contributors: lgrandicelli
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PZD4ACBRFR9GS
Tags: recent, post, wordpress, plugin, thumbnails, widget, recent posts
Requires at least: 3.0
Tested up to: 3.1.3
Stable tag: 1.9
License: GPLv3 or later


Special Recent Posts (SRP) is a simple but very powerful plugin/widget for WordPress which displays your recent posts with thumbnails.

== Description ==

<p>Special Recent Posts (SRP) is a simple but very powerful plugin/widget for WordPress which displays your recent posts with thumbnails.
You can drag multiple widget instances and configure each one with different settings.
You can also use custom PHP code to insert the widget in any part of your theme.</p>

<p>Special Recent Posts is fully configurable and lets you administrate almost everything about your blog’s recent posts.</p>

<strong>Special features</strong>:
<ul>
	<li>Thumbnails automatic selection and generation</li>
	<li>Thumbnails adaptive resize with GD libraries support</li>
	<li>Thumbnails Rotation</li>
	<li>Thumbnails custom sizes option for every widget instance</li>
	<li>Posts/Pages display option</li>
	<li>Posts/Pages inclusion/exclusion</li>
	<li>Custom Post Types filtering</li>
	<li>Post Status Filtering</li>
	<li>Title and content trimming</li>
	<li>Custom Image StringBreak</li>
	<li>Content Tags filtering</li>
	<li>Random mode visualization</li>
	<li>Posts date visualization</li>
	<li>Category filtering</li>
	<li>Current post filtering</li>
	<li>Configurable custom CSS</li>
	<li>Multiple widgets configurations</li>
	<li>Specific PHP function call for theme customization</li>
	<li>Shortcodes Support</li>
</ul>

<strong>Plugin's homepage</strong><br />
http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress/

<strong>Credits</strong>
Thumbnail generation is handled by the brilliant PHP Thumb Class
http://phpthumb.gxdlabs.com/

== Installation ==

The automatic plugin installer should work for most people. Manual installation is easy and takes fewer than five minutes.

1. Download the plugin, unpack it and upload the '<em>specialrecentposts</em>' folder to your wp-content/plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -&gt; Special Recent Posts to configure the basic options.
4. On the widgets panel, drag the Special Recent Posts widget onto one of your sidebars and configure its specific settings.
5. You're done. Enjoy.

If you wish to use the Special Recent Posts in another part of your theme which is not widget-handled, you can put the following snippet:

`
<?php
	if(function_exists('special_recent_posts')) {
		special_recent_posts($args);
	}
?>
`
where $args is an array of the following options:

`
// Post Type (default: 'posts')
srp_post_type => post/page

// Post Status ('publish|private|inherit|pending|future|draft|trash)
srp_post_status_option => text

// Custom Post Type (text)
srp_custom_post_type_option => text

// The Widget Title (default: 'Special Recent Posts')
srp_widget_title  => text

// Hide Widget Title?
'srp_widget_title_hide' => no|yes

// Display thumbnails? (default: yes)
srp_thumbnail_option => yes|no

// Thumbnail Width
srp_thumbnail_wdg_width => numeric

// Thumbnail Height
srp_thumbnail_wdg_height => numeric

// Thumbnail Rotation
srp_thumbnail_rotation => 'no|cw|ccw',

// How many posts to display. (default: 5)
srp_number_post_option => numeric

// Set the max size of title text
srp_wdg_title_length => numeric

// Set the title text cut mode
srp_wdg_title_length_mode => chars|words|fulltitle

// Set the max size of post text
srp_wdg_excerpt_length => numeric

// Set the post text cut mode
srp_wdg_excerpt_length_mode   => chars|words|fullexcerpt

// Select display order. (default: DESC)
srp_order_post_option => ASC|DESC

// Want to randomize posts order? (default: no)
srp_orderby_post_option => rand|no

// Post Offset (to skip current displayed post from visualization)
srp_post_offset_option => yes|no

// Global Post Offset ( to skip an arbitrary number of posts from the beginning)
srp_post_global_offset_option => numeric

// Insert the category ID to filter result posts. (default: -1|'' = none)
srp_filter_cat_option => numeric

// Choose wheter to display only title or both title and excerpt. (default: titleexcerpt = displays both title and text 5)
srp_content_post_option => titleonly|titleexcerpt

// Show post date? (default: yes)
srp_post_date_option => yes|no

// Posts/pages to include?
srp_include_option => numeric

// Posts/pages to exclude?
srp_exclude_option => numeric

// Add nofollow option.
srp_add_nofollow_option => yes|no

`

Example:
Show last 5 posts in random order without thumbnail. (PHP mode)
`
<?php
// Defining widget options.
$args = array(
	'srp_number_post_option'  => 5,
	'srp_orderby_post_option' => 'rand',
	'srp_thumbnail_option'    => 'no'
);


// Function call.
special_recent_posts($args);
?>
`

If you wish to use a shortcode, put the following inside any of your post/pages:
`[srp]`

Shortcodes parameters names are the same of direct PHP call ones, but you have to put them with the '=' sign instead of the '=>' arrow.
String values must be enclosed within single/double quotes.

Example:
`[srp srp_number_post_option='5' srp_orderby_post_option='rand' srp_thumbnail_option='no']`

== Changelog ==
= 1.9 =
* Added Max Title text size option to single widget instance.
* Added Max Post text size option to single widget instance.

= 1.8 =
* Added Post Status support.
* Added Custom Post Type support.
* Added a new widget option for 'rel=nofollow' links.
* Added tabbed navigation on the top of the widget. Basic and Advanced.

= 1.7.2 =
* Minor bugs fixed.

= 1.7.1 =
* Added a "no-content" option on widget display mode. This will display only thumbnails, without titles, dates and excerpts.
* Widget drop-down category filtering has been replaced by a text field. Now you need to specify categories Ids separated by a comma.
* Fixed some IE7 CSS compatibility issue.
* Fixed bug in direct PHP call. (PHP error: argument #1 or #2 is not an array).

= 1.7 =
* Added shortcode support. The SRP shortcode must be inserted into posts/pages with the special code [srp]. Additional parameters are the same used for direct PHP calls. See readme.txt file for examples.
* Added widget option for thumbnail rotation. For very particular setups.
* Added a global post offset option, to skip an arbitrary number of posts from the beginning of the visualization.
* Added option to display/hide Widget Title.
* Fixed issue that lets included IDs override max number of posts option.
* Minor bugs fixed.

= 1.6.4 =
* Fixed widget title. Now when dragging a new SRP widget, the title is styled by the theme's default CSS, to prevent issues where the SRP title looked different from all other widget ones.
When using PHP call, the title is styled the old way, inside the plugin options panel.
* Fixed bug: Fixed issue that prevented puntuaction from displaying when in 'words cut' mode.

= 1.6.3 =
* FIX: Minor bug fixed.

= 1.6.2 =
* FIX: Another couple of security issues fixed.

= 1.6.1 =
* FIX: Important security issue fixed.

= 1.6 =
* Added a new option to include specific posts/pages in widgets.
* Fixed issue: Now thumbnail images paths are dinamically generated from the WP DB options, in case the default "uploads" folder is changed.

= 1.5.1 =
* Fixed some compatibility issue.

= 1.5 =
* Added a new general option which allows to use a button image as stringbreak.
* Added a new general option which allows to display a linked category title instead of the widget custom one when category filter is on.
* Added a new general option which allows particular html tags to be displayed in the generated excerpt text.
* Added an update method which automatically updates db options with newer plugin versions.
* Fixed Bug: Removed unwanted slashes when saving urls in general options
* Fixed Bug: Fixed some compatibility issue with qTranslate.
* Minor bugs fixed

= 1.4 =
* Fixed issue in the deactivation hook. Now plugin settings will be destroyed only when uninstalling and not when deactivating.
* Added custom thumbnails sizes. Now every widget instance has its own thumbnail sizes which will override the default ones.

= 1.3 =
* Added posts/pages exclusion from display view.

= 1.2 =
* Added an option to select whether to display posts or pages.
* Added a simple sanitize function to clear all outputs, avoiding unwanted slashes/backslashes or string breaks.

= 1.1 =
* Fixed issue that displays image captions inside generated excerpt.
* Minor bugs fixed.

= 1.0 = 
* Initial release

== Frequently Asked Questions ==

= Plugin works but i see no thumbnails =

This issue might be caused by several problems. Check the following list.
<ol>
<li>Thumbnails are rendered using the PHP GD libraries. These should be enabled on your server. Do a phpinfo() on your host to check if they're installed properly. Contact your hosting support to know how to enable them.</li>
<li>Another problem could be that you're hosting the plugin on a MS Windows based machine. This will probably change the encoding data inside the files and could lead to several malfunctions. Better to host on a Unix/Linux based enviroment.</li>
<li>External images are not allowed. This means that if you're trying to generate a thumbnail from an image hosted on a different domain, it won't work.</li>
</ol>

== Requirements ==

In order to work, Special Recent Posts plugin needs the following settings:

1. PHP version 5+
2. GD libraries installed and enabled on your server.
