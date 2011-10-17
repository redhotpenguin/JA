<?php
 /**
 * WP Highrise Contact form file
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		WP Highrise Contact Wordpress Plugin
 * @credit		Original code by Ryan Duff and Peter Westwood from WP-ContactForm
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: wphc.php 1010 2010-02-16 02:46:36Z marcan $
 */

$ret = '
	<form id="wphc" action="' . get_permalink(). '" method="post">
	<div id="edit-honeytrap-wrapper" class="form-item">
		<label for="edit-honeytrap">Leave this field blank: </label>
		<input id="edit-honeytrap" class="form-text" maxlength="128" name="honeytrap" size="60" type="text" />
	</div>
	<div class="message">
		<label for="ct-message">' . __('Question', 'wphc') . '</label><textarea id="ct-message" class="required" name="message"></textarea>
	</div>
	<div class="email">
		<label for="ct-email">E-mail</label>
		<input id="ct-email" class="required email" name="email" type="text" />
	</div>
	<div class="buttons">
		<button type="submit">' . __('Send', 'wphc') . '</button>
	</div>
</form>
<script type="text/javascript">
	jQuery(document).ready( function () {
		jQuery("#wphc").validate();
	} );
</script>';

return $ret;
?>