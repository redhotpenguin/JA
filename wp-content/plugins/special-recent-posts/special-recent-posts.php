<?php
/*
Plugin Name: Special Recent Posts
Plugin URI: http://www.lucagrandicelli.com/special-recent-posts-plugin-for-wordpress
Description: A very simple but powerful plugin/widget to display your latest posts with thumbnails. You can switch between different display modes and customize several options to suite your needs. <strong>To get started:</strong> 1) Click the "Activate" link to the left of this description, 2) Go to Special Recent Post <a href='options-general.php?page=special-recent-posts/lib/lib-admin.php'>setting page</a> and configure the plugin, 3) Go to Widget page and drag the 'Special Recent Posts' widget onto your sidebar and configure its settings, 4) If you wish to use PHP code, use the <code>special_recent_posts()</code> function provided. If you prefer shortcodes, use the <code>[srp]</code> shortcode inside any of your posts/pages. Check the <a href='http://wordpress.org/extend/plugins/special-recent-posts/installation/'>documentation</a> or readme.txt file for further details. 5) Enjoy.
Version: 1.9
Author: Luca Grandicelli
Author URI: http://www.lucagrandicelli.com
License: GPLv3 or later

Copyright (C) 2011  Luca Grandicelli

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
| ---------------------------------------------
| Credits:
| Image manipulation is hadled by PHP Thumbnailer
| http://phpthumb.gxdlabs.com/
| ---------------------------------------------
*/

/*
| ---------------------------------------------
| GLOBAL DECLARATIONS
| In this section we define the enviroment
| basic constants and global paths.
| ---------------------------------------------
*/

define('SRP_PLUGIN_URL'       , plugin_dir_url( __FILE__ ));              // Defining plugin url path.
define('SRP_PLUGIN_MAINFILE'  , __FILE__);                                // Defining plugin main filename.
define('SRP_PLUGIN_VERSION'   , '1.9');                                   // Defining plugin version.
define('SRP_REQUIRED_PHPVER'  , '5.0.0');                                 // Defining required PHP version.
define('SRP_TRANSLATION_ID'   , 'srp-lang');                              // Defining gettext translation ID.
define('SRP_CLASS_FOLDER'     , 'classes/');                              // Defining path for main plugin classes.
define('SRP_CSS_FOLDER'       , 'css/');                                  // Defining path for CSS stylesheets.
define('SRP_JS_FOLDER'        , 'js/');                                   // Defining path for javascript scripts.
define('SRP_IMAGES_FOLDER'    , 'images/');                               // Defining path for images.
define('SRP_LIB_FOLDER'       , 'lib/');                                  // Defining path for external libraries.
define('SRP_LANG_FOLDER'      , 'lang/');                                 // Defining path for language packs.
define('SRP_ICONS_FOLDER'     , SRP_IMAGES_FOLDER . 'icons/');            // Defining path for icons images.
define('SRP_ADMIN_CSS'        , SRP_CSS_FOLDER    . 'css-admin.css');     // Defining path for administration stylesheet.
define('SRP_WIDGET_CSS'       , SRP_CSS_FOLDER    . 'css-widget.css');    // Defining path for administration widget stylesheet.
define('SRP_THEME_CSS'        , SRP_CSS_FOLDER    . 'css-theme.css');     // Defining path for theme widget stylesheet.
define('SRP_IEFIX_CSS'        , SRP_CSS_FOLDER    . 'css-ie-fix.css');    // Defining path for ie browsers fix css.
define('SRP_JS_INIT'          , SRP_JS_FOLDER     . 'srp-init.js');       // Defining path for custom js init script.
define('SRP_DEFAULT_THUMB'    , SRP_ICONS_FOLDER  . 'default-thumb.gif'); // Defining path for default no-image thumbnail placeholder.

/*
| ---------------------------------------------
| INCLUDING PHP THUMB SCRIPT
| With this script thumbnail resize is easier.
| ---------------------------------------------
*/

define('SRP_THUMBPHP_SCRIPT'  , SRP_LIB_FOLDER . 'phpthumb/ThumbLib.inc.php'); // Defining PHPThumb library.
define('SRP_THUMBPHP_HANDLER' , SRP_LIB_FOLDER . 'phpimage.php');              // Defining PHPThumb image handler.

/*
| ---------------------------------------------
| GLOBAL INCLUDES
| In this section we include all the needed
| files for the plugin to work.
| ---------------------------------------------
*/

require_once('config.php');                            // Including main config file.
require_once(SRP_CLASS_FOLDER  . 'class-main.php');    // Including main plugin class.
require_once(SRP_CLASS_FOLDER  . 'class-widgets.php'); // Including widgets class.
require_once(SRP_LIB_FOLDER    . 'lib-admin.php');     // Including plugin admin file.

/*
| -------------------------------------------------------------
| External function to call plugin from PHP inline code.
| Check documentation on # for further configuration settings.
| -------------------------------------------------------------
*/

function special_recent_posts($args = array()) {
	
	// Creating an instance of Special Posts Class with widget args passed in manual mode.
	$srp = new SpecialRecentPosts($args);
	
	// Display Posts.
	$srp->displayPosts(NULL, 'print');
}

function srp_shortcode($atts) {

	// Including external widget values.
	global $srp_default_widget_values;
	
	// If shortcode comes without parameters, make $atts value an array.
	if (!is_array($atts)) {
		$atts = array();
	}
	
	// Assembling default widget options with available shortcode options.
	extract(shortcode_atts($srp_default_widget_values, $atts));
	
	// Creating an instance of Special Posts Class with widget args passed in manual mode.
	$srp = new SpecialRecentPosts($atts);
	
	// Display Posts.
	return $srp->displayPosts(NULL, 'return');
}

/*
| ---------------------------------------------
| PLUGIN HOOKS & ACTIONS
| Listing plugin hooks and actions.
| ---------------------------------------------
*/

register_activation_hook(__FILE__    , array('SpecialRecentPosts', 'install_plugin'));   // Registering plugin activation hook.
register_uninstall_hook( __FILE__    , array('SpecialRecentPosts', 'uninstall_plugin')); // Registering plugin deactivation hook.
add_action('widgets_init'            , 'srp_install_widgets');                           // Defining actions on plugin init.
add_action('admin_init'              , 'srp_admin_init');                                // Defining actions on admin page init.
add_action('admin_menu'              , 'srp_admin_setup');                               // Defining actions for admin page setup.
add_action('wp_head'                 , 'srp_theme_css');                                 // Defining main theme CSS.
add_action('init'                    , 'srp_plugin_init');                               // Registering plugin init method.
add_shortcode('srp'                  , 'srp_shortcode' );                                // Registering SRP Shortcode.
