<?php
/*
License:
 ==============================================================================

    Copyright 2006  Dan Kuykendall  (email : dan@kuykendall.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-107  USA
*/
	class podPress_class {
		var $settings = array();

		// Global hardcoded settings
		var $podcastTag_regexp   = "/\[podcast:([^]]+)]/";
		var $podcastTag          = '[display_podcast]';
		var $podtrac_url         = 'http://www.podtrac.com/pts/redirect.mp3?';
		var $blubrry_url         = 'http://media.blubrry.com/';
		var $requiredAdminRights = 'manage_categories';//'level_7';
		var $realm               = 'Premium Subscribers Content';
		var $justposted          = false;
		var $uploadPath  = '';
		var $tempFileSystemPath  = '';
		var $tempFileURLPath     = '';
		var $tempContentAddedTo  = array();
		var $podangoAPI;

		/*************************************************************/
		/* Load up the plugin values and get ready to action         */
		/*************************************************************/
		
		function podPress_class() {
			//$this->feed_getCategory();
			// this is not workin in WP 3.0: //$this->uploadPath = get_option('upload_path');  
			
			$wp_upload_dir = wp_upload_dir(); // since WP 2.0.0 but the return values were different back than
			if (FALSE == isset($wp_upload_dir['basedir']) OR FALSE == isset($wp_upload_dir['baseurl'])) {
				$wp_upload_dir = $this->upload_dir();
			}
			$this->uploadPath = $wp_upload_dir['basedir'];
			$this->uploadURL = $wp_upload_dir['baseurl'];
			$this->tempFileSystemPath = $this->uploadPath.'/podpress_temp';
			$this->tempFileURLPath = $wp_upload_dir['baseurl'].'/podpress_temp';
			
			// load up podPress general config
			$this->settings = podPress_get_option('podPress_config');
			// make sure things look current, if not run the settings checker
			if(!is_array($this->settings) || PODPRESS_VERSION > $this->settings['lastChecked']) {
				$this->checkSettings();
			}
			if(is_object($GLOBALS['wp_rewrite'])
				&& is_array($GLOBALS['wp_object_cache']) 
				&& is_array($GLOBALS['wp_object_cache']['cache']) 
				&& is_array($GLOBALS['wp_object_cache']['cache']['options']) 
				&& is_array($GLOBALS['wp_object_cache']['cache']['options']['alloptions']) 
				&& is_array($GLOBALS['wp_object_cache']['cache']['options']['alloptions']['rewrite_rules'])
				&& !strpos($GLOBALS['wp_object_cache']['cache']['options']['alloptions']['rewrite_rules'], 'playlist.xspf')
				) {
					$GLOBALS['wp_rewrite']->flush_rules();
			}
		}

		// taken from wp_upload_dir() of WP 2.8.4 to keep up the compatibility of this podPress version with older WP version
		// new in podPress since 8.8.5 beta 9
		function upload_dir() {
			$siteurl = get_option( 'siteurl' );
			$upload_path = get_option( 'upload_path' );
			$upload_path = trim($upload_path);
			if ( empty($upload_path) ) {
				$dir = WP_CONTENT_DIR . '/uploads';
			} else {
				$dir = $upload_path;
			}
			// $dir is absolute, $path is (maybe) relative to ABSPATH
			$dir = $this->path_join( ABSPATH, $dir );
			  
			if ( !$url = get_option( 'upload_url_path' ) ) {
				if ( empty($upload_path) or ( $upload_path == $dir ) ) {
					$url = WP_CONTENT_URL . '/uploads';
				} else {
					$url = trailingslashit( $siteurl ) . $upload_path;
				}
			}
			if ( defined('UPLOADS') ) {
				$dir = ABSPATH . UPLOADS;
				$url = trailingslashit( $siteurl ) . UPLOADS;
			}
			return array( 'basedir' => $dir, 'baseurl' => $url);
		}
		// taken from WP 2.8.4 to keep up the compatibility of this podPress version with older WP version (new in WP since 2.5)
		// new in podPress since 8.8.5 beta 9
		function path_join( $base, $path ) {
			if ( $this->path_is_absolute($path) ) { return $path; }
			return rtrim($base, '/') . '/' . ltrim($path, '/');
		}
		// taken from WP 2.8.4 to keep up the compatibility of this podPress version with older WP version (new in WP since 2.5)
		// new in podPress since 8.8.5 beta 9
		function path_is_absolute( $path ) {
			// this is definitive if true but fails if $path does not exist or contains a symbolic link
			if ( realpath($path) == $path ) { return true;}
			if ( strlen($path) == 0 || $path{0} == '.' ) { return false; }
			// windows allows absolute paths like this
			if ( preg_match('#^[a-zA-Z]:\\\\#', $path) ) { return true; }
			// a path starting with / or \ is absolute; anything else is relative
			return (bool) preg_match('#^[/\\\\]#', $path);
		}

		
		/*************************************************************/
		// Handles all the default values for a new installation of the plugin
		// activate() is called only from podpress_upgrade_class.php during
		// the version check and upgrade routine which runs also after the plugin activation
		/*************************************************************/
		function activate() {
			GLOBAL $wpdb;
			if (function_exists('get_role') AND function_exists('is_role')) {
				if (FALSE === is_role('premium_subscriber')) {
					add_role('premium_subscriber', 'Premium Subscriber', $caps);
				}
				if (TRUE === is_role('premium_subscriber')) {
					$ps_role = get_role('premium_subscriber');
					if (Null !== $ps_role AND !$ps_role->has_cap('premium_content')) {
						$ps_role->add_cap('premium_content');
					}
					if(Null !== $ps_role AND !$ps_role->has_cap('read')) {
						$ps_role->add_cap('read');
					}
				}
				if (TRUE === is_role('administrator')) {
					$role = get_role('administrator');
					if (Null !== $role AND !$role->has_cap('premium_content')) {
						$role->add_cap('premium_content');
					}
				}
			}
			
			// ntm: create the table with the collation or db_charset (since 8.8.5 beta 4)
			if ( TRUE == defined('DB_COLLATE') AND '' !== DB_COLLATE ) {
				$db_charset = ' COLLATE ' . DB_COLLATE;
			} elseif ( (FALSE == defined('DB_COLLATE') OR '' == DB_COLLATE) AND TRUE == defined('DB_CHARSET') AND '' !== DB_CHARSET ) {
				$db_charset = ' DEFAULT CHARACTER SET ' . DB_CHARSET;
			} else {
				$db_charset = '';
			}
			
			// Create stats table
			$create_table = "CREATE TABLE ".$wpdb->prefix."podpress_statcounts (".
			                "postID int(11) NOT NULL default '0',".
			                "media varchar(255) NOT NULL,".
			                "total int(11) default '1',".
			                "feed int(11) default '0',".
			                "web int(11) default '0',".
			                "play int(11) default '0',".
			                "PRIMARY KEY (media)) TYPE=MyISAM" . $db_charset;
			podPress_maybe_create_table($wpdb->prefix."podpress_statcounts", $create_table);
			
			// Create stats table
			$create_table = "CREATE TABLE ".$wpdb->prefix."podpress_stats (".
			                "id int(11) unsigned NOT NULL auto_increment,".
			                "postID int(11) NOT NULL default '0',".
			                "media varchar(255) NOT NULL default '',".
			                "method varchar(50) NOT NULL default '',".
			                "remote_ip varchar(15) NOT NULL default '',".
			                "country varchar(50) NOT NULL default '',".
			                "language VARCHAR(5) NOT NULL default '',".
			                "domain varchar(255) NOT NULL default '',".
			                "referer varchar(255) NOT NULL default '',".
			                "resource varchar(255) NOT NULL default '',".
			                "user_agent varchar(255) NOT NULL default '',".
			                "platform varchar(50) NOT NULL default '',".
			                "browser varchar(50) NOT NULL default '',".
			                "version varchar(15) NOT NULL default '',".
			                "dt int(10) unsigned NOT NULL default '0',".
					"completed TINYINT(1) UNSIGNED DEFAULT '0',".
			                "UNIQUE KEY id (id)) TYPE=MyISAM" . $db_charset;
			podPress_maybe_create_table($wpdb->prefix."podpress_stats", $create_table);

			if(function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}

			$current = get_option('podPress_version');
			if ( FALSE === $current ) {
				$current = constant('PODPRESS_VERSION');
				update_option('podPress_version', $current);
			}

			//$this->checkSettings();
		}

		/**
		* checkLocalPathToMediaFiles - checks whether the "Local path to media files directory" exists or not. (This procedure is simply taken from the checkSettings() function earlier versions.)
		*
		* @package podPress
		* @since 8.8.5 beta 4
		*
		*/
		function checkLocalPathToMediaFiles() {
			unset($this->settings['autoDetectedMediaFilePath']);
			if (!file_exists($this->settings['mediaFilePath'])) {
				$this->settings['autoDetectedMediaFilePath'] = $this->uploadPath;
				
				//~ //$this->settings['autoDetectedMediaFilePath'] = str_replace(get_option('siteurl'), '', $this->settings['mediaWebPath']);
				//~ $this->settings['autoDetectedMediaFilePath'] = str_replace(get_option('siteurl'), '', $this->settings['mediaWebPath']);
				//~ $this->settings['autoDetectedMediaFilePath'] = ABSPATH.$this->settings['autoDetectedMediaFilePath'];
				//~ $this->settings['autoDetectedMediaFilePath'] = str_replace('\\\\', '\\', $this->settings['autoDetectedMediaFilePath']);
				//~ $this->settings['autoDetectedMediaFilePath'] = str_replace('//', '/', $this->settings['autoDetectedMediaFilePath']);
				//~ $this->settings['autoDetectedMediaFilePath'] = str_replace('//', '/', $this->settings['autoDetectedMediaFilePath']);
				if(!file_exists($this->settings['autoDetectedMediaFilePath'])) {
					$this->settings['autoDetectedMediaFilePath'] .= ' ('.__('Auto Detection Failed.', 'podpress').') -'.strval($this->uploadPath).'-';
				} 
			}
		}

		function checkSettings() {
			GLOBAL $wp_object_cache, $wp_rewrite;
			if(!is_array($this->settings)) {
				$this->settings = podPress_get_option('podPress_config');
				if(!is_array($this->settings)) {
					$this->settings = array();
				}
			}

			$this->settings['lastChecked'] = PODPRESS_VERSION;

			// Make sure some standard values are set.
			$x = get_option('rss_language');
			if(!$x || empty($x))
			{
				add_option('rss_language', 'en');
			}

			$x = get_option('rss_image');
			if(!$x || empty($x))
			{
				podPress_update_option('rss_image', podPress_url().'images/powered_by_podpress.jpg');
			}

			// ntm: these settings are for the theme compatibility check resp. message. It seems to me that this checker was not in place at least since 8.8.
			// I could not find the routine which searches through the theme files for the wp_head and get_header etc. calls. Until there is such a test, I will set these values to TRUE.
			//~ if($this->settings['compatibilityChecks']['themeTested'] !== true) {
				//~ $this->settings['compatibilityChecks']['themeTested'] = false;
			//~ }
			//~ if($this->settings['compatibilityChecks']['wp_head'] !== true) {
				//~ $this->settings['compatibilityChecks']['wp_head'] = false;
			//~ }
			//~ if($this->settings['compatibilityChecks']['wp_footer'] !== true) {
				//~ $this->settings['compatibilityChecks']['wp_footer'] = false;
			//~ }
			$this->settings['compatibilityChecks']['themeTested'] = TRUE;
			$this->settings['compatibilityChecks']['wp_head'] = TRUE;
			$this->settings['compatibilityChecks']['wp_footer'] = TRUE;


			if(!is_bool($this->settings['enableStats'])) {
				if($this->settings['enableStats']== 'true') {
					$this->settings['enableStats'] = true;
				} else {
					$this->settings['enableStats'] = false;
				}
			}
			
			// no dashboardwidget support for WP 2.5.x and WP 2.6.x
			if ( TRUE == isset($this->settings['disabledashboardwidget']) AND TRUE === $this->settings['disabledashboardwidget'] OR (TRUE == version_compare($wp_version, '2.5', '>=') AND TRUE == version_compare($wp_version, '2.7', '<')) ) {
				$this->settings['disabledashboardwidget'] = TRUE;
			} else {
				$this->settings['disabledashboardwidget'] = FALSE;
			}
			
			if(!$this->settings['statMethod'] || empty($this->settings['statMethod']) || $this->settings['statMethod'] == 'htaccess')
			{
				$this->settings['statMethod'] = 'permalinks';
			}

			if(!$this->settings['statLogging'] || empty($this->settings['statLogging']))
			{
				$this->settings['statLogging'] = 'Counts';
			}

			if(empty($this->settings['enable3rdPartyStats'])) {
				$this->settings['enable3rdPartyStats'] = 'No';
			}

			if(!is_bool($this->settings['enableBlubrryStats'])) {
				if($this->settings['enableBlubrryStats']== 'true') {
					$this->settings['enableBlubrryStats'] = true;
				} else {
					$this->settings['enableBlubrryStats'] = false;
				}
			}

			if(!$this->settings['rss_copyright'] || empty($this->settings['rss_copyright']))
			{
				$this->settings['rss_copyright'] = __('Copyright', 'podpress').' &#xA9; '.get_bloginfo('blogname').' '. date('Y',time());
			}

			if(podPress_WPVersionCheck('2.0.0')) {
				if(!is_bool($this->settings['enablePremiumContent'])) {
					if($this->settings['enablePremiumContent']== 'true') {
						$this->settings['enablePremiumContent'] = true;
					} else {
						$this->settings['enablePremiumContent'] = false;
					}
				}
			} else {
				$this->settings['enablePremiumContent'] = false;
			}

			if(empty($this->settings['premiumMethod'])) {
				$this->settings['premiumMethod'] = 'Digest';
			}
			if(!defined('PODPRESS_PREMIUM_METHOD')) {
				define('PODPRESS_PREMIUM_METHOD', $this->settings['premiumMethod']);
			}

			if(!is_bool($this->settings['enableTorrentCasting'])) {
				if($this->settings['enableTorrentCasting']== 'true') {
					$this->settings['enableTorrentCasting'] = true;
				} else {
					$this->settings['enableTorrentCasting'] = false;
				}
			}

			if(empty($this->settings['podcastFeedURL'])) {
				if(podPress_WPVersionCheck('2.1')) {
					$this->settings['podcastFeedURL'] = get_option('siteurl').'/?feed=podcast';
				} else {
					$this->settings['podcastFeedURL'] = get_option('siteurl').'/?feed=rss2';
				}
			}

			if(empty($this->settings['mediaWebPath'])) {
				$this->settings['mediaWebPath'] = $this->uploadURL;//get_option('siteurl').'/wp-content/uploads';
			}
			
			$this->checkLocalPathToMediaFiles();

			if(empty($this->settings['maxMediaFiles']) || $this->settings['maxMediaFiles'] < 1) {
				$this->settings['maxMediaFiles'] = 10;
			}

			if(!$this->settings['contentBeforeMore'] || empty($this->settings['contentBeforeMore']))
			{
				$this->settings['contentBeforeMore'] = 'yes';
			}

			if(!$this->settings['contentLocation'] || empty($this->settings['contentLocation']))
			{
				$this->settings['contentLocation'] = 'end';
			}

			if(!$this->settings['contentImage'] || empty($this->settings['contentImage']))
			{
				$this->settings['contentImage'] = 'button';
			}

			if(!$this->settings['contentPlayer'] || empty($this->settings['contentPlayer']))
			{
				$this->settings['contentPlayer'] = 'both';
			}
			
			if(!$this->settings['contentHidePlayerPlayNow'] || empty($this->settings['contentHidePlayerPlayNow']))
			{
				$this->settings['contentHidePlayerPlayNow'] = 'enabled';
			}

			if(FALSE == isset($this->settings['videoPreviewImage']) OR empty($this->settings['videoPreviewImage'])) {
				$this->settings['videoPreviewImage'] = PODPRESS_URL.'/images/vpreview_center.png';
			}

			if(!is_bool($this->settings['disableVideoPreview'])) {
				if($this->settings['disableVideoPreview']== 'true') {
					$this->settings['disableVideoPreview'] = true;
				} else {
					$this->settings['disableVideoPreview'] = false;
				}
			}

			if(!$this->settings['contentDownload'] || empty($this->settings['contentDownload']))
			{
				$this->settings['contentDownload'] = 'enabled';
			}

			if(!$this->settings['contentDownloadText'] || empty($this->settings['contentDownloadText']))
			{
				$this->settings['contentDownloadText'] = 'enabled';
			}

			if(!$this->settings['contentDownloadStats'] || empty($this->settings['contentDownloadStats']))
			{
				$this->settings['contentDownloadStats'] = 'enabled';
			}

			if(!$this->settings['contentDuration'] || empty($this->settings['contentDuration']))
			{
				$this->settings['contentDuration'] = 'enabled';
			}

			if ( FALSE == isset($this->settings['incontentandexcerpt']) ) {
				$this->settings['incontentandexcerpt'] = 'in_content_and_excerpt';
			}

			if(!is_bool($this->settings['contentAutoDisplayPlayer'])) {
				if($this->settings['contentAutoDisplayPlayer'] == 'false') {
					$this->settings['contentAutoDisplayPlayer'] = false;
				} else {
					$this->settings['contentAutoDisplayPlayer'] = true;
				}
			}

			if(!is_bool($this->settings['enableFooter'])) {
				if($this->settings['enableFooter']== 'false') {
					$this->settings['enableFooter'] = false;
				} else {
					$this->settings['enableFooter'] = true;
				}
			}
			
			if ( FALSE == isset($this->settings['mp3Player']) ) {
				$this->settings['mp3Player'] = '1pixelout';
			}
			
			if($this->settings['player']['bg'] == '') {
				$this->resetPlayerSettings();
			}

			if(empty($this->settings['iTunes']['summary'])) {
				$this->settings['iTunes']['summary'] = stripslashes(get_option('blogdescription'));
			} else {
				$this->settings['iTunes']['summary'] = stripslashes($this->settings['iTunes']['summary']);
			}
			$this->settings['iTunes']['keywords'] = stripslashes($this->settings['iTunes']['keywords']);
			$this->settings['iTunes']['subtitle'] = stripslashes($this->settings['iTunes']['subtitle']);
			$this->settings['iTunes']['author'] = stripslashes($this->settings['iTunes']['author']);

			$this->settings['iTunes']['FeedID'] = stripslashes($this->settings['iTunes']['FeedID']);
			$this->settings['iTunes']['FeedID'] = str_replace(' ', '', $this->settings['iTunes']['FeedID']);
			if(!empty($this->settings['iTunes']['FeedID']) && !is_numeric($this->settings['iTunes']['FeedID'])) {
				$this->settings['iTunes']['FeedID'] = settype($this->settings['iTunes']['FeedID'], 'double');
			}

			if(empty($this->settings['iTunes']['explicit'])) {
				$this->settings['iTunes']['explicit'] = 'No';
			}

			if(empty($this->settings['iTunes']['image'])) {
				$x = get_option('rss_image');
				if ( FALSE !== $x && $x != podPress_url().'images/powered_by_podpress.jpg') {
					$this->settings['iTunes']['image'] = $x;
				} else {
					$this->settings['iTunes']['image'] = podPress_url().'images/powered_by_podpress_large.jpg';
				}
			}

			if ( FALSE == isset($this->settings['iTunes']['new-feed-url']) OR ('Enable' != $this->settings['iTunes']['new-feed-url'] AND 'Disable' != $this->settings['iTunes']['new-feed-url']) ) {
				$this->settings['iTunes']['new-feed-url'] = 'Disable';
			}
			if ( FALSE == isset($this->settings['iTunes']['block']) OR ('Yes' != $this->settings['iTunes']['block'] AND 'No' != $this->settings['iTunes']['block']) ) {
				$this->settings['iTunes']['block'] = 'No';
			}
			if ( FALSE == isset($this->settings['rss_license_url']) ) {
				$this->settings['rss_license_url'] = '';
			}
			
			// since 8.8.8: default settings for the fully customizable feeds
			if ( FALSE == is_array($this->settings['podpress_feeds']) OR empty($this->settings['podpress_feeds']) ) {
				$email = get_option('admin_email');
				if ( FALSE === $email ) {
					$email = '';
				}
				$tagline = $this->settings['iTunes']['subtitle'];
				if ( FALSE === $tagline ) {
					$tagline = '';
				}
				$blog_charset = get_bloginfo('charset');
				$rss_language = get_option('rss_language');
				if ( FALSE === $rss_language ) { $rss_language = 'en'; }
				$rss_image = get_option('rss_image');
				if ( FALSE === $rss_image OR TRUE == empty($rss_image) ) {
					$rss_image = PODPRESS_URL.'/images/powered_by_podpress.jpg';
				}
				$ttl = get_option('rss_ttl');
				if ( FALSE === $ttl  OR (!empty($ttl) && $ttl < 1440) ) {
					$ttl  = 1440;
				}
				//~ if ( defined('PODPRESS_TAKEOVER_OLD_SETTINGS') AND TRUE === constant('PODPRESS_TAKEOVER_OLD_SETTINGS') ) {
					if ( FALSE == is_array($this->settings['iTunes']['category']) ) {
						$itunescategory = Array();
					}
					$this->settings['podpress_feeds'][0] = array(
						'use' => TRUE, 
						'premium' => FALSE,
						'name' => __('Podcast Feed', 'podpress'),
						'slug' => 'podcast',
						'feedtitle' => 'append',
						'subtitle' => $tagline,
						'itunes-newfeedurl' => 'Disable',
						'descr' => $this->settings['iTunes']['summary'],
						'itunes-category' => $itunescategory,
						'rss_category' => $this->settings['rss_category'],
						'itunes-keywords' => $this->settings['iTunes']['keywords'],
						'itunes-author' => $this->settings['iTunes']['author'],
						'email' => $email,
						'itunes-image' => $this->settings['iTunes']['image'],
						'rss_image' => $rss_image,
						'copyright' => $this->settings['rss_copyright'],
						'license_url' => $this->settings['rss_license_url'],
						'language' => $rss_language,
						'charset' => $blog_charset,
						'FileTypes' => Array(),
						'inclCategories' => Array(),
						'show_only_podPress_podcasts' => TRUE,
						'bypass_incl_selection' => FALSE,
						'itunes-explicit' => $this->settings['iTunes']['explicit'],
						'feedtype' => 'rss',
						'ttl' => $ttl,
						'itunes-feedid' => $this->settings['iTunes']['FeedID'],
						'itunes-block' => $this->settings['iTunes']['block']
					);
					$this->settings['podpress_feeds'][1] = array(
						'use' => TRUE, 
						'premium' => FALSE,
						'name' => __('Enhanced Podcast Feed', 'podpress'),
						'slug' => 'enhancedpodcast',
						'feedtitle' => 'append',
						'subtitle' => $tagline,
						'itunes-newfeedurl' => 'Disable',
						'descr' => $this->settings['iTunes']['summary'],
						'itunes-category' => $itunescategory,
						'rss_category' => $this->settings['rss_category'],
						'itunes-keywords' => $this->settings['iTunes']['keywords'],
						'itunes-author' => $this->settings['iTunes']['author'],
						'email' => $email,
						'itunes-image' => $this->settings['iTunes']['image'],
						'rss_image' => $rss_image,
						'copyright' => $this->settings['rss_copyright'],
						'license_url' => $this->settings['rss_license_url'],
						'language' => $rss_language,
						'charset' => $blog_charset,
						'FileTypes' => Array('audio_m4a', 'video_m4v'),
						'inclCategories' => Array(),
						'show_only_podPress_podcasts' => TRUE,
						'bypass_incl_selection' => FALSE,
						'itunes-explicit' => $this->settings['iTunes']['explicit'],
						'feedtype' => 'atom',
						'ttl' => $ttl,
						'itunes-feedid' => $this->settings['iTunes']['FeedID'],
						'itunes-block' => $this->settings['iTunes']['block']
					);
					$this->settings['podpress_feeds'][2] = array(
						'use' => TRUE, 
						'premium' => FALSE,
						'name' => __('Torrent Feed', 'podpress'),
						'slug' => 'torrent',
						'feedtitle' => 'append',
						'subtitle' => $tagline,
						'itunes-newfeedurl' => 'Disable',
						'descr' => $this->settings['iTunes']['summary'],
						'itunes-category' => $itunescategory,
						'rss_category' => $this->settings['rss_category'],
						'itunes-keywords' => $this->settings['iTunes']['keywords'],
						'itunes-author' => $this->settings['iTunes']['author'],
						'email' => $email,
						'itunes-image' => $this->settings['iTunes']['image'],
						'rss_image' => $rss_image,
						'copyright' => $this->settings['rss_copyright'],
						'license_url' => $this->settings['rss_license_url'],
						'language' => $rss_language,
						'charset' => $blog_charset,
						'FileTypes' => Array('torrent'),
						'inclCategories' => Array(),
						'show_only_podPress_podcasts' => TRUE,
						'bypass_incl_selection' => FALSE,
						'itunes-explicit' => $this->settings['iTunes']['explicit'],
						'feedtype' => 'atom',
						'ttl' => $ttl,
						'itunes-feedid' => $this->settings['iTunes']['FeedID'],
						'itunes-block' => $this->settings['iTunes']['block']
					);
					$this->settings['podpress_feeds'][3] = array(
						'use' => TRUE, 
						'premium' => TRUE,
						'name' => __('Premium Feed', 'podpress'),
						'slug' => 'premium',
						'feedtitle' => 'append',
						'subtitle' => $tagline,
						'itunes-newfeedurl' => 'Disable',
						'descr' => $this->settings['iTunes']['summary'],
						'itunes-category' => $itunescategory,
						'rss_category' => $this->settings['rss_category'],
						'itunes-keywords' => $this->settings['iTunes']['keywords'],
						'itunes-author' => $this->settings['iTunes']['author'],
						'email' => $email,
						'itunes-image' => $this->settings['iTunes']['image'],
						'rss_image' => $rss_image,
						'copyright' => $this->settings['rss_copyright'],
						'license_url' => $this->settings['rss_license_url'],
						'language' => $rss_language,
						'charset' => $blog_charset,
						'FileTypes' => Array(),
						'inclCategories' => Array(),
						'show_only_podPress_podcasts' => TRUE,
						'bypass_incl_selection' => FALSE,
						'itunes-explicit' => $this->settings['iTunes']['explicit'],
						'feedtype' => 'rss',
						'ttl' => $ttl,
						'itunes-feedid' => $this->settings['iTunes']['FeedID'],
						'itunes-block' => $this->settings['iTunes']['block']
					);
				//~ } else {
					//~ // in case it is a first time installation
					//~ $this->settings['podpress_feeds'][0] = array(
						//~ 'use' => TRUE, 
						//~ 'premium' => FALSE,
						//~ 'name' => __('Podcast Feed', 'podpress'),
						//~ 'slug' => 'podcast',
						//~ 'feedtitle' => 'append',
						//~ 'subtitle' => '',
						//~ 'itunes-newfeedurl' => 'Disable',
						//~ 'descr' => '',
						//~ 'itunes-category' => Array(),
						//~ 'rss_category' => '',
						//~ 'itunes-keywords' => '',
						//~ 'itunes-author' => '',
						//~ 'email' => '',
						//~ 'itunes-image' => $this->settings['iTunes']['image'],
						//~ 'rss_image' => $rss_image,
						//~ 'copyright' => $this->settings['rss_copyright'],
						//~ 'license_url' => '',
						//~ 'language' => $rss_langauge,
						//~ 'charset' => $blog_charset,
						//~ 'FileTypes' => Array(),
						//~ 'itunes-explicit' => 'No',
						//~ 'feedtype' => 'rss',
						//~ 'ttl' => $ttl,
						//~ 'itunes-feedid' => '',
						//~ 'itunes-block' => 'No'
					//~ );
				//~ }
			}
			
			podPress_update_option('podPress_config', $this->settings);
			
			if (
				is_object($wp_rewrite)
				&& is_array($wp_object_cache) 
				&& is_array($wp_object_cache['cache']) 
				&& is_array($wp_object_cache['cache']['options']) 
				&& is_array($wp_object_cache['cache']['options']['alloptions']) 
				&& is_array($wp_object_cache['cache']['options']['alloptions']['rewrite_rules'])
				&& !strpos($wp_object_cache['cache']['options']['alloptions']['rewrite_rules'], 'playlist.xspf')
			) {
				$wp_rewrite->flush_rules();
			}
		}

		function deactivate() {
			// at the moment I have nothing I would want to clean up
		}
		
		/**
		* make_upgrade - helps to update the current settings
		*
		* @package podPress
		* @since 8.8.8 beta 5
		*
		*/
		function update_podpress_class($podpress_version_from_db = '0') {
			if (class_exists('podPressUpgrade_class')) {
				$u = new podPressUpgrade_class($podpress_version_from_db);
				$this->settings = $u->settings;
			}
			return $this;
		}

		function iTunesLink() {
			return '<a href="http://phobos.apple.com/WebObjects/MZStore.woa/wa/viewPodcast?id='.$this->settings['iTunes']['FeedID'].'"><img src="'.podPress_url().'images/itunes.png" border="0" alt="View in iTunes"/></a>';
		}

		function PlayerDefaultSettings() {
			// ntm: these colors are from the recent audio-player plugin
			// The new colors should make it clear that the 1PixelOut player was also updated in 8.8.5
			$result['bg'] = '#E5E5E5';
			$result['text'] = '#333333';
			$result['leftbg'] = '#CCCCCC';
			$result['lefticon'] = '#333333';
			$result['volslider'] = '#666666';
			$result['voltrack'] = '#FFFFFF';
			$result['rightbg'] = '#B4B4B4';
			$result['rightbghover'] = '#999999';
			$result['righticon'] = '#333333';
			$result['righticonhover'] = '#FFFFFF';
			$result['loader'] = '#009900';
			$result['track'] = '#FFFFFF';
			$result['border'] = '#CCCCCC';
			$result['tracker'] = '#DDDDDD';
			$result['skip'] = '#666666';
			
			$result['slider'] = '#666666'; // this is only for the Podango player
			
			$result['initialvolume'] = 70; // for 1Pixelout player (since podPress 8.8.5.2)
			$result['buffer'] = 5; // for 1Pixelout player (since podPress 8.8.5.2)
			$result['checkpolicy'] = 'no'; // for 1Pixelout player (since podPress 8.8.5.2)
			
			// player settings which are no direct resp. JS parameters of the players
			$result['overwriteTitleandArtist'] = 'no'; // for 1Pixelout player (since podPress 8.8.5.2)
			$result['listenWrapper'] = false;
			return $result;
		}
		
		function resetPlayerSettings() {
			$result = $this->PlayerDefaultSettings();
			$this->settings['player'] = $result;
			return $result;
		}

		function convertPodcastFileNameToValidWebPath($filename) {
			if ( strpos(substr($filename, 0, 10), '://') ) {
				$url = $filename;
			} else {
				if ( substr($filename, 0,1) == '/' ) {
					$baseurl = strtolower(strtok($_SERVER['SERVER_PROTOCOL'], '/')).'://'.$_SERVER['HTTP_HOST'].$this->settings['mediaWebPath'];
				} elseif ( strpos(substr($this->settings['mediaWebPath'], 0, 10), '://') ) {
					$baseurl = $this->settings['mediaWebPath'];
				} else {
					$baseurl = get_option('siteurl').$this->settings['mediaWebPath'];
				}
				if ( substr($filename, -1, 1) != '/' ) {
					$baseurl .= '/';
				}
				$url = $baseurl.$filename;
			}
			return $url;
		}

		function convertPodcastFileNameToWebPath($postID, $mediaNum, $filename = '', $method = false){
			$url = $this->convertPodcastFileNameToValidWebPath($filename);
			if($method != false) {
				if($this->settings['enableStats']) {
					$filename_part = podPress_getFileName($url);
					if($this->settings['statMethod'] == 'download.mp3') {
						$url = podPress_url().'download.mp3?'.$method.'='.$postID.'/'.$mediaNum.'/'.$filename_part;
					} else {
						$url = get_bloginfo('home').'/podpress_trac/'.$method.'/'.$postID.'/'.$mediaNum.'/'.$filename_part;
					}
				} elseif($this->settings['enable3rdPartyStats'] == 'Podtrac') {
					$url = str_replace(array('ftp://', 'http://', 'https://'), '', $url);
					$url = $this->podtrac_url.$url;
				} elseif($this->settings['enable3rdPartyStats'] == 'BluBrry' && !empty($this->settings['statBluBrryProgramKeyword'])) {
					$url = $this->blubrry_url.$this->settings['statBluBrryProgramKeyword'].'/'.$url;
				}
			}
			$url = str_replace(' ', '%20', $url);
			return $url;
		}
		
		function convertPodcastFileNameToSystemPath($filename = ''){
			if( FALSE === strpos(substr($filename, 0, 10), '://') ) {
				//if ( FALSE === empty($this->settings['mediaFilePath']) ) {
					$filename = $this->settings['mediaFilePath'].'/'.$filename;
					if(file_exists($filename))
					{
						return $filename;
					}
				//} 
			}
			return false;
		}
		
		/**
		* TryToFindAbsFileName - tries to build a absolute path for the given URL and checks whether the build path exists and is local or not. This inetended deal with the (real) URLs of the media files
		*
		* @package podPress
		* @since 8.8.6 beta 1
		*
		* @param str $url - The (real) URL of a media file. (real URL means the URL of a media file and not the URLs masked for statistics)
		*
		* @return mixed the abspath to the file or FALSE
		*/
		function TryToFindAbsFileName($url = '') {
			if (FALSE == empty($url)) {
				$uploadpath = $this->uploadPath;
				// remove drive letter
				$uploadpath_WL = end(explode(':', $uploadpath));
				// remove doubled backslashes
				$uploadpath_san1 = str_replace("\\\\", "\\", $uploadpath_WL);
				// replace backslash with slashes
				$uploadpath_san2 = str_replace("\\", "/", $uploadpath_san1);
				$uploadpath_parts = explode('/', $uploadpath_san2);
				
				//$result= parse_url($url);
				$home= get_bloginfo('home');
				$result['path'] = str_replace($home, '', $url);
				$urlpath_parts = explode('/', $result['path']);
				$diff = array_diff($urlpath_parts, $uploadpath_parts);
				$filename = $uploadpath.'/'.implode('/', $diff);
				if (TRUE === file_exists($filename)) {
					return $filename;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		/**
		* checkWritableTempFileDir - (return values different since 8.8.6) controls whether the podPress temp/-folder is exists, or can be created and is writeable. This folder is e.g. uploads/podpress_temp/
		*
		* @package podPress
		* @since 8.8.6 beta 5
		*
		* @param bool $returnMessages - If it is true then the function returns in case of an error instead of false a string.
		*
		* @return mixed - TRUE if the folder exists and is writeable / FALSE or a string if there is a problem
		*/
		function checkWritableTempFileDir($returnMessages = FALSE) {
			$siteurl = get_option('siteurl');
			if (file_exists($this->tempFileSystemPath)) {
				if(is_writable($this->tempFileSystemPath)) {
					if ($returnMessages) {
						return __('(The folder is writeable.)', 'podpress');
					} else {
						return true;
					}
				} else {
					if ($returnMessages) {
						return '<p class="message error">'.sprintf(__('Your uploads/podpress_temp directory is not writable. Please set permissions as needed, and make sure <a href="%1$s/wp-admin/options-misc.php">configuration</a> is correct.', 'podpress'), $siteurl).'<br />'.__('Currently set to:', 'podpress').'<code>'.$this->tempFileSystemPath."</code></p>\n";
					} else {
						return false;
					}
				}
			} elseif (!file_exists($this->uploadPath)) {
				if ($returnMessages) {
					return '<p class="message error">'.sprintf(__('Your WordPress upload directory does not exist. Please create it and make sure <a href="%1$s/wp-admin/options-misc.php">configuration</a> is correct.', 'podpress'), $siteurl).'<br />'.__('Currently set to:', 'podpress').'<code>'.$this->uploadPath."</code></p>\n";
				} else {
					return false;
				}
			} elseif (!is_writable($this->uploadPath)) {
				if ($returnMessages) {
					return '<p class="message error">'.sprintf(__('Your WordPress upload directory is not writable. Please set permissions as needed, and make sure <a href="%1$s/wp-admin/options-misc.php">configuration</a> is correct.', 'podpress'), $siteurl).'<br />'.__('Currently set to:', 'podpress').'<code>'.$this->uploadPath."</code></p>\n";
				} else {
					return false;
				}
			} else {
				$mkdir = @mkdir($this->tempFileSystemPath);
				if (!$mkdir) {
					if ($returnMessages) {
						return '<p class="message error">'.__('Could not create uploads/podpress_temp directory. Please set permission of the following directory to 755 or 777:', 'podpress').'<br /><code>'.$this->tempFileSystemPath."</code></p>\n";
					} else {
						return false;
					}
				} else {
					if ($returnMessages) {
						return __('(The folder is writeable.)', 'podpress');
					} else {
						return true;
					}
				}
			}
		}


		/*************************************************************/
		/* Load up the plugin values and get ready to action         */
		/*************************************************************/
		function addPostData($input, $forEdit = false) {
			$input->podPressMedia = podPress_get_post_meta($input->ID, 'podPressMedia', true);
			if(!is_array($input->podPressMedia)) {
				$x = maybe_unserialize($input->podPressMedia);
				if(is_array($x)) {
					$input->podPressMedia = $x;
				}
				if(!is_array($input->podPressMedia)) {
					$x = maybe_unserialize($input->podPressMedia, true);
					if(is_array($x)) {
						$input->podPressMedia = $x;
					}
				}
			}
			if(is_array($input->podPressMedia)) {
				reset($input->podPressMedia);
				while (list($key) = each($input->podPressMedia)) {
					if( !empty($input->podPressMedia[$key]['URI']) ) {
						if ( TRUE == isset($input->podPressMedia[$key]['premium_only']) AND ($input->podPressMedia[$key]['premium_only'] == 'on' || $input->podPressMedia[$key]['premium_only'] == true) ) {
							$input->podPressMedia[$key]['content_level'] = 'premium_content';
						} elseif ( !isset($input->podPressMedia[$key]['content_level']) ) {
							$input->podPressMedia[$key]['content_level'] = 'free';
						}

						if(!isset($input->podPressMedia[$key]['type'])) {
							$input->podPressMedia[$key]['type'] = '';
						}
						settype($input->podPressMedia[$key]['size'], 'int');
						if(0 >= $input->podPressMedia[$key]['size']) {
							$filepath = $this->convertPodcastFileNameToSystemPath($input->podPressMedia[$key]['URI']);
							if($filepath) {
								$input->podPressMedia[$key]['size'] = filesize ($filepath);
							} else {
								$input->podPressMedia[$key]['size'] = 1;
							}
						}

						$input->podPressMedia[$key]['ext'] = podPress_getFileExt($input->podPressMedia[$key]['URI']);
						$input->podPressMedia[$key]['mimetype'] = podPress_mimetypes($input->podPressMedia[$key]['ext']);

						if(!$forEdit && $this->settings['enablePremiumContent'] && $input->podPressMedia[$key]['content_level'] != 'free' && @$GLOBALS['current_user']->allcaps[$input->podPressMedia[$key]['content_level']] != 1) {
							$input->podPressMedia[$key]['authorized'] = false;
							$input->podPressMedia[$key]['URI'] = '';
							$input->podPressMedia[$key]['URI_torrent'] = '';
						} else {
							$input->podPressMedia[$key]['authorized'] = true;
						}
					}
				}
			}

			$input->podPressPostSpecific = podPress_get_post_meta($input->ID, 'podPressPostSpecific', true);
			if(!is_array($input->podPressPostSpecific)) {
				$input->podPressPostSpecific = array();
			}

			if(empty($input->podPressPostSpecific['itunes:subtitle'])) {
				$input->podPressPostSpecific['itunes:subtitle'] = '##PostExcerpt##';
			}
			if(empty($input->podPressPostSpecific['itunes:summary'])) {
				$input->podPressPostSpecific['itunes:summary'] = '##PostExcerpt##';
			}
			if(empty($input->podPressPostSpecific['itunes:keywords'])) {
				$input->podPressPostSpecific['itunes:keywords'] = '##WordPressCats##';
			}
			if(empty($input->podPressPostSpecific['itunes:author'])) {
				$input->podPressPostSpecific['itunes:author'] = '##Global##';
			}
			if(empty($input->podPressPostSpecific['itunes:explicit'])) {
				$input->podPressPostSpecific['itunes:explicit'] = 'Default';
			}
			if(empty($input->podPressPostSpecific['itunes:block'])) {
				$input->podPressPostSpecific['itunes:block'] = 'Default';
			}
			return $input;
		}

		function the_posts($input) {
			if ( FALSE === is_admin() && !$this->settings['compatibilityChecks']['themeTested'] ) {
				$this->settings['compatibilityChecks']['themeTested'] = true;
				podPress_update_option('podPress_config', $this->settings);
			}
			if(!is_array($input)) {
				return $input;
			}
			foreach($input as $key=>$value) {
				$input[$key] = $this->addPostData($value);
			}
			return $input;
		}

		function posts_join($input) {
			GLOBAL $wpdb;
			if ( defined('PODPRESS_PODCASTSONLY') AND FALSE !== constant('PODPRESS_PODCASTSONLY') ) {
				$input .= " JOIN ".$wpdb->prefix."postmeta ON ".$wpdb->prefix."posts.ID=".$wpdb->prefix."postmeta.post_id ";
			}
			return $input;
		}

		function posts_where($input) {
			GLOBAL $wpdb;
			if ( defined('PODPRESS_PODCASTSONLY') AND FALSE !== constant('PODPRESS_PODCASTSONLY') ) {
				$input .= " AND ".$wpdb->prefix."postmeta.meta_key='podPressMedia' ";
			}
			return $input;
		}

		function insert_the_excerpt($content = '') {
			GLOBAL $post;
			$this->tempContentAddedTo[$post->ID] = true;
			return $content;
		}

		function insert_the_excerptplayer($content = '') {
			GLOBAL $post;
			unset($this->tempContentAddedTo[$post->ID]);
			$content = $this->insert_content($content, TRUE);
			unset($this->tempContentAddedTo[$post->ID]);
			return $content;
		}

		function insert_content($content = '', $is_excerpt = FALSE) {
			GLOBAL $post, $podPressTemplateData, $podPressTemplateUnauthorizedData, $wpdb;
			if ( !empty($post->post_password) ) { // if there's a password
				if ( stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH]) != $post->post_password ) {	// and it doesn't match the cookie
					return $content;
				}
			}
			if(isset($this->tempContentAddedTo[$post->ID])) {
				if(is_feed()) {
					return str_replace($this->podcastTag,'',$content);
				}
				if ( FALSE === is_single() AND FALSE === is_page() ) {
					return $content;
				}
			} else {
				$this->tempContentAddedTo[$post->ID] = true;
			}

			if(is_feed()) {
				if($this->settings['protectFeed'] == 'Yes' && get_bloginfo('charset') == 'UTF-8') {
					$content = podPress_feedSafeContent($content);
				}
				if($this->settings['rss_showlinks'] != 'yes') {
					return str_replace($this->podcastTag,'',$content);
				}
			}

			if(!is_array($post->podPressMedia)) {
				return str_replace($this->podcastTag,'',$content);
			}

			$hasLocationDefined = (bool)strstr($content, $this->podcastTag);
			if(!$hasLocationDefined) {
				if($this->settings['contentBeforeMore'] == 'no') {
					if (is_home() or is_archive()) {
						if ( FALSE !== strpos($post->post_content, '<!--more-->') ) {
							return $content;
						}
					}
				}
				if($this->settings['contentLocation'] == 'start') {
					$content = $this->podcastTag.$content;
				} else {
					$content .= $this->podcastTag;
				}
			}
			
			
			$podpressTag_in_the_content = '<p>'.$this->podcastTag.'</p>';
			
			// add the player and the other elements not if the related setting has been set 
			if ( TRUE == isset($this->settings['incontentandexcerpt']) ) {
				if ( $is_excerpt === TRUE ) {
					switch ( $this->settings['incontentandexcerpt'] ) {
						default :
						case 'in_content_and_excerpt' :
						case 'in_excerpt_only' :
						break;
						case 'in_content_only' :
							if ( FALSE !== stripos($content, $podpressTag_in_the_content) ) {
								return str_replace($podpressTag_in_the_content, '', $content);
							} else {
								return str_replace($this->podcastTag,'',$content);
							}
						break;
					}
					$content = "\n<!-- is excerpt -->\n" . $content;
				} else {
					switch ( $this->settings['incontentandexcerpt'] ) {
						default :
						case 'in_content_and_excerpt' :
						case 'in_content_only' :
						break;
						case 'in_excerpt_only' :
							if ( FALSE !== stripos($content, $podpressTag_in_the_content) ) {
								return str_replace($podpressTag_in_the_content, '', $content);
							} else {
								return str_replace($this->podcastTag,'',$content);
							}
						break;
					}
					$content = "\n<!-- is content -->\n" . $content;
				}
			}

			$podPressRSSContent = '';
			$showmp3player = false;
			$showvideopreview = false;
			$showvideoplayer = false;
			$podPressTemplateData = array();

			$podPressTemplateData['showDownloadText'] = $this->settings['contentDownloadText'];
			$podPressTemplateData['showDownloadStats'] = $this->settings['contentDownloadStats'];
			$podPressTemplateData['showDuration'] = $this->settings['contentDuration'];
			$this->playerCount++;
			$podPressTemplateData['files'] = array();
			$podPressTemplateData['player'] = array();
			reset($post->podPressMedia);
			while (list($key) = each($post->podPressMedia)) {
				if(empty($post->podPressMedia[$key]['previewImage'])) {
					$post->podPressMedia[$key]['previewImage'] = $this->settings['videoPreviewImage'];
				}

				$supportedVideoTypes = array('video_mp4', 'video_m4v', 'video_mov', 'video_qt', 'video_avi', 'video_mpg', 'video_asf', 'video_wmv', 'video_flv', 'video_swf', 'video_ogv');
				if (TRUE == $this->settings['disableVideoPreview'] AND TRUE == in_array($post->podPressMedia[$key]['type'], $supportedVideoTypes)) {
					$post->podPressMedia[$key]['disablePreview'] = true;
				} 
				
				if ( TRUE == isset($post->podPressMedia[$key]['feedonly']) AND 'on' == $post->podPressMedia[$key]['feedonly'] ) {
					continue;
				}
				$post->podPressMedia[$key]['title'] = htmlentities(stripslashes($post->podPressMedia[$key]['title']), ENT_QUOTES, get_option('blog_charset'));
				$post->podPressMedia[$key]['stats'] = false;
				if($this->settings['enableStats']) {
					$pos = strrpos($post->podPressMedia[$key]['URI'], '/');
					//$len = strlen($post->podPressMedia[$key]['URI']);
					while(substr($post->podPressMedia[$key]['URI'], $pos, 1) == '/') {
						$pos++;
					}
					$filename = substr($post->podPressMedia[$key]['URI'], $pos);
					if($this->settings['statLogging'] == 'Full' || $this->settings['statLogging'] == 'FullPlus') {
						$where = $this->wherestr_to_exclude_bots('', 'AND');
						$query_string="SELECT method, COUNT(DISTINCT id) as downloads FROM ".$wpdb->prefix."podpress_stats WHERE postID='".$post->ID."' AND media='".urlencode($filename)."' ".$where."GROUP BY method ORDER BY method ASC";
						$stats = $wpdb->get_results($query_string);
						if (0 < count($stats)) {
							$feed = intval($stats[0]->downloads);
							$play = intval($stats[1]->downloads);
							$web = intval($stats[2]->downloads);
							$post->podPressMedia[$key]['stats'] = array('feed'=>$feed, 'web'=>$web, 'play'=>$play, 'total'=>($feed+$play+$web));
						}
					} else {
						$sql = "SELECT * FROM ".$wpdb->prefix."podpress_statcounts WHERE media = '".urlencode($filename)."'";
						$stats = $wpdb->get_results($sql);
						if($stats) {
							$post->podPressMedia[$key]['stats'] = array('feed'=>intval($stats[0]->feed), 'web'=>intval($stats[0]->web), 'play'=>intval($stats[0]->play), 'total'=>intval($stats[0]->total));
						}
					}
				}
				$supportedMediaTypes = array('audio_mp3', 'audio_ogg', 'audio_m4a', 'audio_mp4', 'audio_m3u', 'video_mp4', 'video_m4v', 'video_mov', 'video_qt', 'video_avi', 'video_mpg', 'video_asf', 'video_wmv', 'audio_wma', 'video_flv', 'video_swf', 'video_ogv', 'ebook_pdf', 'embed_youtube', 'misc_torrent');
				if(!in_array($post->podPressMedia[$key]['type'], $supportedMediaTypes)) {
					$post->podPressMedia[$key]['type'] = 'misc_other';
				}
				
				// this loop is for the basics. After this the unauthorized content will stop
				if(empty($post->podPressMedia[$key]['title'])) {
					$post->podPressMedia[$key]['title'] = podPress_defaultTitles($post->podPressMedia[$key]['type']);
				}
				
				if ( '##Global##' == $post->podPressPostSpecific['itunes:author'] ) {
					Global $podPress;
					if (empty($podPress->settings['iTunes']['author'])) {
						$post->podPressMedia[$key]['artist'] = get_bloginfo('blogname');
					} else {
						$post->podPressMedia[$key]['artist'] = $podPress->settings['iTunes']['author'];
					}
				} else {
					$post->podPressMedia[$key]['artist'] = $post->podPressPostSpecific['itunes:author'];
				}
				
				if($this->settings['contentImage'] != 'none') {
					$post->podPressMedia[$key]['image'] = $post->podPressMedia[$key]['type'].'_'.$this->settings['contentImage'].'.png';
				}

				if($post->podPressMedia[$key]['authorized']) {
					$post->podPressMedia[$key]['URI_orig'] = $post->podPressMedia[$key]['URI'];
					$post->podPressMedia[$key]['URI'] = $this->convertPodcastFileNameToWebPath($post->ID, $key, $post->podPressMedia[$key]['URI'], 'web');
					$post->podPressMedia[$key]['URI_Player'] = $this->convertPodcastFileNameToWebPath($post->ID, $key, $post->podPressMedia[$key]['URI_orig'], 'play');
					if(!empty($post->podPressMedia[$key]['URI_torrent'])) {
						$post->podPressMedia[$key]['URI_torrent'] = $this->convertPodcastFileNameToWebPath($post->ID, $key, $post->podPressMedia[$key]['URI_torrent'], 'web');
					}

					if($this->settings['contentDownload'] == 'disabled') {
						$post->podPressMedia[$key]['enableDownload'] = false;
						$post->podPressMedia[$key]['enableTorrentDownload'] = false;
					} else {
						$post->podPressMedia[$key]['enableDownload'] = true;
						$podPressRSSContent .= '<a href="'.$post->podPressMedia[$key]['URI'].'">'.__('Download', 'podpress').' '.__($post->podPressMedia[$key]['title'], 'podpress').'</a><br/>';
						if($this->settings['enableTorrentCasting'] && !empty($post->podPressMedia[$key]['URI_torrent'])) {
							$post->podPressMedia[$key]['enableTorrentDownload'] = true;
						}
					}
					switch($this->settings['contentPlayer']) {
						case 'disabled':
							$post->podPressMedia[$key]['enablePlayer'] = false;
							$post->podPressMedia[$key]['enablePopup'] = false;
							break;
						case 'inline':
							$post->podPressMedia[$key]['enablePlayer'] = true;
							$post->podPressMedia[$key]['enablePopup'] = false;
							break;
						case 'popup':
							$post->podPressMedia[$key]['enablePlayer'] = false;
							$post->podPressMedia[$key]['enablePopup'] = true;
							break;
						case 'both':
							$post->podPressMedia[$key]['enablePlayer'] = true;
							$post->podPressMedia[$key]['enablePopup'] = true;
						default:
					}
					if($this->settings['contentHidePlayerPlayNow'] == 'disabled') {
						$post->podPressMedia[$key]['enablePlaylink'] = FALSE;
					} else {
						$post->podPressMedia[$key]['enablePlaylink'] = TRUE;
					}
					
					if($post->podPressMedia[$key]['enablePlayer']) {
						// This loop is to put together the player data.
						switch($post->podPressMedia[$key]['type']) {
							case 'audio_mp3':
								$post->podPressMedia[$key]['dimensionW'] = 290;
								$post->podPressMedia[$key]['dimensionH'] = 24;
								//~ $post->podPressMedia[$key]['dimensionW'] = 300;
								//~ $post->podPressMedia[$key]['dimensionH'] = 30;
								break;
							case 'audio_ogg':
							case 'audio_m4a':
							case 'audio_mp4':
							case 'audio_wma':
							case 'video_ogv':
							case 'video_m4v':
							case 'video_mp4':
							case 'video_mov':
							case 'video_qt':
							case 'video_avi':
							case 'video_mpg':
							case 'video_asf':
							case 'video_wmv':
							case 'video_flv':
							case 'video_swf':
								break;
							case 'embed_youtube':
								$x = parse_url($post->podPressMedia[$key]['URI_orig']);
								$x = explode('&', $x['query']);
								foreach($x as $v) {
									if(substr($v, 0, 2) == 'v=') {
										if(str_replace('/', '', $post->podPressMedia[$key]['previewImage']) == str_replace('/', '', $this->settings['videoPreviewImage'])) {
											$post->podPressMedia[$key]['previewImage'] = 'http://img.youtube.com/vi/'. substr($v, 2).'/default.jpg';
										}
										$post->podPressMedia[$key]['URI_Player'] = substr($v, 2).'.youtube';
										break;
									}
								}
								$post->podPressMedia[$key]['URI'] = $post->podPressMedia[$key]['URI_orig'];
								break;
							case 'audio_m3u':
								$post->podPressMedia[$key]['enableDownload'] = true;
							case 'ebook_pdf':
							default:
								$post->podPressMedia[$key]['enablePlayer'] = false;
								$post->podPressMedia[$key]['enablePopup'] = false;
						}
					}
				}
				if( TRUE == isset($post->podPressMedia[$key]['disablePlayer']) AND TRUE === $post->podPressMedia[$key]['disablePlayer']) {
					$post->podPressMedia[$key]['enablePlayer'] = false;
					$post->podPressMedia[$key]['enablePopup'] = false;
				} 

				$podPressTemplateData['files'][] = $post->podPressMedia[$key];
				$post->podPressMedia[$key]['URI'] = $post->podPressMedia[$key]['URI_orig'];
				unset($post->podPressMedia[$key]['URI_orig']);
			}

			if(is_feed()) {
				return str_replace($this->podcastTag, '<br/>'.$podPressRSSContent, $content);
			}

			if(!$this->settings['compatibilityChecks']['wp_head']) {
				$podPressContent = '<code>'.__('podPress theme compatibility problem. Please check podPress->General Settings for more information.', 'podpress').'</code><br/>';
				$this->settings['compatibilityChecks']['wp_head'] = false;
				$this->settings['compatibilityChecks']['wp_footer'] = false;
				podPress_update_option('podPress_config', $this->settings);
			} else {
				/* The theme file needs to populate these */
				$podPressContent = podPress_webContent($podPressTemplateData);
			}
			if ( FALSE !== stripos($content, $podpressTag_in_the_content) ) {
				return str_replace($podpressTag_in_the_content, $podPressContent, $content);
			} else {
				return str_replace($this->podcastTag, $podPressContent, $content);
			}
		}

		function xmlrpc_post_addMedia($input) {
			$postdata = $input['postdata'];
			$content_struct = $input['content_struct'];
			if(isset($content_struct['enclosure']) && !empty($content_struct['enclosure']['url'])) {
				$media[0]['URI'] = $content_struct['enclosure']['url'];
				$media[0]['authorized'] = true;
				if(!empty($content_struct['enclosure']['type'])) {
					$media[0]['type'] = $content_struct['enclosure']['type'];
				} else {
					$media[0]['type'] = podPress_mimetypes(podPress_getFileExt($content_struct['enclosure']['url']));
				}
				if($media[0]['type'] == 'video/x-ms-wmv') {
					$media[0]['type'] = 'video/wmv';
				} elseif($media[0]['type'] == 'video/x-flv') {
					$media[0]['type'] = 'video/flv';
				}					
				$media[0]['type'] = str_replace('/', '_', $media[0]['type']);

				if(!empty($content_struct['enclosure']['duration'])) {
					$media[0]['duration'] =$content_struct['enclosure']['duration'];
				} else {
					$media[0]['duration'] = 0;
				}
				if(!empty($content_struct['enclosure']['size'])) {
					$media[0]['size'] = $content_struct['enclosure']['size'];
				} else {
						$media[0]['size'] = 0;
				}
				if(!empty($content_struct['enclosure']['title'])) {
					$media[0]['title'] = $content_struct['enclosure']['title'];
				}
				if(!empty($content_struct['enclosure']['previewImage'])) {
					$media[0]['previewImage'] = $content_struct['enclosure']['previewImage'];
				}
				if(!empty($content_struct['enclosure']['rss'])) {
						$media[0]['rss'] = $content_struct['enclosure']['rss'];
				} else {
					$media[0]['rss'] = true;
				}	

				delete_post_meta($postdata['ID'], 'podPressMedia');
				podPress_add_post_meta($postdata['ID'], 'podPressMedia', $media, true) ;
			}
			return true;
		}
		
		/**
		* wherestr_to_exclude_bots - builds a WHERE string to exclude bot IP addresses and user agents from the statistic output
		*
		* @package podPress
		* @since 8.8.5 beta 3
		*
		* @param str $tablename [optional] - If you want to add the WHERE string to a query with data from two or more db tables then you should use this parameter to add the name of stats table.
		* @param str $begin_phrase [optional] - If you have already a WHERE condition and you want to add this WHERE string to a query then you can use e.g. AND or OR instead of WHERE which is the default
		*
		* @return str WHERE string with one space at the end or an empty string
		*/
		function wherestr_to_exclude_bots($tablename = '', $begin_phrase='WHERE') {
			$botdb = get_option('podpress_botdb');
			if (FALSE == empty($tablename)) {
				$tablename = rtrim($tablename, '.');
				$tablename .= '.';
			}
			$begin_phrase = trim($begin_phrase);
			if (is_array($botdb) AND 0 < count($botdb)) {
				if (is_array($botdb['fullbotnames']) AND 1 < count($botdb['fullbotnames'])) {
					$where = $begin_phrase." ".$tablename."user_agent NOT IN ('". implode("', '", $botdb['fullbotnames'])."') ";
				} elseif ( is_array($botdb['fullbotnames']) AND 1 == count($botdb['fullbotnames']) ) {
					$where = $begin_phrase." ".$tablename."user_agent NOT IN ('". $botdb['fullbotnames'][0]."') ";
				} else {
					$where = '';
				}
				if ($where != '') {
					$and_or_where = 'AND';
				} else {
					$and_or_where = $begin_phrase;
				}
				if (is_array($botdb['IP']) AND 1 < count($botdb['IP'])) {
					$where .= $and_or_where." ".$tablename."remote_ip NOT IN ('". implode("', '", $botdb['IP'])."') ";
				} elseif ( is_array($botdb['IP']) AND 1 == count($botdb['IP']) ) {
					$where .= $and_or_where." ".$tablename."remote_ip NOT IN ('". $botdb['IP'][0]."') ";
				} else {
					$where .= '';
				}
			} else {
				$where = '';
			}
			return $where;
		}
		
		/**
		* cleanup_itunes_keywords - cleans the iTunes:Keywords up and gives a string back which contains max. 8 comma separated words. This string is escaped with strip_tags and htmlspecialchars and ready for storing it in a DB.
		*
		* @package podPress
		* @since 8.8.5 beta 3
		*
		* @param str $rawstring - The raw input string.
		* @param str $blog_charset [optional] - should be a PHP conform charset string like UTF-8 
		*
		* @return str clean keyword string
		*/
		function cleanup_itunes_keywords($rawstring='', $blog_charset='') {
			if ( FALSE === empty($rawstring) ) {
				$tmpstring = strip_tags(trim($rawstring));
				$tmpstring_parts = explode(',', $tmpstring);
				$i=0;
				foreach ($tmpstring_parts as $tmpstring_part) {
					$string_parts[] = trim($tmpstring_part);
					if ( 7 == $i ) {
						break;
					}
					$i++;
				}
				if ( FALSE === empty($blog_charset) ) {
					return htmlspecialchars(implode(', ', $string_parts), ENT_QUOTES, $blog_charset);
				} else {
					return htmlspecialchars(implode(', ', $string_parts), ENT_QUOTES);
				}
			} else {
				return '';
			}
		}
	}
?>