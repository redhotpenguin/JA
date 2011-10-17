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

$clean_post_id = !empty($_POST['srp'])?intval($_POST['srp']):(!empty($_GET['srp'])?intval($_GET['srp']):0);

if (!empty($_POST['email_list']) && !empty($_POST['action_type'])){
	$email_list = implode("','", $_POST['email_list']);
	switch($_POST['action_type']){
		case 'd':
			$wpdb->query("DELETE FROM $wp_subscribe_reloaded->table_subscriptions WHERE `email` IN ('$email_list') AND `post_ID` = '$clean_post_id'");
			break;
		case 's':
			$wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'N' WHERE `email` IN ('$email_list') AND `post_ID` = '$clean_post_id'");
			break;
		case 'a':
			$wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'Y' WHERE `email` IN ('$email_list') AND `post_ID` = '$clean_post_id'");
			break;
		default:
			break;
	}
	echo '<p><b>'.__('Subscriptions have been successfully updated.','subscribe-reloaded').'</b></p>';
}
$message = stripslashes(get_option('subscribe_reloaded_author_text'));
if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) $message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
echo "<p>$message</p>";
?>

<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="post" id="email_list_form"
	onsubmit="return confirm('<?php _e('Please remember: this operation cannot be undone. Are you sure you want to proceed?', 'subscribe-reloaded') ?>')">
<fieldset style="border:0">
<?php
	$subscriptions = $wpdb->get_results("SELECT `email`, `status`, `dt` FROM $wp_subscribe_reloaded->table_subscriptions WHERE `post_ID` = '$clean_post_id' ORDER BY `dt` ASC, `email` ASC", OBJECT);
	if (is_array($subscriptions) && !empty($subscriptions)){
		echo '<p>'.__('Title','subscribe-reloaded').': <strong>'.get_the_title($clean_post_id).'</strong></p>';
		echo '<p>'.__('Legend: Y: subscribed, N: suspended, C: awaiting confirmation','subscribe-reloaded').'</p>';
		echo '<ul class="subscribe-reloaded-list">';
		foreach($subscriptions as $i => $a_subscription){
			$subscriber_salt = $wp_subscribe_reloaded->generate_key($a_subscription->email);
			$manager_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
			if (strpos($manager_link, '?') !== false)
				$manager_link = "$manager_link&sre=".urlencode($a_subscription->email)."&srk=$subscriber_salt";
			else
				$manager_link = "$manager_link?sre=".urlencode($a_subscription->email)."&srk=$subscriber_salt";
			echo "<li><input type='checkbox' name='email_list[]' value='{$a_subscription->email}' id='e_$i'/> <label for='e_$i'><a class='subscribe-column-1' href='$manager_link'>$a_subscription->email</a></label> <span class='subscribe-separator subscribe-separator-1'>&mdash;</span> <span class='subscribe-column-2'>$a_subscription->dt</span> <span class='subscribe-separator subscribe-separator-2'>&mdash;</span> <span class='subscribe-column-3'>$a_subscription->status</span></li>\n";
		}
		echo '</ul>';
		echo '<p><a class="small-text" href="#" onclick="t=document.getElementById(\'email_list_form\').elements;for(i in t) t[i].checked=1">'.__('Select all','subscribe-reloaded').'</a> - ';
		echo '<a class="small-text" href="#" onclick="t=document.getElementById(\'email_list_form\').elements;for(i in t)if(t[i].checked==1){t[i].checked=0} else{t[i].checked=1}">'.__('Invert selection','subscribe-reloaded').'</a></p>';
		echo '<p>'.__('Action:','subscribe-reloaded').' <input type="radio" name="action_type" value="d" id="action_type_delete" /> <label for="action_type_delete">'.__('Delete','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp; ';
		echo '<input type="radio" name="action_type" value="s" id="action_type_suspend" checked="checked" /> <label for="action_type_suspend">'.__('Suspend','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp; ';
		echo '<input type="radio" name="action_type" value="a" id="action_type_activate" /> <label for="action_type_activate">'.__('Resume','subscribe-reloaded').'</label></p>';
		echo '<p><input type="submit" class="subscribe-form-button" value="'.__('Update subscriptions','subscribe-reloaded').'" /><input type="hidden" name="srp" value="'.$clean_post_id.'"/></p>';
		
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