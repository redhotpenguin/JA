<?php

global $wpdb;

// Goodbye data...
$wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}subscribe_reloaded`");

// Goodbye options...
delete_option('subscribe_reloaded_manager_page');
delete_option('subscribe_reloaded_manager_page_title');
delete_option('subscribe_reloaded_request_mgmt_link');
delete_option('subscribe_reloaded_request_mgmt_link_thankyou');
delete_option('subscribe_reloaded_subscribe_without_commenting');
delete_option('subscribe_reloaded_subscription_confirmed');
delete_option('subscribe_reloaded_subscription_confirmed_dci');
delete_option('subscribe_reloaded_author_text');
delete_option('subscribe_reloaded_user_text');

delete_option('subscribe_reloaded_purge_days');
delete_option('subscribe_reloaded_from_name');
delete_option('subscribe_reloaded_from_email');
delete_option('subscribe_reloaded_checked_by_default');
delete_option('subscribe_reloaded_enable_double_check');
delete_option('subscribe_reloaded_notify_authors');
delete_option('subscribe_reloaded_enable_html_emails');
delete_option('subscribe_reloaded_process_trackbacks');
delete_option('subscribe_reloaded_enable_admin_messages');
delete_option('subscribe_reloaded_admin_subscribe');

delete_option('subscribe_reloaded_notification_subject');
delete_option('subscribe_reloaded_notification_content');
delete_option('subscribe_reloaded_checkbox_label');
delete_option('subscribe_reloaded_checkbox_class');
delete_option('subscribe_reloaded_checkbox_inline_style');
delete_option('subscribe_reloaded_checkbox_html');
delete_option('subscribe_reloaded_subscribed_label');
delete_option('subscribe_reloaded_subscribed_waiting_label');
delete_option('subscribe_reloaded_author_label');
delete_option('subscribe_reloaded_double_check_subject');
delete_option('subscribe_reloaded_double_check_content');
delete_option('subscribe_reloaded_management_subject');
delete_option('subscribe_reloaded_management_content');

// Remove scheduled autopurge events
wp_clear_scheduled_hook('subscribe_reloaded_purge');

?>