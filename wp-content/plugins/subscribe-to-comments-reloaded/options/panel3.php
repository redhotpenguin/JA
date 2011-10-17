<?php
// Avoid direct access to this piece of code
if (strpos($_SERVER['SCRIPT_FILENAME'], basename(__FILE__))){
	header('Location: /');
	exit;
}

// Update options
if (isset($_POST['options'])){
	$faulty_fields = '';
	if (isset($_POST['options']['manager_page']) && !subscribe_reloaded_update_option('manager_page', $_POST['options']['manager_page'], 'text')) $faulty_fields = __('Management Page ID','subscribe-reloaded').', ';
	if (isset($_POST['options']['purge_days']) && !subscribe_reloaded_update_option('purge_days', $_POST['options']['purge_days'], 'integer')) $faulty_fields = __('Autopurge requests','subscribe-reloaded').', ';
	if (isset($_POST['options']['from_name']) && !subscribe_reloaded_update_option('from_name', $_POST['options']['from_name'], 'text')) $faulty_fields = __('Sender name','subscribe-reloaded').', ';
	if (isset($_POST['options']['from_email']) && !subscribe_reloaded_update_option('from_email', $_POST['options']['from_email'], 'text')) $faulty_fields = __('Sender email address','subscribe-reloaded').', ';
	if (isset($_POST['options']['checked_by_default']) && !subscribe_reloaded_update_option('checked_by_default', $_POST['options']['checked_by_default'], 'yesno')) $faulty_fields = __('Checked by default','subscribe-reloaded').', ';
	if (isset($_POST['options']['checkbox_class']) && !subscribe_reloaded_update_option('checkbox_class', $_POST['options']['checkbox_class'], 'text')) $faulty_fields = __('Custom CSS Class','subscribe-reloaded').', ';
	if (isset($_POST['options']['checkbox_inline_style']) && !subscribe_reloaded_update_option('checkbox_inline_style', $_POST['options']['checkbox_inline_style'], 'text')) $faulty_fields = __('Custom inline style','subscribe-reloaded').', ';
	if (isset($_POST['options']['checkbox_html']) && !subscribe_reloaded_update_option('checkbox_html', $_POST['options']['checkbox_html'], 'text')) $faulty_fields = __('Custom HTML','subscribe-reloaded').', ';
	if (isset($_POST['options']['enable_double_check']) && !subscribe_reloaded_update_option('enable_double_check', $_POST['options']['enable_double_check'], 'yesno')) $faulty_fields = __('Enable double check','subscribe-reloaded').', ';
	if (isset($_POST['options']['notify_authors']) && !subscribe_reloaded_update_option('notify_authors', $_POST['options']['notify_authors'], 'yesno')) $faulty_fields = __('Notify authors','subscribe-reloaded').', ';
	if (isset($_POST['options']['enable_html_emails']) && !subscribe_reloaded_update_option('enable_html_emails', $_POST['options']['enable_html_emails'], 'yesno')) $faulty_fields = __('Enable HTML emails','subscribe-reloaded').', ';
	if (isset($_POST['options']['process_trackbacks']) && !subscribe_reloaded_update_option('process_trackbacks', $_POST['options']['process_trackbacks'], 'yesno')) $faulty_fields = __('Send trackbacks','subscribe-reloaded').', ';
	if (isset($_POST['options']['enable_admin_messages']) && !subscribe_reloaded_update_option('enable_admin_messages', $_POST['options']['enable_admin_messages'], 'yesno')) $faulty_fields = __('Notify admin','subscribe-reloaded').', ';
	if (isset($_POST['options']['admin_subscribe']) && !subscribe_reloaded_update_option('admin_subscribe', $_POST['options']['admin_subscribe'], 'yesno')) $faulty_fields = __('Let admin subscribe','subscribe-reloaded').', ';

	// Display an alert in the admin interface if something went wrong
	echo '<div class="updated fade"><p>';
	if (empty($faulty_fields)){
			_e('Your settings have been successfully updated.','subscribe-reloaded');
	}
	else{
		_e('There was an error updating the following fields:','subscribe-reloaded');
		echo ' <strong>'.substr($faulty_fields,0,-2).'</strong>';
	}
	echo "</p></div>\n";
}

