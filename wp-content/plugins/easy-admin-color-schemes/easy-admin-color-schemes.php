<?php
/*
Plugin Name: Easy Admin Color Schemes
Plugin URI: http://www.jamesdimick.com/creations/easy-admin-color-schemes/
Description: The Easy Admin Color Schemes plugin allows users to easily customize the colors of the administration interface for WordPress. It works by adding a new page to the Settings area in the WordPress admin interface. Users can use the simple form to change the look of the admin interface without needing to know a great deal about WordPress. The plugin generates a new stylesheet file for each color scheme created. This allows for seamless integration with the color scheme functions already available in the WordPress 2.5+ core.
Version: 3.2
Author: James Dimick
Author URI: http://www.jamesdimick.com/

=== VERSION HISTORY ===
  04.18.08 - v1.0 - The first version
  04.20.08 - v1.1 - Changed some URL query variable names in an attempt to prevent clashing with other plugins
  04.21.08 - v1.2 - Changed the way Last Modified dates are handled so the plugin still works instead of erroring out
  04.22.08 - v1.3 - Fixed an issue with slashes in the CSS content when you save a scheme
  04.23.08 - v1.4 - Added a better-looking default color scheme called Washedout
  04.23.08 - v1.5 - Added error codes to aid in debugging and fixed some image issues with the new Washedout color scheme
  04.26.08 - v1.6 - Added a link to the user profile page right by the Admin Color Scheme selector which links to the plugin
  04.26.08 - v1.7 - Fixed the way the new link on the profile page works so only users with proper permissions can see it
  04.29.08 - v1.8 - Corrected some things to allow for better localization
  05.09.08 - v2.0 - Added a new export feature which allows users to export color schemes in a couple different formats
                  |- Also added an upload feature so color schemes that have been exported can be imported back in
                  |- Fixed some issues with setting the current scheme from the plugin page
                  |- Fixed a few spelling errors in various parts of the plugin
                  |- Fixed some problems with the JavaScript
                  |- Fixed a few issues with localization
                  |- Improved the error reporting functions
  05.14.08 - v2.1 - Fixed the major issue some people were having with setting their current scheme from the plugin page
                  |- As a positive side-effect, setting the scheme from the built-in scheme picker on the profile page now works correctly
  06.25.08 - v2.2 - Removed the link on the user profile page until it can be done more reliably
                  |- Also added a new scheme called For the Love of 2.3 which attempts to bring back some of the old WordPress 2.3 styles
  06.28.08 - v2.3 - Fixed an issue with the new For the Love of 2.3 color scheme
                  |- Moved the update preview button to just above the preview window
                  |- Added a note just below the primary colors area in an attempt to relieve some confusion many have been having
                  |- Updated the included screenshot to include the new changes
  06.28.08 - v2.4 - Fixed (hopefully) the issue with the last modified dates
  08.18.08 - v2.5 - Fixed some issues caused by WordPress 2.6 including an issue with the For the Love of 2.3 scheme
                  |- Also updated the Washedout color scheme a bit
  08.21.08 - v2.6 - Fixed an issue with exporting color schemes with Method 2 of the export functionality
                  |- Fixed a few minor issues with the localization parts of the plugin
                  |- Also added a small bit to the Right Now section on the dashboard
  08.22.08 - v2.7 - Fixed (really this time) the issue with exporting color schemes with Method 2 of the export functionality
  01.22.09 - v3.0 - Updated the plugin interface to fit in better with the new WordPress 2.7 look and be more intuitive
                  |- Added collapsible sections to help with overall plugin ease of use
                  |- Added a toggle button for the live preview section
                  |- Removed the Washedout color scheme because it is obsolete with the new default gray color scheme of 2.7
                  |- Removed the For the Love of 2.3 scheme because it is too difficult to maintain with the constant changes in WordPress
                  |- Added two new color schemes called Red and Green which are variations of the default scheme of 2.7
                  |- Now allowing more special characters in the color scheme names and also scheme names up to 200 characters
                  |- Also now allowing editing of color scheme names
                  |- Added a Copy action which will copy the selected color scheme into the Create a Color Scheme form
                  |- Can now export the default WordPress color schemes as well
                  |- Now using a new and improved color picker
                  |- Completely removed previously commented-out code to save on the overall file size
                  |- Fixed a few small bugs here and there
                  |- Updated the included readme, screenshot, and .POT file to include the new changes
  06.17.09 - v3.1 - Fixed an issue with the last modified times
                  |- Added Russian translation thanks to fatcow (http://www.fatcow.com/)
  07.25.09 - v3.2 - Fixed (hopefully) the image display problems in the lower preview pane when adding/editing a color scheme
                  |- Fixed a bug in the last modified times function
                  |- Replaced some of the old JavaScript with jQuery code of similar functionality
                  |- Added a new Preview function which opens a preview of the selected color scheme in a lightbox
                  |- Added some more in-depth permissions functionality
                  |- Added the ability for the primary colors to actually affect the color scheme
                  |- Added the ability to set a default color scheme which will affect all new users
                  |- Added the ability to force a certain color scheme on all users regardless of what they choose
                  |- Updated the included readme, screenshot, and .POT file to include the new changes

=== LEGAL INFORMATION ===
  Copyright (C) 2009 James Dimick <mail@jamesdimick.com> - www.JamesDimick.com

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

$plugpageurl = get_option('siteurl').'/wp-admin/options-general.php?page=easy-admin-color-schemes';
$eacs_domain = 'EasyAdminColorSchemes';
load_plugin_textdomain($eacs_domain, 'wp-content/plugins/easy-admin-color-schemes');
add_action('init', 'eacs_init');

function eacs_init() {
	add_action('admin_init', 'eacs_css_files');
	add_action('admin_print_styles', 'eacs_force_scheme');
	add_action('admin_print_styles', 'eacs_scheme_preview');
	add_action('admin_print_styles', 'eacs_scheme_iframe');
	add_action('user_register', 'eacs_default_scheme', 1000, 1);
	add_filter('plugin_action_links', 'eacs_filter_plugin_actions', 10, 2);

	function eacs_filter_plugin_actions($links, $file) {
		static $this_plugin;
		if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
		if($file == $this_plugin) {
			$settings_link = '<a href="options-general.php?page=easy-admin-color-schemes">'.__('Settings').'</a>';
			array_unshift($links, $settings_link);
		}
		return($links);
	}

	add_action('admin_menu', 'eacs_add_settings_page');

	if($_GET['page'] == 'easy-admin-color-schemes' || $_GET['page'] == 'easy-admin-color-schemes.php') {
		add_action('admin_init', 'eacs_process_request');
		add_action('admin_head', 'eacs_admin_head');
		wp_enqueue_script('postbox');
		add_thickbox();
	}
}

function eacs_error($code = 0) {
	global $eacs_domain;
	if(preg_match('/^(1|2|3|4|5|6|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|33)$/', $code)) {
		$msg = __('A fatal error occurred while trying to interact with needed files and directories. This could mean your server is not configured properly and/or the files and directories are not writable&hellip;', $eacs_domain);
	} elseif(preg_match('/^(25|27|32)$/', $code)) {
		$msg = __('The specified action/method is invalid!', $eacs_domain);
	} elseif(preg_match('/^(7|8|26|29|31)$/', $code)) {
		$msg = __('The specified admin color scheme does not exist!', $eacs_domain);
	} elseif($code === 28) {
		$msg = __('A fatal error occurred while trying to set the current admin color scheme on the specified profile.', $eacs_domain);
	} elseif($code === 30) {
		$msg = __('A fatal error occurred while trying to delete the specified admin color scheme.', $eacs_domain);
	} else {
		$msg = __('An unknown fatal error occurred!', $eacs_domain);
		$code = 0;
	}

	wp_die(sprintf(__('<big>Fatal Error!</big><br />%1$s<br /><code>&mdash; Easy Admin Color Schemes [Error Code: %2$d]</code>', $eacs_domain), $msg, $code));
}

function eacs_return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	switch($last) {
		case 'g':
		$val *= 1024;
		case 'm':
		$val *= 1024;
		case 'k':
		$val *= 1024;
	}
	return($val);
}

function eacs_current_user_can_manip() {
	global $userdata;
	get_currentuserinfo();
	$manip_role_orig = get_option('eacs_manip_role');
	$manip_users_orig = get_option('eacs_manip_users');
	$manip_role = (isset($manip_role_orig) && is_numeric($manip_role_orig)) ? (int)$manip_role_orig : 10;
	$manip_users = (isset($manip_users_orig) && is_array($manip_users_orig)) ? $manip_users_orig : array();
	if($userdata->user_level >= $manip_role || in_array($userdata->ID, $manip_users) || $userdata->ID === '1') {
		return(true);
	} else {
		return(false);
	}
}

function eacs_default_scheme($user_id) {
	global $_wp_admin_css_colors;
	$default = get_option('eacs_default');
	if(isset($default) && $default !== '0' && $_wp_admin_css_colors[$default]) {
		update_user_option($user_id, 'admin_color', $default, true);
	}
}

function eacs_force_scheme() {
	global $_wp_admin_css_colors;
	$force = get_option('eacs_force');
	if(isset($force) && $force !== 'none' && $_wp_admin_css_colors[$force] && !isset($_GET['eacsprev']) && !isset($_GET['eacsiframe'])) {
		wp_deregister_style('colors');
		wp_enqueue_style('color', $_wp_admin_css_colors[$force]->url);
	}
}

function eacs_process_request() {
	if(isset($_POST['eacsubmit']) || isset($_POST['eacsiesubmit']) || isset($_GET['eacsaction']) && $_GET['eacsaction'] != 'edit' && $_GET['eacsaction'] != 'export') {
		global $wpdb, $_wp_admin_css_colors, $userdata, $plugpageurl;
		get_currentuserinfo();
		if(eacs_css_files() === false) eacs_error(1); else $cssfiles = eacs_css_files();
		foreach($_wp_admin_css_colors as $color => $color_info) { $parsedurl = parse_url($color_info->url); $allschemes[$color] = array('name' => $color_info->name, 'url' => $color_info->url, 'path' => $_SERVER['DOCUMENT_ROOT'].$parsedurl['path'], 'colors' => $color_info->colors, 'contents' => @file_get_contents($_SERVER['DOCUMENT_ROOT'].$parsedurl['path'])); }

		function isempty($var) {
			$var = trim($var);
			if(!isset($var) || empty($var)) return true; else return false;
		}

		if(isset($_POST['eacsubmit']) || isset($_POST['eacsiesubmit'])) {
			$schemedir = ABSPATH.PLUGINDIR.'/easy-admin-color-schemes/schemes/';
			$action = trim($_POST['eacsaction']);
			$ieaction = trim($_POST['eacsieaction']);
			$schemename = trim(stripslashes($_POST['schemename']));
			$cleanschemename = sanitize_title($schemename);
			$schemefile = $schemedir.$cleanschemename.'.css';
			$priclrtxt1 = trim($_POST['priclrtxt1']);
			$priclrtxt2 = trim($_POST['priclrtxt2']);
			$priclrtxt3 = trim($_POST['priclrtxt3']);
			$priclrtxt4 = trim($_POST['priclrtxt4']);
			$schemecss = trim(stripslashes($_POST['schemecss']));
			$exportmethod = trim($_POST['exportmethod']);
			$importcssfile = trim($_POST['importcssfile']);
			$importsizemax = eacs_return_bytes(ini_get('upload_max_filesize'));
			$exporthiddename = trim($_POST['eacsiename']);

			if(!is_writable($schemedir)) eacs_error(2);

			if($action == 'add' || $action == 'edit') {
				if(isempty($schemename) && isempty($priclrpck1) && isempty($priclrpck2) && isempty($priclrpck3) && isempty($priclrpck4) && isempty($schemecss)) {
					wp_redirect($plugpageurl.'&msg=7'); exit;
				}
			}

			if($action == 'add') {
				if(eacs_current_user_can_manip()) {
					if(!file_exists($schemefile)) {
						if(!$cssfiles[$cleanschemename] && !$_wp_admin_css_colors[$cleanschemename]) {
							if(preg_match('/^.{3,200}$/i', $schemename) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt1) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt2) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt3) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt4)) {
								$nfc = "/*\r\n".'Scheme Name: '.$schemename."\r\n".'Primary Colors: '.str_replace('#', '', $priclrtxt1).'|'.str_replace('#', '', $priclrtxt2).'|'.str_replace('#', '', $priclrtxt3).'|'.str_replace('#', '', $priclrtxt4)."\r\n*/\r\n".$schemecss;
								$sf = @fopen($schemefile, 'wb') or eacs_error(3);
								if(fwrite($sf, $nfc) === false) eacs_error(4);
								@fclose($sf);
								wp_redirect($plugpageurl.'&msg=1'); exit;
							} else {
								wp_redirect($plugpageurl.'&msg=9'); exit;
							}
						} else {
							wp_redirect($plugpageurl.'&msg=4'); exit;
						}
					} else {
						wp_redirect($plugpageurl.'&msg=4'); exit;
					}
				}
			} elseif($action == 'edit') {
				if(eacs_current_user_can_manip()) {
					$hiddencurname = trim($_POST['eacsname']);
					$schemefilebase = basename($cssfiles[$hiddencurname]['file']);
					$currschemefile = $schemedir.$schemefilebase;
					if(file_exists($currschemefile)) {
						if($cssfiles[$hiddencurname]) {
							if(preg_match('/^.{3,200}$/i', $schemename) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt1) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt2) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt3) && preg_match('/^\#[a-fA-F0-9]{6}$/i', $priclrtxt4)) {
								$nfc = "/*\r\n".'Scheme Name: '.$schemename."\r\n".'Primary Colors: '.str_replace('#', '', $priclrtxt1).'|'.str_replace('#', '', $priclrtxt2).'|'.str_replace('#', '', $priclrtxt3).'|'.str_replace('#', '', $priclrtxt4)."\r\n*/\r\n".$schemecss;
								$sf = @fopen($currschemefile, 'wb') or eacs_error(5);
								if(fwrite($sf, $nfc) === false) eacs_error(6);
								@fclose($sf);
								if($currschemefile !== $schemefile) { if(@rename($currschemefile, $schemefile) === false) { eacs_error(1); } }
								wp_redirect($plugpageurl.'&eacsaction=edit&eacsid='.$schemename.'&msg=2'); exit;
							} else {
								wp_redirect($plugpageurl.'&msg=9'); exit;
							}
						} else {
							eacs_error(7);
						}
					} else {
						eacs_error(8);
					}
				}
			} elseif($ieaction == 'import') {
				if(eacs_current_user_can_manip()) {
					if(!empty($_FILES['importcssfile']) && $_FILES['importcssfile']['error'] == 0) {
						$filename = basename($_FILES['importcssfile']['name']);
						$filecontent = @file_get_contents($_FILES['importcssfile']['tmp_name']) or eacs_error(9);
						$ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
						if($ext == 'css' && $_FILES['importcssfile']['type'] == 'text/css' && $_FILES['importcssfile']['size'] < $importsizemax) {
							$newname = $schemedir.$filename;
							if(!file_exists($newname)) {
								if(preg_match('/^\/\*[\n\r]+Scheme Name: (.{3,200})[\n\r]+Primary Colors: ([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\n\r]+\*\/(.+)$/is', $filecontent)) {
									if(move_uploaded_file($_FILES['importcssfile']['tmp_name'], $newname)) {
										wp_redirect($plugpageurl.'&msg=10'); exit;
									} else {
										wp_redirect($plugpageurl.'&msg=11'); exit;
									}
								} else {
									wp_redirect($plugpageurl.'&msg=12'); exit;
								}
							} else {
								wp_redirect($plugpageurl.'&msg=13'); exit;
							}
						} else {
							wp_redirect($plugpageurl.'&msg=14'); exit;
						}
					} else {
						wp_redirect($plugpageurl.'&msg=15'); exit;
					}
				}
			} elseif($ieaction == 'export') {
				if($allschemes[$exporthiddename]) {
					@clearstatcache();
					$current_scheme = $allschemes[$exporthiddename];
					$cssname = sanitize_title($current_scheme['name']);
					$cssfilebase = basename($current_scheme['url']);
					$cssfile = $current_scheme['path'];
					$filesize = @filesize($cssfile) or eacs_error(10);
					$filecont = trim($current_scheme['contents']);
					if(!preg_match('/^\/\*[\n\r]+Scheme Name: (.{3,200})[\n\r]+Primary Colors: ([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\n\r]+\*\//is', $filecont)) { $filecontent = "/*\r\n".'Scheme Name: '.$current_scheme['name']."\r\n".'Primary Colors: '.str_replace('#', '', $current_scheme['colors'][0]).'|'.str_replace('#', '', $current_scheme['colors'][1]).'|'.str_replace('#', '', $current_scheme['colors'][2]).'|'.str_replace('#', '', $current_scheme['colors'][3])."\r\n*/\r\n".$filecont; } else { $filecontent = $filecont; }
					if($exportmethod == 'method1') {
						header('Pragma: public');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Cache-Control: private', false);
						header('Content-Transfer-Encoding: binary');
						header('Content-Type: text/css');
						header('Content-Length: '.$filesize);
						header('Content-Disposition: attachment; filename="'.$cssname.'.css";');
						echo($filecontent); exit;
					} elseif($exportmethod == 'method2') {
						if(!empty($_ENV['TMP'])) $tempdir = $_ENV['TMP']; elseif(!empty($_ENV['TMPDIR'])) $tempdir = $_ENV['TMPDIR']; elseif(!empty($_ENV['TEMP'])) $tempdir = $_ENV['TEMP']; else $tempdir = dirname(tempnam('', 'na'));
						if(empty($tempdir)) eacs_error(13);
						if(is_writable($tempdir) == false) eacs_error(14);
						$tempdir = rtrim($tempdir, '/'); $tempdir .= '/';
						$tmpdir = $tempdir.'EACS'.rand(10000, 99999).'/';
						@mkdir($tmpdir) or eacs_error(15);
						$tmpcssfile = $tmpdir.$cssname.'.css';
						$tmpphpfile = $tmpdir.$cssname.'.php';
						$tmpzipfile = $tmpdir.$cssname.'.zip';
						@chdir($tmpdir) or eacs_error(16);
						$opentmpcssfile = @fopen($tmpcssfile, 'w+') or eacs_error(17);
						$opentmpphpfile = @fopen($tmpphpfile, 'w+') or eacs_error(18);
						$parsedcssfile = trim($filecontent);
						$parsedphpfile = sprintf('<?php'."\n".'/*'."\n".'Plugin Name: Admin Color Scheme: %1$s'."\n".'Description: This plugin adds the <strong>%1$s</strong> admin color scheme. This plugin was generated by the <a href="http://www.jamesdimick.com/creations/easy-admin-color-schemes/">Easy Admin Color Schemes</a> plugin for WordPress.'."\n".'*/'."\n\n".'add_action(\'admin_init\', \'eacs_admin_color_scheme_%3$s\');'."\n\n".'function eacs_admin_color_scheme_%3$s() {'."\n\t".'$plugin_url = get_option(\'siteurl\').\'/wp-content/plugins/\'.plugin_basename(dirname(__FILE__));'."\n\t".'wp_admin_css_color(\'%2$s\', \'%1$s\', $plugin_url.\'/%4$s.css\', array(\'%5$s\', \'%6$s\', \'%7$s\', \'%8$s\'));'."\n".'}'."\n".'?>', $current_scheme['name'], sanitize_title($current_scheme['name']), str_replace('-', '', sanitize_title($current_scheme['name'])), $cssname, $current_scheme['colors'][0], $current_scheme['colors'][1], $current_scheme['colors'][2], $current_scheme['colors'][3]);
						$writetmpcssfile = @fwrite($opentmpcssfile, $parsedcssfile) or eacs_error(19);
						$writetmpphpfile = @fwrite($opentmpphpfile, $parsedphpfile) or eacs_error(20);
						if(!class_exists('PclZip')) require_once(ABSPATH.'wp-admin/includes/class-pclzip.php');
						$archive = new PclZip($cssname.'.zip');
						$create = $archive->create($tmpcssfile.','.$tmpphpfile, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_ADD_PATH, $cssname);
						if($create == 0) eacs_error(21);
						$newzipfile = @fopen($tmpzipfile, 'r') or eacs_error(22);
						$newzipfilesize = @filesize($tmpzipfile) or eacs_error(23);
						header('Pragma: public');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Cache-Control: private', false);
						header('Content-Transfer-Encoding: binary');
						header('Content-Type: application/zip');
						header('Content-Length: '.$newzipfilesize);
						header('Content-Disposition: attachment; filename="'.$cssname.'.zip";');
						@readfile($tmpzipfile) or eacs_error(24);
						@unlink($tmpcssfile); @unlink($tmpphpfile); @unlink($tmpzipfile); @rmdir($tmpdir);
						exit;
					} else {
						eacs_error(25);
					}
				} else {
					eacs_error(26);
				}
			} else {
				eacs_error(27);
			}
		} elseif($_GET['eacsaction'] == 'use') {
			if(!$_wp_admin_css_colors[get_option('eacs_force')]) {
				if($_wp_admin_css_colors[$_GET['eacsid']]) {
					if(update_user_option($userdata->ID, 'admin_color', $_GET['eacsid'], true)) {
						wp_redirect($plugpageurl.'&msg=8'); exit;
					} else {
						eacs_error(28);
					}
				} else {
					eacs_error(29);
				}
			} else {
				wp_redirect($plugpageurl.'&msg=16'); exit;
			}
		} elseif($_GET['eacsaction'] == 'delete') {
			if(eacs_current_user_can_manip()) {
				if($cssfiles[$_GET['eacsid']]) {
					@unlink($cssfiles[$_GET['eacsid']]['file']) or eacs_error(30);
					wp_redirect($plugpageurl.'&msg=3'); exit;
				} else {
					eacs_error(31);
				}
			}
		} else {
			eacs_error(32);
		}
	}
}

