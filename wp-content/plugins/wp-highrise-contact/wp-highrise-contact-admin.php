<?php
 /**
 * WP Highrise Contact Plugin Admin Options
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		WP Highrise Contact Wordpress Plugin
 * @credit		Original code by Ryan Duff and Peter Westwood from WP-ContactForm
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: options-contactform.php 1009 2010-02-16 02:45:28Z marcan $
 */

$requirements_problem = false;
if (version_compare( phpversion(), '5', '<')) {
	$requirements_problem = true;
	?><div class="error fade" style="background-color:red;"><p><strong><?php _e('Sorry, you are running an old version of PHP. WP Highrise Contact requires PHP 5.x to work. Please ensure you are running PHP 5 and the plugin will work properly.', 'wphc'); ?></strong></div><?
}
if ( function_exists( "curl_connect" ) ) {
	$requirements_problem = true;
	?><div class="error fade" style="background-color:red;"><p><strong><?php _e('Sorry, cURL needs to be installed for WP Highrise Contact to work properly.', 'wphc'); ?></strong></div><?
}
if ($requirements_problem) exit;

load_plugin_textdomain('wphc',$path = 'wp-content/plugins/wp-highrise-contact');
$location = get_option('siteurl') . '/wp-admin/admin.php?page=wp-highrise-contact/wp-highrise-contact-admin.php'; // Form Action URI

/*Lets add some default options if they don't exist*/
add_option('wphc_email_address', __('lead@mycompany.com', 'wphc'));
add_option('wphc_email_subject', __('Lead Created', 'wphc'));
add_option('wphc_highrise_account', 'mycompany', 'wphc');
add_option('wphc_highrise_account_ssl', '0', 'wphc');
add_option('wphc_highrise_token', '', 'wphc');
add_option('wphc_highrise_task', 1, 'wphc');
add_option('wphc_highrise_task_hours', 24, 'wphc');
add_option('wphc_highrise_task_category', '', 'wphc');
add_option('wphc_success_message', 'Thank you for contacting us. We will get back to you shortly.', 'wphc');

/*check form submission and update options*/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	update_option('wphc_email_address', $_POST['wphc_email_address']);
	update_option('wphc_email_subject', $_POST['wphc_email_subject']);
	update_option('wphc_highrise_account', $_POST['wphc_highrise_account']);
	update_option('wphc_highrise_account_ssl', $_POST['wphc_highrise_account_ssl']);
	update_option('wphc_highrise_token', $_POST['wphc_highrise_token']);
	update_option('wphc_highrise_task', $_POST['wphc_highrise_task']);
	update_option('wphc_highrise_task_hours', $_POST['wphc_highrise_task_hours']);
	update_option('wphc_highrise_task_category', $_POST['wphc_highrise_task_category']);
	update_option('wphc_success_message', $_POST['wphc_success_message']);
}

/*Get options for form fields*/
$wphc_highrise_task = get_option('wphc_highrise_task');
$wphc_highrise_account_ssl = get_option('wphc_highrise_account_ssl');

require_once( dirname( __FILE__ ) . "/highrise/highrise.php" );
$server = new inbox_highrise_CurlConnection(get_option('wphc_highrise_account'), get_option('wphc_highrise_token'));
$server->useHttps = get_option('wphc_highrise_account_ssl');
$highrise_connected = $server->accepted();

if ($highrise_connected) {
	$tags = inbox_highrise_Tag::listEntities($server);
	$task_categories = inbox_highrise_TaskCategory::listEntities($server);
}

$wphc_highrise_task_yes = $wphc_highrise_task ? 'checked = "checked"' : '';
$wphc_highrise_task_no = !$wphc_highrise_task ? 'checked = "checked"' : '';

$wphc_highrise_account_ssl_yes = $wphc_highrise_account_ssl ? 'checked = "checked"' : '';
$wphc_highrise_account_ssl_no = !$wphc_highrise_account_ssl ? 'checked = "checked"' : '';

