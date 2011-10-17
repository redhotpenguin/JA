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
	<div class="firstName">
		<label for="ct-firstName">' . __('First name', 'wphc'). '</label>
		<input id="ct-firstName" class="required" name="firstName" type="text" />
	</div>
	<div class="lastName">
		<label for="ct-lastName">' . __('Last name', 'wphc'). '</label>
		<input id="ct-lastName" class="required" name="lastName" type="text" />
	</div>
	<div class="title">
		<label for="ct-title">' . __('Title', 'wphc') .'</label>
		<input id="ct-title" name="title" type="text" />
	</div>

	<div class="company">
		<label for="ct-company">' . __('Company', 'wphc'). '</label>
		<input id="ct-company" class="required" name="company" type="text" />
	</div>
	<div class="city">
		<label for="ct-city">' . __('City', 'wphc'). '</label>
		<input id="ct-city" name="city" type="text" />
	</div>
	<div class="phoneNumber">
		<label for="ct-phoneNumber">' . __('Phone number', 'wphc') . '</label>
		<input id="ct-phoneNumber" name="phoneNumber" type="text" />
	</div>
	<div class="email">
		<label for="ct-email">E-mail</label>
		<input id="ct-email" class="required email" name="email" type="text" />
	</div>

	<div class="website">
		<label for="ct-website">' . __('Web site', 'wphc') . '</label>
		<input id="ct-website" name="website" type="text" />
	</div>
	<div class="message">
		<label for="ct-message">' . __('Message', 'wphc') . '</label><textarea id="ct-message" class="required" name="message"></textarea>
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