function eacs_css_files() {
	if(is_user_logged_in()) {
		global $_wp_admin_css_colors;
		@clearstatcache();
		$scheme_dir = ABSPATH.PLUGINDIR.'/easy-admin-color-schemes/schemes/';
		$dir = @opendir($scheme_dir);
		$result = array();

		while($file = readdir($dir)) {
			if($file != '.' || $file != '..') {
				if(preg_match('/^(.+)\.css$/i', $file)) {
					$file_contents = @file_get_contents($scheme_dir.$file);

					if(preg_match('/^\/\*[\n\r]+Scheme Name: (.{3,200})[\n\r]+Primary Colors: ([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\n\r]+\*\/(.+)$/is', $file_contents, $matches)) {
						$clean_title = sanitize_title(trim($matches[1]));

						if(!$_wp_admin_css_colors[$clean_title]) {
							wp_admin_css_color($clean_title, trim($matches[1]), get_option('siteurl').'/'.PLUGINDIR.'/easy-admin-color-schemes/schemes/'.$file, array('#'.$matches[2], '#'.$matches[3], '#'.$matches[4], '#'.$matches[5]));
						}

						if(!$result[$clean_title]) {
							$result[$clean_title] = array('name' => trim($matches[1]), 'file' => $scheme_dir.$file, 'color1' => '#'.$matches[2], 'color2' => '#'.$matches[3], 'color3' => '#'.$matches[4], 'color4' => '#'.$matches[5], 'content' => $matches[6]);
						}
					}
				}
			}
		}

		@closedir($dir);
		$result = (isset($result) && !empty($result) && $result != '') ? $result : false;
		return($result);
	} else {
		return(false);
	}
}