?>
<form action="admin.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=<?php echo $current_panel ?>" method="post">
<table class="form-table <?php echo $wp_locale->text_direction ?>">
<tbody>
	<tr>
		<th scope="row"><label for="manager_page"><?php _e('Management page','subscribe-reloaded') ?></label></th>
		<td><?php echo get_bloginfo('url') ?> <input type="text" name="options[manager_page]" id="manager_page" value="<?php echo subscribe_reloaded_get_option('manager_page'); ?>" size="30">
			<div class="description"><?php _e('The permalink for your management page (something like <code>/manage-subscriptions</code> or <code>/?page_id=345</code>). This page <b>does not</b> actually exist in the system, but its link must follow your permalink structure.','subscribe-reloaded'); 
			if ((get_option('permalink_structure','') == '') && (strpos(subscribe_reloaded_get_option('manager_page'), '?page_id=') === false))
				echo '<br/><strong>'.__("Warning: it looks like the value you are using may be incompatible with your permalink structure",'subscribe-reloaded').'</strong>'; ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="purge_days"><?php _e('Autopurge requests','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[purge_days]" id="purge_days" value="<?php echo subscribe_reloaded_get_option('purge_days'); ?>" size="10"> <?php _e('days','subscribe-reloaded') ?>
			<div class="description"><?php _e("Delete pending subscriptions (not confirmed) after X days. Zero disables this feature.",'subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="from_name"><?php _e('Sender name','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[from_name]" id="from_name" value="<?php echo subscribe_reloaded_get_option('from_name'); ?>" size="50">
			<div class="description"><?php _e('Name to use for the "from" field when sending a new notification to the user.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="from_email"><?php _e('Sender email address','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[from_email]" id="from_email" value="<?php echo subscribe_reloaded_get_option('from_email'); ?>" size="50">
			<div class="description"><?php _e('Email address to use for the "from" field when sending a new notification to the user.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="checked_by_default"><?php _e('Checked by default','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[checked_by_default]" id="checked_by_default" value="yes"<?php echo (subscribe_reloaded_get_option('checked_by_default') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[checked_by_default]" value="no" <?php echo (subscribe_reloaded_get_option('checked_by_default') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Decide if the checkbox should be checked by default or not.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="checkbox_class"><?php _e('Custom CSS Class','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[checkbox_class]" id="checkbox_class" value="<?php echo subscribe_reloaded_get_option('checkbox_class'); ?>" size="20">
			<div class="description"><?php _e('Custom CSS class to associate to the checkbox, if you want to customize its style.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="checkbox_inline_style"><?php _e('Custom inline style','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[checkbox_inline_style]" id="checkbox_inline_style" value="<?php echo subscribe_reloaded_get_option('checkbox_inline_style'); ?>" size="20">
			<div class="description"><?php _e('Custom inline CSS to add to the checkbox.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="checkbox_html"><?php _e('Custom HTML','subscribe-reloaded') ?></label></th>
		<td><input type="text" name="options[checkbox_html]" id="checkbox_html" value="<?php echo subscribe_reloaded_get_option('checkbox_html'); ?>" size="50">
			<div class="description"><?php _e('Custom HTML code to be used when displaying the checkbox. Allowed tags: [checkbox_field], [checkbox_label]','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="enable_double_check"><?php _e('Enable double check','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[enable_double_check]" id="enable_double_check" value="yes"<?php echo (subscribe_reloaded_get_option('enable_double_check') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[enable_double_check]" value="no" <?php echo (subscribe_reloaded_get_option('enable_double_check') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Send a notification email to confirm the subscription (to avoid addresses misuse).','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="notify_authors"><?php _e('Notify admin','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[notify_authors]" id="notify_authors" value="yes"<?php echo (subscribe_reloaded_get_option('notify_authors') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[notify_authors]" value="no" <?php echo (subscribe_reloaded_get_option('notify_authors') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Notify the administrator when a new comment is posted.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="enable_html_emails"><?php _e('Enable HTML emails','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[enable_html_emails]" id="enable_html_emails" value="yes"<?php echo (subscribe_reloaded_get_option('enable_html_emails') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[enable_html_emails]" value="no" <?php echo (subscribe_reloaded_get_option('enable_html_emails') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('If enabled, will send email messages with content-type = text/html instead of text/plain','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="process_trackbacks"><?php _e('Send trackbacks','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[process_trackbacks]" id="process_trackbacks" value="yes"<?php echo (subscribe_reloaded_get_option('process_trackbacks') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[process_trackbacks]" value="no" <?php echo (subscribe_reloaded_get_option('process_trackbacks') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Notify users when a new trackback or pingback is added to the discussion.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="enable_admin_messages"><?php _e('Track all subscriptions','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[enable_admin_messages]" id="enable_admin_messages" value="yes"<?php echo (subscribe_reloaded_get_option('enable_admin_messages') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[enable_admin_messages]" value="no" <?php echo (subscribe_reloaded_get_option('enable_admin_messages') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Notify the administrator when users subscribe without commenting.','subscribe-reloaded'); ?></div></td>
	</tr>
	<tr>
		<th scope="row"><label for="admin_subscribe"><?php _e('Let admin subscribe','subscribe-reloaded') ?></label></th>
		<td>
			<input type="radio" name="options[admin_subscribe]" id="admin_subscribe" value="yes"<?php echo (subscribe_reloaded_get_option('admin_subscribe') == 'yes')?' checked="checked"':''; ?>> <?php _e('Yes','subscribe-reloaded') ?> &nbsp; &nbsp; &nbsp;
			<input type="radio" name="options[admin_subscribe]" value="no" <?php echo (subscribe_reloaded_get_option('admin_subscribe') == 'no')?'  checked="checked"':''; ?>> <?php _e('No','subscribe-reloaded') ?>
			<div class="description"><?php _e('Let the administrator subscribe to comments when logged in.','subscribe-reloaded'); ?></div></td>
	</tr>
</tbody>
</table>
<p class="submit"><input type="submit" value="<?php _e('Save Changes') ?>" class="button-primary" name="Submit"></p>
</form>