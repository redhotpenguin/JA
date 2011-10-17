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
$post_ID = !empty($_POST['srp'])?intval($_POST['srp']):(!empty($_GET['srp'])?intval($_GET['srp']):0);
$post = get_post($post_ID);
if (($post_ID > 0) && !is_object($post)){
	return '';
}
$post_permalink = get_permalink($post_ID);
if (!empty($_POST['sre'])){
	$wp_subscribe_reloaded = new wp_subscribe_reloaded();

	// Send management link
	$from_name = stripslashes(get_option('subscribe_reloaded_from_name', 'admin'));
	$from_email = get_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'));
	$subject = stripslashes(get_option('subscribe_reloaded_management_subject', 'Manage your subscriptions on [blog_name]'));
	$message = stripslashes(get_option('subscribe_reloaded_management_content', ''));
	$manager_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
	if (function_exists('qtrans_convertURL')) $manager_link = qtrans_convertURL($manager_link);
	
	$clean_email = $wp_subscribe_reloaded->clean_email($_POST['sre']);
	$subscriber_salt = $wp_subscribe_reloaded->generate_key($clean_email);

	$headers = "MIME-Version: 1.0\n";
	$headers .= "From: $from_name <$from_email>\n";
	$content_type = (get_option('subscribe_reloaded_enable_html_emails', 'no') == 'yes')?'text/html':'text/plain';
	$headers .= "Content-Type: $content_type; charset=".get_bloginfo('charset')."\nX-Subscribe-to-Comments-Version: $wp_subscribe_reloaded->subscribe_version\n";

	if (strpos($manager_link, '?') !== false)
		$manager_link = "$manager_link&sre=".urlencode($clean_email)."&srk=$subscriber_salt";
	else
		$manager_link = "$manager_link?sre=".urlencode($clean_email)."&srk=$subscriber_salt";

	// Replace tags with their actual values
	$subject = str_replace('[blog_name]', get_bloginfo('name'), $subject);
	$message = str_replace('[blog_name]', get_bloginfo('name'), $message);
	$message = str_replace('[manager_link]', $manager_link, $message);
	
	// QTranslate support
	if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
		$subject = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($subject);
		$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
	}
	if($content_type == 'text/html') $message = $wp_subscribe_reloaded->wrap_html_message($message, $subject);
	wp_mail($clean_email, $subject, $message, $headers);

	$message = str_replace('[post_permalink]', $post_permalink, stripslashes(get_option('subscribe_reloaded_request_mgmt_link_thankyou')));
	if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
		$message = str_replace('[post_title]', qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post->post_title), $message);
		$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
	}
	else{
		$message = str_replace('[post_title]', $post->post_title, $message);
	}
	
	echo $message;
} else {
?>

<p><?php 
$message = stripslashes(get_option('subscribe_reloaded_request_mgmt_link'));
if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) $message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
echo $message;
?></p>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" onsubmit="if(this.subscribe_reloaded_email.value=='' || this.subscribe_reloaded_email.value.indexOf('@')==0) return false">
<fieldset style="border:0">
	<p><label for="subscribe_reloaded_email"><?php _e('Email','subscribe-reloaded') ?></label>
	<input type="text" class="subscribe-form-field" name="sre" value="<?php echo isset($_COOKIE['comment_author_email_'.COOKIEHASH])?$_COOKIE['comment_author_email_'.COOKIEHASH]:'email'; ?>" size="22"
		onfocus="if(this.value==this.defaultValue)this.value=''" onblur="if(this.value=='')this.value=this.defaultValue" /> 
	<input name="submit" type="submit" class="subscribe-form-button" value="<?php _e('Send','subscribe-reloaded') ?>" /></p>
</fieldset>
</form>
<?php
}
$output = ob_get_contents();
ob_end_clean();
return $output;
?>