function eacs_scheme_preview() {
	global $_wp_admin_css_colors;

	if($_wp_admin_css_colors[$_GET['eacsprev']]) {
		function insertJS() { ?>
			<script type="text/javascript">
				// <![CDATA[
				jQuery(document).ready(function(){
					jQuery('a').each(function(){
						if(this.href.indexOf('/wp-admin/') >= 0) {
							var split = this.href.split('#');
							var href = split[0] + ((this.href.indexOf('?') <= -1) ? '?' : '&') + 'eacsprev=<?php echo($_GET['eacsprev']) ?>';
							this.href = (split[1] == undefined) ? href : href + '#' + split[1];
						}
					});
				});
				// ]]>
			</script>
		<?php }

		wp_deregister_style('colors');
		wp_enqueue_style('color', $_wp_admin_css_colors[$_GET['eacsprev']]->url);
		add_action('admin_head', 'insertJS');
	}
}

function eacs_scheme_iframe() {
	if(isset($_GET['eacsiframe']) && $_GET['eacsiframe'] === 'true') {
		function insertIframeJS() { ?>
			<script type="text/javascript">
				// <![CDATA[
				jQuery(document).ready(function(){
					jQuery('a').each(function(){
						if(this.href.indexOf('/wp-admin/') >= 0) {
							var split = this.href.split('#');
							var href = split[0] + ((this.href.indexOf('?') <= -1) ? '?' : '&') + 'eacsiframe=true';
							this.href = (split[1] == undefined) ? href : href + '#' + split[1];
						}
					});
				});
				// ]]>
			</script>
		<?php }

		add_action('admin_head', 'insertIframeJS');
	}
}

