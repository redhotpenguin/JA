<?php
// Avoid direct access to this piece of code
if (strpos($_SERVER['SCRIPT_FILENAME'], basename(__FILE__))){
  header('Location: /');
  exit;
}

// Load localization files
global $wpdb;
load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/subscribe-to-comments-reloaded/langs', '/subscribe-to-comments-reloaded/langs');
$wp_subscribe_reloaded = new wp_subscribe_reloaded();
$clean_post_id = intval($_GET['srp']);
$clean_email = urldecode($wp_subscribe_reloaded->clean_email($_GET['sre']));
$post = get_post($clean_post_id);

$wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'Y' WHERE `email` = '$clean_email' AND `post_ID` = '$clean_post_id'");
$message = stripslashes(get_option('subscribe_reloaded_subscription_confirmed'));
if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) $message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
return "<p>$message</p>";

?>