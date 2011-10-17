<?php
// Avoid direct access to this piece of code
if (strpos($_SERVER['SCRIPT_FILENAME'], basename(__FILE__))){
  header('Location: /');
  exit;
}
global $wpdb;
ob_start();

// Load localization files
load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/subscribe-to-comments-reloaded/langs', '/subscribe-to-comments-reloaded/langs');
$wp_subscribe_reloaded = new wp_subscribe_reloaded();
if (!empty($current_user->user_email))
	$clean_email = $current_user->user_email;
else
	$clean_email = !empty($_POST['sre'])?$wp_subscribe_reloaded->clean_email($_POST['sre']):(!empty($_GET['sre'])?$wp_subscribe_reloaded->clean_email($_GET['sre']):'undefined');

if (!empty($_POST['post_list']) && !empty($_POST['action_type'])){
	$post_list = implode("','", $_POST['post_list']);
	switch($_POST['action_type']){
		case 'd':
			$wpdb->query("DELETE FROM $wp_subscribe_reloaded->table_subscriptions WHERE `post_ID` IN ('$post_list') AND `email` = '$clean_email'");
			break;
		case 's':
			$wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'N' WHERE `post_ID` IN ('$post_list') AND `email` = '$clean_email'");
			break;
		case 'a':
			$wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'Y' WHERE `post_ID` IN ('$post_list') AND `email` = '$clean_email'");
			break;
		default:
			break;
	}
	echo '<p><b>'.__('All the subscriptions have been successfully updated.','subscribe-reloaded').'</b></p>';
}
$message = stripslashes(get_option('subscribe_reloaded_user_text'));
if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) $message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
echo "<p>$message</p>";
?>

<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post" id="post_list_form"
	onsubmit="return confirm('<?php _e('Please remember: this operation cannot be undone. Are you sure you want to proceed?', 'subscribe-reloaded') ?>')">
<fieldset style="border:0">
<?php
	$subscriptions = $wpdb->get_results("SELECT `status`, `post_ID`, `dt` FROM $wp_subscribe_reloaded->table_subscriptions WHERE `email` = '$clean_email' ORDER BY `status` ASC, `post_ID` ASC", OBJECT);
	if (is_array($subscriptions) && !empty($subscriptions)){
		echo '<p>'.__('Email','subscribe-reloaded').': <strong>'.$clean_email.'</strong></p>';
		echo '<p>'.__('Legend: Y: subscribed, N: suspended, C: awaiting confirmation','subscribe-reloaded').'</p>';
		echo '<ul class="subscribe-reloaded-list">';
		foreach($subscriptions as $a_subscription){
			$permalink = get_permalink($a_subscription->post_ID);
			$title = get_the_title($a_subscription->post_ID);
			echo "<li><input type='checkbox' name='post_list[]' value='{$a_subscription->post_ID}' id='post_{$a_subscription->post_ID}'/> <label for='post_{$a_subscription->post_ID}'><a class='subscribe-column-1' href='$permalink'>$title</a></label> <span class='subscribe-separator subscribe-separator-1'>&mdash;</span> <span class='subscribe-column-2'>$a_subscription->dt</span> <span class='subscribe-separator subscribe-separator-2'>&mdash;</span> <span class='subscribe-column-3'>{$a_subscription->status}</span></li>\n";
		}
		echo '</ul>';
		echo '<p><a class="subscribe-small-text" href="#" onclick="t=document.getElementById(\'post_list_form\').elements;for(i in t) t[i].checked=1">'.__('Select all','subscribe-reloaded').'</a> - ';
		echo '<a class="small-text" href="#" onclick="t=document.getElementById(\'post_list_form\').elements;for(i in t)if(t[i].checked==1){t[i].checked=0} else{t[i].checked=1}">'.__('Invert selection','subscribe-reloaded').'</a></p>';
		echo '<p>'.__('Action:','subscribe-reloaded').' <input type="radio" name="action_type" value="d" id="action_type_delete" /> <label for="action_type_delete">'.__('Delete','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp; ';
		echo '<input type="radio" name="action_type" value="s" id="action_type_suspend" checked="checked" /> <label for="action_type_suspend">'.__('Suspend','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp; ';
		echo '<input type="radio" name="action_type" value="a" id="action_type_activate" /> <label for="action_type_activate">'.__('Resume','subscribe-reloaded').'</label></p>';
		echo '<p><input type="submit" class="subscribe-form-button" value="'.__('Update subscriptions','subscribe-reloaded').'" /></p>';
		
	}
	else{
		echo '<p>'.__('Sorry, no subscriptions found.','subscribe-reloaded').'</p>';
	}
?>
</fieldset>
</form>
<?php
$output = ob_get_contents();
ob_end_clean();
return $output;
?>