function eacs_admin_head() {
	global $_wp_admin_css_colors, $eacs_domain; ?>
	<link rel="stylesheet" type="text/css" href="<?php echo(get_option('siteurl').'/'.PLUGINDIR.'/easy-admin-color-schemes/includes/easy-admin-color-schemes.css') ?>" />
	<script type="text/javascript" src="<?php echo(get_option('siteurl').'/'.PLUGINDIR.'/easy-admin-color-schemes/includes/jscolor.js') ?>"></script>
	<script type="text/javascript">
	// <![CDATA[
	function deleteConfirm(sn){return confirm('<?php _e('Are you sure you want to delete the', $eacs_domain) ?> \"' + sn + '\" <?php _e('scheme?', $eacs_domain) ?>\n<?php _e('This action cannot be undone!', $eacs_domain) ?>');}
	function updatePreview() {
		var innerCSS = jQuery('#schemecss').val().replace(/url\((.+)\)/gi, function(m,url){

		var url1=url.replace(/[\"\']*/gi,''),
		url2=url1.replace(/^[\.\/]*/gi,''),
		url3=url2.replace(/^wp\-admin\//gi,'<?php echo(admin_url()) ?>'),
		url4=url3.replace(/^images\//gi,'<?php echo(admin_url()) ?>images/'),
		url5=url4.replace(/^wp\-content\//gi,'<?php echo(get_option('siteurl')) ?>/wp-content/'),
		url6=url5.replace(/^includes\//gi,'<?php echo(get_option('siteurl')) ?>/wp-content/plugins/easy-admin-color-schemes/includes/');
		return'url('+url6+')';

		});
		jQuery('#schemepreview').contents().find('head').find('#colors-css').remove();
		jQuery('#schemepreview').contents().find('head').find('#eacspreviewstyles').remove();
		jQuery('#schemepreview').contents().find('head').append('<style type="text/css" id="eacspreviewstyles">' + innerCSS + '</style>');
	}
	jQuery(document).ready(function(){
		jQuery('#eacsubmit').after('<input type="button" name="schemeprevtog" id="schemeprevtog" value="<?php _e('Toggle&nbsp;Live&nbsp;Preview', $eacs_domain) ?>" class="button-secondary bold" title="<?php _e('Click to toggle the live preview', $eacs_domain) ?>" />');
		jQuery('#schemeprevtog').after('<div id="eacs-live-preview" style="display:none"><h4 class="spechead2"><?php _e('Live Preview', $eacs_domain) ?> <input type="button" name="schemeprevupd" id="schemeprevupd" value="<?php _e('&darr;&nbsp;Update', $eacs_domain) ?>" onclick="updatePreview();return false;" class="button-secondary" title="<?php _e('Click to update the live preview below', $eacs_domain) ?>" /></h4><iframe name="schemepreview" id="schemepreview" src="<?php echo(get_option('siteurl').'/wp-admin/') ?>?eacsiframe=true" onload="updatePreview();return false;"><?php _e('Scheme Preview', $eacs_domain) ?></iframe><p style="text-align:center;padding:0;margin:3px 0 0 0"><small class="nonessential"><?php _e('In some cases images in the live preview may not appear correctly&hellip;', $eacs_domain) ?></small></p></div>');
		if(location.hash != '#scheme-input'){jQuery('#eacs-inside-one').hide();}
		if(location.hash != '#import-export'){jQuery('#eacs-inside-two').hide();}
		if(location.hash != '#plugin-settings'){jQuery('#eacs-inside-three').hide();}
		jQuery('#eacs-handlediv-one').click(function(){jQuery('#eacs-inside-one').slideToggle('normal');});
		jQuery('#eacs-hndle-one').click(function(){jQuery('#eacs-inside-one').slideToggle('normal');});
		jQuery('#eacs-handlediv-two').click(function(){jQuery('#eacs-inside-two').slideToggle('normal');});
		jQuery('#eacs-hndle-two').click(function(){jQuery('#eacs-inside-two').slideToggle('normal');});
		jQuery('#eacs-handlediv-three').click(function(){jQuery('#eacs-inside-three').slideToggle('normal');});
		jQuery('#eacs-hndle-three').click(function(){jQuery('#eacs-inside-three').slideToggle('normal');});
		jQuery('#eacs-handlediv-four').click(function(){jQuery('#eacs-inside-four').slideToggle('normal');});
		jQuery('#eacs-hndle-four').click(function(){jQuery('#eacs-inside-four').slideToggle('normal');});
		jQuery('#schemeprevtog').click(function(){jQuery('#eacs-live-preview').slideToggle('normal');});
		var priclrtxt1cur = jQuery('#priclrtxt1').val().replace(/^[^#]{1,1}[^a-fA-F0-9]{1,6}$/gi, '');
		var priclrtxt2cur = jQuery('#priclrtxt2').val().replace(/^[^#]{1,1}[^a-fA-F0-9]{1,6}$/gi, '');
		var priclrtxt3cur = jQuery('#priclrtxt3').val().replace(/^[^#]{1,1}[^a-fA-F0-9]{1,6}$/gi, '');
		var priclrtxt4cur = jQuery('#priclrtxt4').val().replace(/^[^#]{1,1}[^a-fA-F0-9]{1,6}$/gi, '');
		function changeColor() {
			var theCSS = jQuery('#schemecss').val();
			var theColor = jQuery(this).val().replace(/^[^#]{1,1}[^a-fA-F0-9]{1,6}$/gi, '');
			var theOldColor = (jQuery(this).attr('id') == 'priclrtxt1') ? new RegExp(priclrtxt1cur, 'gi') : (jQuery(this).attr('id') == 'priclrtxt2') ? new RegExp(priclrtxt2cur, 'gi') : (jQuery(this).attr('id') == 'priclrtxt3') ? new RegExp(priclrtxt3cur, 'gi') : (jQuery(this).attr('id') == 'priclrtxt4') ? new RegExp(priclrtxt4cur, 'gi') : null;
			var replacedCSS = theCSS.replace(theOldColor, theColor);
			var correctReplacedCSS = replacedCSS.replace(/url\((.+)\)/gi, function(m,url){var url1=url.replace(/[\"\']*/gi,''),url2=url1.replace(/^[\.\/]*/gi,''),url3=url2.replace(/^wp\-admin\//gi,'<?php echo(admin_url()) ?>'),url4=url3.replace(/^images\//gi,'<?php echo(admin_url()) ?>/images/'),url5=url4.replace(/^wp\-content\//gi,'<?php echo(get_option('siteurl')) ?>/wp-content/'),url6=url5.replace(/^includes\//gi,'<?php echo(get_option('siteurl')) ?>/wp-content/plugins/easy-admin-color-schemes/includes/');return'url('+url6+')';});
			jQuery('#schemecss').val(replacedCSS);
			if(jQuery(this).attr('id')=='priclrtxt1'){priclrtxt1cur=theColor;}else if(jQuery(this).attr('id')=='priclrtxt2'){priclrtxt2cur=theColor;}else if(jQuery(this).attr('id')=='priclrtxt3'){priclrtxt3cur=theColor;}else if(jQuery(this).attr('id')=='priclrtxt4'){priclrtxt4cur=theColor;}
			jQuery('#schemepreview').contents().find('head').find('#eacspreviewstyles').remove();
			jQuery('#schemepreview').contents().find('head').append('<style type="text/css" id="eacspreviewstyles">' + correctReplacedCSS + '</style>');
		}
		jQuery('#priclrtxt1').change(changeColor);jQuery('#priclrtxt1').keypress(changeColor);
		jQuery('#priclrtxt2').change(changeColor);jQuery('#priclrtxt2').keypress(changeColor);
		jQuery('#priclrtxt3').change(changeColor);jQuery('#priclrtxt3').keypress(changeColor);
		jQuery('#priclrtxt4').change(changeColor);jQuery('#priclrtxt4').keypress(changeColor);
	});
	// ]]>
	</script>
<?php }

function eacs_add_settings_page() {
	global $eacs_domain;
	function eacs_settings_page() {
		global $wpdb, $wp_roles, $userdata, $_wp_admin_css_colors, $plugpageurl, $eacs_domain;
		$cssfiles = eacs_css_files();
		$default_scheme = @file_get_contents(ABSPATH.'/wp-admin/css/colors-fresh.css') or eacs_error(33);
		$current_scheme = $cssfiles[$_GET['eacsid']];
		$current_scheme_cleaname = sanitize_title($current_scheme['name']);
		foreach($_wp_admin_css_colors as $color => $color_info) { $parsedurl = parse_url($color_info->url); $allschemes[$color] = array('name' => $color_info->name, 'keyname' => $color, 'url' => $color_info->url, 'colors' => $color_info->colors, 'contents' => @file_get_contents($_SERVER['DOCUMENT_ROOT'].$parsedurl['path'])); }
		$current_scheme_frmall = $allschemes[$_GET['eacsid']];
		$current_scheme_frmall_cleaname = $allschemes[$_GET['eacsid']]['keyname'];
		$scheme_header = (($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) ? __('Edit Color Scheme', $eacs_domain) : __('Create a Color Scheme', $eacs_domain);
		$scheme_headdesc = (($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) ? __('Use the form below to edit the currently selected color scheme.', $eacs_domain) : __('Use this form to create a new color scheme.', $eacs_domain);
		if(($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) { $scheme_name = $current_scheme['name']; } elseif($allschemes[$_GET['eacscopyid']]) { $scheme_name = 'Copy of '.trim($allschemes[$_GET['eacscopyid']]['name']); } else { $scheme_name = 'My Cool Scheme'; }
		$scheme_nameclr = (($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) ? '' : ' onfocus="if(this.value == \'My Cool Scheme\') { this.value=\'\'; }" onblur="if(this.value == \'\') { this.value=\'My Cool Scheme\'; }"';
		if(($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) { $scheme_priclr1 = $current_scheme['color1']; } elseif($allschemes[$_GET['eacscopyid']]) { $scheme_priclr1 = $allschemes[$_GET['eacscopyid']]['colors'][0]; } else { $scheme_priclr1 = '#464646'; }
		if(($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) { $scheme_priclr2 = $current_scheme['color2']; } elseif($allschemes[$_GET['eacscopyid']]) { $scheme_priclr2 = $allschemes[$_GET['eacscopyid']]['colors'][1]; } else { $scheme_priclr2 = '#6D6D6D'; }
		if(($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) { $scheme_priclr3 = $current_scheme['color3']; } elseif($allschemes[$_GET['eacscopyid']]) { $scheme_priclr3 = $allschemes[$_GET['eacscopyid']]['colors'][2]; } else { $scheme_priclr3 = '#F1F1F1'; }
		if(($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) { $scheme_priclr4 = $current_scheme['color4']; } elseif($allschemes[$_GET['eacscopyid']]) { $scheme_priclr4 = $allschemes[$_GET['eacscopyid']]['colors'][3]; } else { $scheme_priclr4 = '#DFDFDF'; }
		if(($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) { $scheme_css = $current_scheme['content']; } elseif($allschemes[$_GET['eacscopyid']]) { $scheme_css = preg_replace('/^\/\*[\n\r]+Scheme Name: (.{3,40})[\n\r]+Primary Colors: ([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\|]([a-fA-F0-9]{6})[\n\r]+\*\/(.+)$/is', '$6', $allschemes[$_GET['eacscopyid']]['contents']); } else { $scheme_css = $default_scheme; }
		$scheme_hiddename = (($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) ? '<input type="hidden" name="eacsname" id="eacsname" value="'.sanitize_title($scheme_name).'" />' : '';
		$scheme_action = (($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) ? 'edit' : 'add';
		$scheme_submitval = (($_GET['eacsaction'] == 'edit' || $_GET['eacsaction'] == 'export') && $current_scheme) ? __('Save Scheme', $eacs_domain) : __('Create Scheme', $eacs_domain);
		$importexport_header = (($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) ? __('Export Color Scheme', $eacs_domain) : __('Import a Color Scheme', $eacs_domain);
		$importexport_headdesc = (($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) ? sprintf(__('Choose the method you would like to use to export the <strong>%s</strong> color scheme.', $eacs_domain), $current_scheme_frmall['name']) : __('Use this form to import a pre-existing color scheme for use with this plugin.', $eacs_domain);
		$import_enctype = (($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) ? '' : ' enctype="multipart/form-data"';
		$import_sizemax = (($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) ? '' : '<input type="hidden" name="MAX_FILE_SIZE" value="'.eacs_return_bytes(ini_get('upload_max_filesize')).'" />';
		$export_hiddename = (($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) ? '<input type="hidden" name="eacsiename" id="eacsiename" value="'.$current_scheme_frmall_cleaname.'" />' : '';
		$importexport_action = (($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) ? 'export' : 'import';
		$importexport_submitval = (($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) ? __('Export Scheme', $eacs_domain) : __('Import Scheme', $eacs_domain);

		function stream_last_modified($url) {
			if(function_exists('version_compare')&&version_compare(phpversion(),'4.3.0')>0){
				if(!($fp=@fopen($url,'r'))){return(NULL);}
				$meta=stream_get_meta_data($fp);
				for($j=0;isset($meta['wrapper_data'][$j]);$j++){if(strstr(strtolower($meta['wrapper_data'][$j]),'last-modified')){$modtime=substr($meta['wrapper_data'][$j],15);break;}}
				fclose($fp);
			}else{
				$parts=parse_url($url);
				$host=$parts['host'];
				$path=$parts['path'];
				if(!($fp=@fsockopen($host,80))){return(NULL);}
				$req="HEAD $path HTTP/1.0\r\nUser-Agent: PHP/".phpversion()."\r\nHost: $host:80\r\nAccept: */*\r\n\r\n";
				fputs($fp,$req);
				while(!feof($fp)){$str=fgets($fp,4096);if(strstr(strtolower($str),'last-modified')){$modtime=substr($str,15);break;}}
				fclose($fp);
			}
			return(isset($modtime)?strtotime($modtime):time());
		}

		function eacs_dropdown_roles($selected = false) {
			global $wp_roles;
			$p = '';
			$r = '';
			$editable_roles = get_editable_roles();
			foreach($editable_roles as $role => $details) {
				$name = translate_user_role($details['name']);
				$levels = '';
				foreach($details['capabilities'] as $cap => $val) { if(preg_match('/^level\_[0-9]{1,2}$/i', $cap)) $levels[] = str_replace('level_', '', $cap); }
				if($selected == $levels[0])
					$p = '<option selected="selected" value="'.$levels[0].'">'.$name.'</option>';
				else
					$r .= '<option value="'.$levels[0].'">'.$name.'</option>';
			}
			echo($p.$r);
		}

		if(isset($_GET['msg'])) {
			if($_GET['msg'] == '1') { ?>
				<div id="eacsmsg" class="updated fade"><p><strong><?php _e('Your new color scheme has been added!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '2') { ?>
				<div id="eacsmsg" class="updated fade"><p><strong><?php _e('Your changes to the color scheme have been saved!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '3') { ?>
				<div id="eacsmsg" class="updated fade"><p><strong><?php _e('You have successfully deleted the color scheme!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '4') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('The color scheme you were trying to add already exists!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '5') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('The provided scheme is not a valid color scheme!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '6') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('The action provided is incorrect!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '7') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('Your submission had missing required items!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '8') { ?>
				<div id="eacsmsg" class="updated fade"><p><strong><?php _e('Your current color scheme has been changed!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '9') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('Your submission had invalid required items!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '10') { ?>
				<div id="eacsmsg" class="updated fade"><p><strong><?php _e('Your color scheme has been successfully imported!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '11') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('The color scheme could not be imported!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '12') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('The color scheme you were trying to import does not have the proper header!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '13') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('The color scheme you were trying to import already exists!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '14') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php printf(__('Imported color schemes can only be .CSS files smaller than %s!', $eacs_domain), ini_get('upload_max_filesize').'B') ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '15') { ?>
				<div id="eacsmsg" class="error"><p><strong><?php _e('No file was uploaded!', $eacs_domain) ?></strong></p></div>
			<?php } elseif($_GET['msg'] == '16') {
				$forced = ($_wp_admin_css_colors[get_option('eacs_force')]) ? '&ldquo;'.ucwords(strtolower($_wp_admin_css_colors[get_option('eacs_force')]->name)).'&rdquo;' : __('same', $eacs_domain); ?>
				<div id="eacsmsg" class="updated fade"><p><strong><?php _e('Your current color scheme has been updated!', $eacs_domain); echo('<br /><small style="font-weight:normal;line-height:2em">'); printf(__('However, the administrator has chosen to restrict all users to use the %s color scheme. You will not be able to use your chosen color scheme until this restriction has been lifted.', $eacs_domain), $forced); echo('</small>') ?></strong></p></div>
			<?php }
		} ?>
		<div class="wrap">
			<?php screen_icon() ?>
			<h2 id="all-schemes"><?php _e('Color Schemes', $eacs_domain) ?></h2>
			<div class="section">
				<p><?php printf(__('Below is a list of all currently existing color schemes. You can add more by going down to the <a href="%1$s" title="Create a color scheme">Create a Color Scheme</a> area. Also, use the <a href="%2$s" title="Import a color scheme">Import a Color Scheme</a> area to import a pre-existing scheme.', $eacs_domain), $plugpageurl.'#scheme-input', $plugpageurl.'#import-export') ?></p>
				<table class="widefat" id="the-scheme-list">
					<thead>
						<tr>
							<th scope="col"><?php _e('Scheme', $eacs_domain) ?></th>
							<th scope="col" style="width:20%"><?php _e('Last Modified', $eacs_domain) ?></th>
							<th scope="col" style="text-align:right;width:20%"><?php _e('Actions', $eacs_domain) ?></th>
						</tr>
					</thead>

					<tbody>
						<?php if($_wp_admin_css_colors) {
							$current_user_admin_color = (get_user_option('admin_color')) ? get_user_option('admin_color') : 'fresh';
							foreach($_wp_admin_css_colors as $color => $color_info) { $cleaname = sanitize_title($color_info->name); $srtschemes[$cleaname] = $color; }
							ksort($srtschemes);

							foreach($srtschemes as $ischeme => $color) {
								if($_wp_admin_css_colors[$color]) {
									$schememodtime = stream_last_modified($_wp_admin_css_colors[$color]->url);
									$schememodate = ($schememodtime) ? date('d/m/Y', $schememodtime).'&nbsp;<small class="nonessential">'.date('\@\&\n\b\s\p\;g:iA', $schememodtime).'</small>' : 'Unknown'; ?>
									<tr>
										<td class="eacs-schemes-colors"><?php foreach($_wp_admin_css_colors[$color]->colors as $html_color) { ?><span style="background-color:<?php echo($html_color) ?>">&nbsp;</span><?php } ?><strong><?php echo($_wp_admin_css_colors[$color]->name) ?></strong></td>
										<td style="width:20%"><?php _e($schememodate, $eacs_domain) ?></td>
										<td style="text-align:right;width:20%"><script type="text/javascript">/* <![CDATA[ */document.write('<?php echo('<a href="'.admin_url().'?eacsprev='.$color.'&amp;TB_iframe=true&amp;height=600&amp;width=800" title="'.__('Preview this color scheme', $eacs_domain).'" class="thickbox">'.__('Preview', $eacs_domain).'</a>&nbsp;|&nbsp;') ?>');/* ]]> */</script><?php
										$use_link = ($color != $current_user_admin_color && !$_wp_admin_css_colors[get_option('eacs_force')]) ? '<a href="'.$plugpageurl.'&amp;eacsaction=use&amp;eacsid='.$color.'" title="'.__('Use this color scheme now', $eacs_domain).'">'.__('Use', $eacs_domain).'</a>' : '<span class="nonessential">'.__('Use', $eacs_domain).'</span>';
										$copy_link = (eacs_current_user_can_manip()) ? '<a href="'.$plugpageurl.'&amp;eacscopyid='.$color.'#scheme-input" title="'.__('Copy this color scheme to the Create a Color Scheme area below', $eacs_domain).'">'.__('Copy', $eacs_domain).'</a>' : '<span class="nonessential">'.__('Copy', $eacs_domain).'</span>';
										$export_link = ($_GET['eacsaction'] == 'export' && $_GET['eacsid'] == $color) ? '<span class="nonessential">'.__('Export', $eacs_domain).'</span>' : '<a href="'.$plugpageurl.'&amp;eacsaction=export&amp;eacsid='.$color.'#import-export" title="'.__('Export this color scheme', $eacs_domain).'">'.__('Export', $eacs_domain).'</a>';

										if($cssfiles[$color] && eacs_current_user_can_manip()) {
											$edit_link = ($_GET['eacsaction'] == 'edit' && $_GET['eacsid'] == $color) ? '<span class="nonessential">'.__('Edit', $eacs_domain).'</span>' : '<a href="'.$plugpageurl.'&amp;eacsaction=edit&amp;eacsid='.$color.'#scheme-input" title="'.__('Edit this color scheme', $eacs_domain).'">'.__('Edit', $eacs_domain).'</a>';
											$delete_link = '<a href="'.$plugpageurl.'&amp;eacsaction=delete&amp;eacsid='.$color.'" title="'.__('Delete this color scheme', $eacs_domain).'" class="delete" onclick="return deleteConfirm(\''.addslashes($_wp_admin_css_colors[$color]->name).'\');">'.__('Delete', $eacs_domain).'</a>';
										} else {
											$edit_link = '<span class="nonessential">'.__('Edit', $eacs_domain).'</span>';
											$delete_link = '<span class="nonessential">'.__('Delete', $eacs_domain).'</span>';
										}

										echo($use_link.'&nbsp;|&nbsp;'.$edit_link.'&nbsp;|&nbsp;'.$copy_link.'&nbsp;|&nbsp;'.$export_link.'&nbsp;|&nbsp;'.$delete_link); ?></td>
									</tr>
								<?php }
							}
						} else { ?>
							<tr>
								<td colspan="3"><strong><?php _e('There are currently no color schemes! Add some below&hellip;', $eacs_domain) ?></strong></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>


			<div class="metabox-holder meta-box-sortables ui-sortable pointer">
				<?php if(eacs_current_user_can_manip()) { ?>
					<div id="scheme-input" class="postbox">
						<div id="eacs-handlediv-one" class="handlediv" title="Click to toggle"><br /></div><h3 id="eacs-hndle-one" class="hndle" title="Click to toggle"><span><?php echo($scheme_header) ?></span></h3>
						<div id="eacs-inside-one" class="inside">
							<p><?php echo($scheme_headdesc); _e(' Also, if JavaScript is enabled in your browser, there is a live preview. Click on the Toggle Live Preview button below to toggle the live preview on and off.', $eacs_domain) ?></p>
							<form method="post" action="<?php echo($plugpageurl) ?>">
								<div id="schemeform" class="form-field">
									<h4 class="spechead"><?php _e('Scheme Details', $eacs_domain) ?></h4>
									<p class="fleft"><label for="schemename"><?php _e('The Name', $eacs_domain) ?></label><br /><input type="text" name="schemename" id="schemename" value="<?php echo($scheme_name) ?>" size="40" maxlength="200"<?php echo($scheme_nameclr) ?> title="<?php _e('The color scheme name can contain alphanumeric characters, spaces, and special characters. It can be between 3 and 200 characters in length&hellip;', $eacs_domain) ?>" /></p>
									<p class="fright"><label for="priclrtxt1"><?php _e('Primary Colors', $eacs_domain) ?></label><br /><span id="priclrs"><input type="text" name="priclrtxt1" id="priclrtxt1" value="<?php echo($scheme_priclr1) ?>" size="8" maxlength="7" class="color code" />&nbsp;<input type="text" name="priclrtxt2" id="priclrtxt2" value="<?php echo($scheme_priclr2) ?>" size="8" maxlength="7" class="color code" />&nbsp;<input type="text" name="priclrtxt3" id="priclrtxt3" value="<?php echo($scheme_priclr3) ?>" size="8" maxlength="7" class="color code" />&nbsp;<input type="text" name="priclrtxt4" id="priclrtxt4" value="<?php echo($scheme_priclr4) ?>" size="8" maxlength="7" class="color code" /></span></p>
									<h4 class="spechead2"><label for="schemecss"><?php _e('Scheme CSS', $eacs_domain) ?></label><?php if($allschemes[$_GET['eacscopyid']]) { echo(' <small class="nonessential" style="font-size:x-small;font-weight:normal">'); printf(__('(Copied from %s)', $eacs_domain), '<strong>'.trim($allschemes[$_GET['eacscopyid']]['name']).'</strong>'); echo('</small>'); } ?></h4>
									<textarea name="schemecss" id="schemecss" cols="50" rows="20" class="code"><?php echo($scheme_css) ?></textarea>
								</div>
								<p class="submit"><?php echo($scheme_hiddename) ?><input type="hidden" name="eacsaction" id="eacsaction" value="<?php echo($scheme_action) ?>" /><input type="submit" name="eacsubmit" id="eacsubmit" value="<?php echo($scheme_submitval) ?>" class="button-primary" /><br class="clear" /></p>
							</form>
						</div>
					</div>
				<?php } ?>

				<?php if($_GET['eacsaction'] != 'export' && $_GET['eacsaction'] != 'edit' && !eacs_current_user_can_manip()){}else { ?>
					<div id="import-export" class="postbox">
						<div id="eacs-handlediv-two" class="handlediv" title="Click to toggle"><br /></div><h3 id="eacs-hndle-two" class="hndle" title="Click to toggle"><span><?php echo($importexport_header) ?></span></h3>
						<div id="eacs-inside-two" class="inside">
							<p><?php echo($importexport_headdesc) ?></p>
							<form method="post" action="<?php echo($plugpageurl) ?>"<?php echo($import_enctype) ?>>
								<div class="form-field">
									<?php if(($_GET['eacsaction'] == 'export' || $_GET['eacsaction'] == 'edit') && $current_scheme_frmall) { ?>
										<p><input type="radio" name="exportmethod" id="exportmethod1" value="method1" checked="checked" /> <strong><label for="exportmethod1">EACS CSS File</label></strong><br /><small class="nonessential specindent"><?php _e('This method exports the currently selected color scheme as a special CSS file with special header text at the beginning of the file which allows it to be imported back into this plugin.', $eacs_domain) ?></small></p>
										<p><input type="radio" name="exportmethod" id="exportmethod2" value="method2" /> <strong><label for="exportmethod2">WordPress Plugin</label></strong><br /><small class="nonessential specindent"><?php _e('This method exports the currently selected color scheme as its own WordPress plugin so it can be used by itself on any WordPress site without requiring the Easy Admin Color Schemes plugin.', $eacs_domain) ?></small></p>
									<?php } else { ?>
										<p><input type="file" name="importcssfile" id="importcssfile" size="55" /><br /><small class="nonessential"><?php printf(__('<strong>Please Note:</strong> Only files with the <strong><a href="http://www.wikipedia.org/wiki/Cascading_Style_Sheets" class="nonessential" title="Cascading Style Sheets">.CSS</a></strong> extension can be imported. Files bigger than <strong>%s</strong> cannot be imported!<br />Imported files <strong>MUST</strong> have the proper header at the beginning of the file in order to be recognized!', $eacs_domain), ini_get('upload_max_filesize').'B') ?></small></p>
										<div class="note"><p><?php _e('A proper header looks like this:', $eacs_domain) ?></p><pre class="code">/*&#10;Scheme Name: Washedout&#10;Primary Colors: 464646|DEDEDE|D54E21|6A6A6A&#10;*/</pre></div>
									<?php } ?>
								</div>
								<p class="submit"><?php echo($import_sizemax.$export_hiddename) ?><input type="hidden" name="eacsieaction" id="eacsieaction" value="<?php echo($importexport_action) ?>" /><input type="submit" name="eacsiesubmit" id="eacsiesubmit" value="<?php echo($importexport_submitval) ?>" class="button-primary" /></p>
							</form>
						</div>
					</div>
				<?php } ?>

				<?php if(current_user_can('manage_options')) { ?>
					<div id="plugin-settings" class="postbox">
						<div id="eacs-handlediv-three" class="handlediv" title="Click to toggle"><br /></div><h3 id="eacs-hndle-three" class="hndle" title="Click to toggle"><span><?php _e('Settings', $eacs_domain) ?></span></h3>
						<div id="eacs-inside-three" class="inside">
							<form method="post" action="options.php">
								<table class="form-table eacsform">
									<tr>
										<th><label for="eacs_default">The&nbsp;site-wide&nbsp;default&nbsp;color&nbsp;scheme&nbsp;is:</label></th>
										<td><select name="eacs_default" id="eacs_default"><option value="0"<?php if(get_option('eacs_default') === false || get_option('eacs_default') === '0') echo(' selected="selected"'); ?>>&mdash; WordPress Default &mdash;</option><?php foreach($_wp_admin_css_colors as $color => $color_info) { $selected1 = (get_option('eacs_default') === $color) ? ' selected="selected"' : ''; echo('<option value="'.$color.'"'.$selected1.'>'.$color_info->name.'</option>'); } ?></select></td>
									</tr>

									<tr>
										<th><label for="eacs_force">Force&nbsp;this&nbsp;color&nbsp;scheme&nbsp;for&nbsp;all&nbsp;users:</label></th>
										<td><select name="eacs_force" id="eacs_force"><option value="0"<?php if(get_option('eacs_force') === false || get_option('eacs_force') === '0') echo(' selected="selected"'); ?>>&mdash; None &mdash;</option><?php foreach($_wp_admin_css_colors as $color => $color_info) { $selected2 = (get_option('eacs_force') === $color) ? ' selected="selected"' : ''; echo('<option value="'.$color.'"'.$selected2.'>'.$color_info->name.'</option>'); } ?></select></td>
									</tr>

									<tr>
										<th><label for="eacs_manip_role">Minimum&nbsp;role&nbsp;required&nbsp;to&nbsp;add,&nbsp;import,&nbsp;edit,&nbsp;and&nbsp;delete&nbsp;color&nbsp;schemes:</label></th>
										<td><select name="eacs_manip_role" id="eacs_manip_role"><?php $selected3 = get_option('eacs_manip_role') ? get_option('eacs_manip_role') : '10'; eacs_dropdown_roles($selected3) ?></select></td>
									</tr>

									<tr>
										<th><label for="eacs_manip_users">Specific&nbsp;users&nbsp;allowed&nbsp;to&nbsp;add,&nbsp;import,&nbsp;edit,&nbsp;and&nbsp;delete&nbsp;color&nbsp;schemes:</label><br /><small class="nonessential">Overwrites the previous setting for the specified users</small><br /><small style="color:#21759B">[Multiple selections allowed]</small></th>
										<td><select name="eacs_manip_users[]" id="eacs_manip_users" multiple="multiple" size="4" class="eacsautoh"><?php $userIDs = $wpdb->get_col($wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users ORDER BY %s ASC", 'user_nicename')); foreach($userIDs as $userID) { $user = get_userdata($userID); $selected4 = (in_array($user->ID, get_option('eacs_manip_users'))) ? ' selected="selected"' : ''; echo('<option value="'.$user->ID.'"'.$selected4.'>'.$user->user_nicename.'</option>'); } ?></select></td>
									</tr>
								</table>

								<p class="submit"><?php wp_nonce_field('update-options') ?><input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="eacs_default,eacs_force,eacs_manip_role,eacs_manip_users" /><input type="submit" name="eacsetsubmit" id="eacsetsubmit" value="<?php _e('Save Changes', $eacs_domain) ?>" class="button-primary" /></p>
							</form>
						</div>
					</div>
				<?php } ?>

				<div id="more-info" class="postbox">
					<div id="eacs-handlediv-four" class="handlediv" title="Click to toggle"><br /></div><h3 id="eacs-hndle-four" class="hndle" title="Click to toggle"><span><?php _e('More Information', $eacs_domain) ?></span></h3>
					<div id="eacs-inside-four" class="inside">
						<p><?php _e('This plugin allows you to manage the color schemes for the administration interface of this site. You can add, edit, import, export, and delete any scheme that is compatible with this plugin. The interface is fairly straightforward but, if you have any issues please <a href="http://www.jamesdimick.com/contact/">get in contact with the author</a>. You can also report any bugs, errors, or anything else you would like at <a href="http://www.jamesdimick.com/creations/easy-admin-color-schemes/">the official plugin page</a>. Finally, if you would like to help support this plugin, you can donate to the author by clicking the Donate button below. Donations are <strong>very</strong> much appreciated and they will all go to making this plugin better!', $eacs_domain) ?></p>

						<form id="donatebtn" action="https://www.paypal.com/cgi-bin/webscr" method="post">
							<p>
								<input type="hidden" name="cmd" value="_s-xclick" />
								<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----" />
								<input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate" />
								<img src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" alt="" />
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	<?php }

	add_submenu_page('options-general.php', __('Color Schemes', $eacs_domain), __('Color Schemes', $eacs_domain), 0, 'easy-admin-color-schemes', 'eacs_settings_page');
}

function eacs_rightnow() {
	global $_wp_admin_css_colors, $eacs_domain;
	if(function_exists('get_user_option')) {
		$scheme_name = $_wp_admin_css_colors[get_user_option('admin_color')]->name; ?>
		<p class="eacs-right-now"><?php if(!$_wp_admin_css_colors[get_option('eacs_force')]) { ?><a href="<?php echo(get_option('siteurl').'/wp-admin/options-general.php?page=easy-admin-color-schemes') ?>" class="button rbutton"><?php _e('Change Scheme', $eacs_domain) ?></a><?php } printf(__('Admin Color Scheme %1$s', $eacs_domain), '<strong><a href="'.get_option('siteurl').'/wp-admin/options-general.php?page=easy-admin-color-schemes">'.$scheme_name.'</a></strong>'); ?></p>
	<?php }
}

add_action('rightnow_end', 'eacs_rightnow', 1);
?>
