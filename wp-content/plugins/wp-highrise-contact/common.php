<?php
 /**
 * INBOX Wordpress common
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		WP Highrise Contact Wordpress Plugin
 * @credit		Original code by Ryan Duff and Peter Westwood from WP-ContactForm
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: wphc.php 1010 2010-02-16 02:46:36Z marcan $
 */

if (!function_exists('inbox_debug')) {
	/**
	 * Output a line of debug
	 *
	 * @param string $msg text to be outputed as a debug line
	 * @param bool $exit if TRUE the script will end
	 */
	function inbox_debug($msg, $exit=false)
	{
		echo "<div style='padding: 5px; color: red; font-weight: bold'>debug :: $msg</div>";
		if ($exit) {
			die();
		}
	}
}
?>