?>
<div id="wphc-form">
	<h2><?php _e('WP Highrise Contact Options', 'wphc') ?></h2>
	<p><?php _e('This plugin generates a form which you can drop on any page or post. When a visitor fills the form, the data will be sent to you via email and it will also be sent to your 37signals Highrise account where the following will be created:', 'wphc') ?></p>
	<div style="padding-left: 30px;">
		<li><?php _e('A contact populated with the form\'s data (name, email, company, title, phone, etc...)', 'wphc') ?></li>
		<li><?php _e('A note will be created, associated with the previously create contact', 'wphc') ?></li>
		<li><?php _e('A task associated to this note', 'wphc') ?></li>
	</div>

	<h3><?php _e('How to display a form', 'wphc') ?></h3>
	<p><?php _e('To display the form, simply us the shortcode <b>[wp-highrise-contact]</b> on any post or page. When the post or page is rendered, the shortcode will be replaced by the form.', 'wphc') ?></p>

  <form name="wphc-options" method="post" action="<?php echo $location ?>&amp;updated=true">

	<h3><?php _e('Contact Form Notification', 'wphc') ?></h3>
	<p><?php _e('When a visitor fills out the form, a notification is sent to an email address to notify someone in your organisation', 'wphc') ?></p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wphc_email_address"><?php _e('Recipient email address:', 'wphc'); ?></label></th>
			<td><input name="wphc_email_address" id="wphc_email_address" value="<?=get_option('wphc_email_address');?>" class="regular-text" type="text"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wphc_email_subject"><?php _e('Subject of the email:', 'wphc'); ?></label></th>
			<td><input name="wphc_email_subject" id="wphc_email_subject" value="<?=get_option('wphc_email_subject');?>" class="regular-text" type="text"></td>
		</tr>
	</table>

	<h3><?php _e('Highrise Connection', 'wphc') ?></h3>
	<p><?php _e('Input the credentials of your Highrise account. Please note that your <b>Highrise Account Name</b> is the first part of your Highrise URL. For example, if your URL is https://<b>mycompany</b>.highrisehq.com, your Highrise Account Name is <em>mycompany</em>.', 'wphc') ?></p>
	<?php if (!$highrise_connected) : ?>
		<div class="error fade" style="background-color:red;"><p><strong><?php _e('You need to enter valid Highrise credentials in order for the form to integrate with your Highrise account:', 'wphc'); ?></strong></div>
	<?php endif; ?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wphc_highrise_account"><?php _e('Your Highrise account name:', 'wphc'); ?></label></th>
			<td><input name="wphc_highrise_account" id="wphc_highrise_account" value="<?=get_option('wphc_highrise_account');?>" class="regular-text" type="text"></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wphc_highrise_account_ssl"><?php _e('SSL 128-bit encryption ?', 'wphc'); ?></label></th>
			<td>
				<input name="wphc_highrise_account_ssl" value="1" <?=$wphc_highrise_account_ssl_yes;?> type="radio"><?php _e('Yes'); ?>
				<input name="wphc_highrise_account_ssl" value="0" <?=$wphc_highrise_account_ssl_no;?> type="radio"><?php _e('No'); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wphc_highrise_token"><?php _e('Highrise API Token:', 'wphc'); ?></label></th>
			<td><input name="wphc_highrise_token" id="wphc_highrise_token" value="<?=get_option('wphc_highrise_token');?>" class="regular-text" type="text"></td>
		</tr>
	</table>

	<h3><?php _e('Highrise Contact Creation Options', 'wphc') ?></h3>
	<p><?php _e('When a visitor fills out the form, a contact will be created on your Highrise account, as well as a note and a task.', 'wphc') ?></p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wphc_highrise_task"><?php _e('Create a task associated with the contact:', 'wphc'); ?></label></th>
			<td>
				<input name="wphc_highrise_task" value="1" <?=$wphc_highrise_task_yes;?> type="radio"><?php _e('Yes'); ?>
				<input name="wphc_highrise_task" value="0" <?=$wphc_highrise_task_no;?> type="radio"><?php _e('No'); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wphc_highrise_task_hours"><?php _e('Task due date (in hours):', 'wphc'); ?></label></th>
			<td><input name="wphc_highrise_task_hours" id="wphc_highrise_task_hours" value="<?=get_option('wphc_highrise_task_hours');?>" class="regular-text" type="text"></td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="wphc_highrise_task_category"><?php _e('Task category:', 'wphc'); ?></label></th>
			<td>
				<select size="1" name="wphc_highrise_task_category" id="wphc_highrise_task_category">
					<?php foreach($task_categories as $task_category_ID => $task_category_name) : ?>
					<option value="<?=$task_category_ID;?>" <? if (get_option('wphc_highrise_task_category') == $task_category_ID) : ?>selected="selected"<?endif;?> > <?=$task_category_name;?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>

	<h3><?php _e('Other Options', 'wphc') ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wphc_success_message"><?php _e('Message to display to the user after submission of the form:', 'wphc'); ?></label></th>
			<td><textarea cols="50" rows = "5" name="wphc_success_message" id="wphc_success_message" class="regular-text"><?=get_option('wphc_success_message');?></textarea></td>
		</tr>
	</table>

    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Update Options', 'wpcf') ?> &raquo;" />
    </p>
  </form>
</div>