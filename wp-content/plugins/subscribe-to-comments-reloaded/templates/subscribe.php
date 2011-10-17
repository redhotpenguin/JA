<?php
// Avoid direct access to this piece of code
if (strpos($_SERVER['SCRIPT_FILENAME'], basename(__FILE__))){
  header('Location: /');
  exit;
}

ob_start();

// Load localization files
load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/subscribe-to-comments-reloaded/langs', '/subscribe-to-comments-reloaded/langs');

// Is the post_id passed in the query string valid?
$post_ID = !empty($_GET['srp'])?intval($_GET['srp']):0;
$post = get_post($post_ID);
if (($post_ID > 0) && !is_object($post)){
	return '';
}
$post_permalink = get_permalink($post_ID);
if (!empty($_POST['subscribe_reloaded_email'])){
	$wp_subscribe_reloaded = new wp_subscribe_reloaded();
	$enable_double_check = get_option('subscribe_reloaded_enable_double_check', 'no');
	
	// If the case, send a message to the administrator
	if (get_option('subscribe_reloaded_enable_admin_messages', 'no') == 'yes'){
		$from_name = stripslashes(get_option('subscribe_reloaded_from_name', 'admin'));
		$from_email = get_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'));
		$clean_email = $wp_subscribe_reloaded->clean_email($_POST['subscribe_reloaded_email']);
	
		$subject = __('New subscription to','subscribe-reloaded')." $post->post_title";
		$message = __('New subscription to','subscribe-reloaded')." $post->post_title\n".__('User:','subscribe-reloaded')." $clean_email";
		
		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $from_name <$from_email>\n";
		$headers .= "Content-Type: text/plain; charset=".get_bloginfo('charset')."\n";
		wp_mail(get_bloginfo('admin_email'), $subject, $message, $headers);
	}
	if ($enable_double_check == 'yes' && !$wp_subscribe_reloaded->is_user_subscribed($post_ID, $_POST['subscribe_reloaded_email'], 'C')){
		$wp_subscribe_reloaded->add_subscription($_POST['subscribe_reloaded_email'], 'C', $post_ID);
		$wp_subscribe_reloaded->confirmation_email($_POST['subscribe_reloaded_email'], $post_ID);
		$message = stripslashes(get_option('subscribe_reloaded_subscription_confirmed_dci'));
	}
	else{
		if(!$wp_subscribe_reloaded->is_user_subscribed($post_ID, $_POST['subscribe_reloaded_email'], 'Y')){
			$this->add_subscription($_POST['subscribe_reloaded_email'], 'Y', $post_ID);
		}
		$message = stripslashes(get_option('subscribe_reloaded_subscription_confirmed'));
	}
	
	$message = str_replace('[post_permalink]', $post_permalink, $message);
	if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
		$message = str_replace('[post_title]', qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post->post_title), $message);
		$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
	}
	else{
		$message = str_replace('[post_title]', $post->post_title, $message);
	}
	
	echo "<p>$message</p>";
} else {
	global $current_user;
	
	if (!empty($current_user->user_email))
		$user_email = $current_user->user_email;
	else
		$user_email = isset($_COOKIE['comment_author_email_'.COOKIEHASH])?$_COOKIE['comment_author_email_'.COOKIEHASH]:'email'
?>

<p><?php 
$message = str_replace('[post_permalink]', $post_permalink, stripslashes(get_option('subscribe_reloaded_subscribe_without_commenting')));
if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
	$message = str_replace('[post_title]', qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post->post_title), $message);
	$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
}
else{
	$message = str_replace('[post_title]', $post->post_title, $message);
}
echo $message;
?></p>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" onsubmit="if(this.subscribe_reloaded_email.value=='' || this.subscribe_reloaded_email.value.indexOf('@')==0) return false">
<fieldset style="border:0">
	<p><label for="subscribe_reloaded_email"><?php _e('Email','subscribe-reloaded') ?></label> <input type="text" class="subscribe-form-field" name="subscribe_reloaded_email" value="<?php echo $user_email ?>" size="22"
		onfocus="if(this.value==this.defaultValue)this.value=''" onblur="if(this.value=='')this.value=this.defaultValue"/>
	<input name="submit" type="submit" class="subscribe-form-button" value="<?php _e('Send','subscribe-reloaded') ?>" /></p>
</fieldset>
</form>
<?php
}
$output = ob_get_contents();
ob_end_clean();
return $output;
?>