<?php
/**
 * @package Techotronic
 * @subpackage All in one Favicon
 *
 * Plugin Name: All in one Favicon
 * Plugin URI: http://www.techotronic.de/plugins/all-in-one-favicon/
 * Description: All in one Favicon management. Easily add a Favicon to your site and the WordPress admin pages. Complete with upload functionality. Supports all three Favicon types (ico,png,gif)
 * Version: 3.1
 * Author: Arne Franken
 * Author URI: http://www.techotronic.de/
 * License: GPL
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

// define constants
define('AIOFAVICON_VERSION', '3.1');

if (!defined('AIOFAVICON_PLUGIN_BASENAME')) {
    define('AIOFAVICON_PLUGIN_BASENAME', plugin_basename(__FILE__));
}
if (!defined('AIOFAVICON_PLUGIN_NAME')) {
    define('AIOFAVICON_PLUGIN_NAME', trim(dirname(AIOFAVICON_PLUGIN_BASENAME), '/'));
}
if (!defined('AIOFAVICON_NAME')) {
    define('AIOFAVICON_NAME', 'All in one Favicon');
}
if (!defined('AIOFAVICON_TEXTDOMAIN')) {
    define('AIOFAVICON_TEXTDOMAIN', 'aio-favicon');
}
if (!defined('AIOFAVICON_PLUGIN_DIR')) {
    define('AIOFAVICON_PLUGIN_DIR', dirname(__FILE__));
}
if (!defined('AIOFAVICON_PLUGIN_URL')) {
    define('AIOFAVICON_PLUGIN_URL', WP_PLUGIN_URL . '/' . AIOFAVICON_PLUGIN_NAME);
}
if (!defined('AIOFAVICON_PLUGIN_LOCALIZATION_DIR')) {
    define('AIOFAVICON_PLUGIN_LOCALIZATION_DIR', AIOFAVICON_PLUGIN_DIR . '/localization');
}
if (!defined('AIOFAVICON_SETTINGSNAME')) {
    define('AIOFAVICON_SETTINGSNAME', 'aio-favicon_settings');
}
if (!defined('AIOFAVICON_LATESTDONATEURL')) {
    define('AIOFAVICON_LATESTDONATEURL', 'http://favicon.techotronic.de/latest-donations.php');
}
if (!defined('AIOFAVICON_TOPDONATEURL')) {
    define('AIOFAVICON_TOPDONATEURL', 'http://favicon.techotronic.de/top-donations.php');
}
//define constants

class AllInOneFavicon {

    var $aioFaviconSettings = array();

    /**
     * Plugin initialization
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function allInOneFavicon(){
    function allInOneFavicon(){

        load_plugin_textdomain(AIOFAVICON_TEXTDOMAIN, false, '/all-in-one-favicon/localization/');

        // add options page
        add_action('admin_menu', array(& $this, 'registerAdminMenu'));

        add_action('admin_post_aioFaviconDeleteSettings', array(& $this, 'aioFaviconDeleteSettings'));
        add_action('admin_post_aioFaviconUpdateSettings', array(& $this, 'aioFaviconUpdateSettings'));

        // Create the settings array by merging the user's settings and the defaults
        $usersettings = (array) get_option(AIOFAVICON_SETTINGSNAME);
        $defaultArray = $this->aioFaviconDefaultSettings();
        $this->aioFaviconSettings = wp_parse_args($usersettings, $defaultArray);

        if(!is_admin()){
            require_once 'includes/header-blog.php';
            add_action( 'wp_head', 'aioFaviconRenderBlogHeader' );
        } else if (is_admin()){
            require_once 'includes/header-admin.php';
            add_action( 'admin_head', 'aioFaviconRenderAdminHeader' );
        }

        // only register scripts and styles for this plugin page since JavaScript overwrites default WordPress behaviour
        if (isset($_GET['page']) && $_GET['page'] == 'all-in-one-favicon/all-in-one-favicon.php') {
            add_action('admin_print_scripts', array(& $this, 'registerAdminScripts'));
            add_action('admin_print_styles', array(& $this, 'registerAdminStyles'));
        }

                //only add link to meta box
        if(isset($this->aioFaviconSettings['removeLinkFromMetaBox']) && !$this->aioFaviconSettings['removeLinkFromMetaBox']){
            add_action('wp_meta',array(& $this, 'renderMetaLink'));
        }
    }

    // allInOneFavicon()

    /**
     * Renders plugin link in Meta widget
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function renderMetaLink() {
    function renderMetaLink() { ?>
        <li><?php _e('Using',AIOFAVICON_TEXTDOMAIN);?> <a href="http://www.techotronic.de/plugins/all-in-one-favicon/" title="<?php echo AIOFAVICON_NAME ?>"><?php echo AIOFAVICON_NAME ?></a></li>
    <?php }

    // renderMetaLink()

    /**
     * Register Settings page JavaScript files
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function registerAdminScripts() {
    function registerAdminScripts() {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_register_script('aioFaviconUpload', AIOFAVICON_PLUGIN_URL .'/js/backend.js', array('jquery','media-upload','thickbox'));
        wp_enqueue_script('aioFaviconUpload');
    }

    // aioFaviconAdminScripts()

    /**
     * Register Settings page CSS styles
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function registerAdminStyles() {
    function registerAdminStyles() {
        wp_enqueue_style('thickbox');
    }

    // aioFaviconAdminStyles()

    /**
     * Render Settings page
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function renderSettingsPage() {
    function renderSettingsPage() {
        include_once 'includes/settings-page.php';
    }

    // renderSettingsPage()

    /**
     * Register the settings page in wordpress
     *
     * @since 1.0
     * @access private
     * @author Arne Franken
     */
    //private function registerSettingsPage() {
    function registerSettingsPage() {
        if (current_user_can('manage_options')) {
            add_options_page(AIOFAVICON_NAME, AIOFAVICON_NAME, 'manage_options', AIOFAVICON_PLUGIN_BASENAME, array(& $this, 'renderSettingsPage'));
        }
    }

    // registerSettingsPage()

    /**
     * Registers the Settings Page in the Admin Menu
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function registerAdminMenu() {
    function registerAdminMenu() {
        if (function_exists('add_management_page') && current_user_can('manage_options')) {

            // update, uninstall message
            if (strpos($_SERVER['REQUEST_URI'], 'all-in-one-favicon.php') && isset($_GET['aioFaviconUpdateSettings'])) {
                $return_message = sprintf(__('Successfully updated %1$s settings.', AIOFAVICON_TEXTDOMAIN), AIOFAVICON_NAME);
            } elseif (strpos($_SERVER['REQUEST_URI'], 'all-in-one-favicon.php') && isset($_GET['aioFaviconDeleteSettings'])) {
                $return_message = sprintf(__('%1$s settings were successfully deleted.', AIOFAVICON_TEXTDOMAIN), AIOFAVICON_NAME);
            } else {
                $return_message = '';
            }
        }
        $this->registerAdminNotice($return_message);

        $this->registerSettingsPage();
    }

    // registerAdminMenu()

    /**
     * Registers Admin Notices
     *
     * @since 1.0
     * @access private
     * @author Arne Franken
     */
    //private function registerAdminNotice($notice) {
    function registerAdminNotice($notice) {
        if ($notice != '') {
            $message = '<div class="updated fade"><p>' . $notice . '</p></div>';
            add_action('admin_notices', create_function('', "echo '$message';"));
        }
    }

    // registerAdminNotice()

    /**
     * Default array of All In One Favicon settings
     *
     * @since 3.0
     * @access private
     * @author Arne Franken
     */
    //private function aioFaviconDefaultSettings() {
    function aioFaviconDefaultSettings() {

        // Create and return array of default settings
        return array(
            'aioFaviconVersion' => AIOFAVICON_VERSION,
            'debugMode' => false,
            'removeLinkFromMetaBox' => false
        );
    }

    // aioFaviconDefaultSettings()

    /**
     * Update jQuery Colorbox settings wrapper
     *
     * handles checks and redirect
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function aioFaviconUpdateSettings() {
    function aioFaviconUpdateSettings() {

        if (!current_user_can('manage_options'))
            wp_die(__('Did not update settings, you do not have the necessary rights.', AIOFAVICON_TEXTDOMAIN));

        //cross check the given referer for nonce set in settings form
        check_admin_referer('aio-favicon-settings-form');
        //get settings from plugins admin page
        $this->aioFaviconSettings = $_POST[AIOFAVICON_SETTINGSNAME];
        //have to add jQueryColorboxVersion here because it is not included in the HTML form
        $this->aioFaviconSettings['aioFaviconVersion'] = AIOFAVICON_VERSION;
        $this->updateSettingsInDatabase();
        $referrer = str_replace(array('&aioFaviconUpdateSettings', '&aioFaviconDeleteSettings'), '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&aioFaviconUpdateSettings');
    }

    // aioFaviconUpdateSettings()

    /**
     * Update jQuery Colorbox settings
     *
     * handles updating settings in the WordPress database
     *
     * @since 1.0
     * @access private
     * @author Arne Franken
     */
    //private function updateSettingsInDatabase() {
    function updateSettingsInDatabase() {
        update_option(AIOFAVICON_SETTINGSNAME, $this->aioFaviconSettings);
    }

    //aioFaviconUpdateSettingsInDatabase()


    /**
     * Delete jQuery Colorbox settings wrapper
     *
     * handles checks and redirect
     *
     * @since 1.0
     * @access public
     * @author Arne Franken
     */
    //public function aioFaviconDeleteSettings() {
    function aioFaviconDeleteSettings() {

        if (current_user_can('manage_options') && isset($_POST['delete_settings-true'])) {
            //cross check the given referer for nonce set in delete settings form
            check_admin_referer('aio-favicon-delete_settings-form');
            $this->deleteSettingsFromDatabase();
        } else {
            wp_die(sprintf(__('Did not delete %1$s settings. Either you dont have the nececssary rights or you didnt check the checkbox.', AIOFAVICON_TEXTDOMAIN), AIOFAVICON_NAME));
        }
        //clean up referrer
        $referrer = str_replace(array('&aioFaviconUpdateSettings', '&aioFaviconDeleteSettings'), '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&aioFaviconDeleteSettings');
    }

    // aioFaviconDeleteSettings()

    /**
     * Delete jQuery Colorbox settings
     *
     * handles deletion from WordPress database
     *
     * @since 1.0
     * @access private
     * @author Arne Franken
     */
    //private function deleteSettingsFromDatabase() {
    function deleteSettingsFromDatabase() {
        delete_option(AIOFAVICON_SETTINGSNAME);
    }

    // aioFaviconDeleteSettingsFromDatabase()

    /**
     * Read HTML from a remote url
     *
     * @since 2.1
     * @access private
     * @author Arne Franken
     *
     * @param string $url
     * @return the response
     */
    //private function getRemoteContent($url) {
    function getRemoteContent($url) {
        if ( function_exists('wp_remote_request') ) {

            $options = array();
            $options['headers'] = array(
                'User-Agent' => 'All-in-One Favicon V' . AIOFAVICON_VERSION . '; (' . get_bloginfo('url') .')'
             );

            $response = wp_remote_request($url, $options);

            if ( is_wp_error( $response ) )
                return false;

            if ( 200 != wp_remote_retrieve_response_code($response) )
                return false;

            return wp_remote_retrieve_body($response);
        }

        return false;
    }

    // getRemoteContent()

    /**
     * gets current URL to return to after donating
     *
     * @since 2.1
     * @access private
     * @author Arne Franken
     */
    //private function getReturnLocation(){
    function getReturnLocation(){
        $currentLocation = "http";
        $currentLocation .= ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? "s" : "")."://";
        $currentLocation .= $_SERVER['SERVER_NAME'];
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
            if($_SERVER['SERVER_PORT']!='443') {
                $currentLocation .= ":".$_SERVER['SERVER_PORT'];
            }
        }
        else {
            if($_SERVER['SERVER_PORT']!='80') {
                $currentLocation .= ":".$_SERVER['SERVER_PORT'];
            }
        }
        $currentLocation .= $_SERVER['REQUEST_URI'];
        echo $currentLocation;
    }

    // getReturnLocation()
}

?><?php
/**
 * initialize plugin, call constructor
 *
 * @since 1.0
 * @access public
 * @author Arne Franken
 */
function allInOneFavicon() {
    global
    $allInOneFavicon;
    $allInOneFavicon = new AllInOneFavicon();
}

//allInOneFavicon()

// add allInOneFavicon() to WordPress initialization
add_action('init', 'allInOneFavicon', 7);
?>