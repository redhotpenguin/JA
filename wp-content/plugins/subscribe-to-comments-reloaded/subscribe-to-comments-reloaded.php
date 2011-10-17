<?php
/*
Plugin Name: Subscribe to Comments Reloaded
Version: 1.6
Plugin URI: http://lab.duechiacchiere.it/index.php?board=5.0
Description: Subscribe to Comments Reloaded is a robust plugin that enables commenters to sign up for e-mail notifications. It includes a full-featured subscription manager that your commenters can use to unsubscribe to certain posts or suspend all notifications.
Author: camu
Author URI: http://www.duechiacchiere.it/
*/

// Avoid direct access to this piece of code
if (strpos($_SERVER['SCRIPT_FILENAME'], basename(__FILE__))){
	header('Location: /');
	exit;
}
/**
 * Displays the checkbox to allow visitors to subscribe
 */ 
function subscribe_reloaded_show(){
	global $post;
	$show_subscription_box = true;
	$html_to_show = '';
	$user_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '');
	if (function_exists('qtrans_convertURL')) $user_link = qtrans_convertURL($user_link);
	if (strpos($user_link, '?') !== false)
		$manager_link = "$user_link&amp;srp=$post->ID";
	else
		$manager_link = "$user_link?srp=$post->ID";

	$wp_subscribe_reloaded = new wp_subscribe_reloaded();
	
	if($wp_subscribe_reloaded->is_user_subscribed($post->ID, '', 'Y')){
		$html_to_show = str_replace('[manager_link]', $user_link,
			stripslashes(get_option('subscribe_reloaded_subscribed_label', __("You are subscribed to this entry. <a href='[manager_link]'>Manage</a> your subscriptions.",'subscribe-reloaded'))));
		$show_subscription_box = false;
	}
	elseif($wp_subscribe_reloaded->is_user_subscribed($post->ID, '', 'C')){
		$html_to_show = str_replace('[manager_link]', $user_link,
			stripslashes(get_option('subscribe_reloaded_subscribed_waiting_label', __("Your subscription to this entry needs to be confirmed. <a href='[manager_link]'>Manage your subscriptions</a>.",'subscribe-reloaded'))));
		$show_subscription_box = false;
	}
	
	if ($wp_subscribe_reloaded->is_author($post->post_author)){	// when the second parameter is empty, cookie value will be used
		if (get_option('subscribe_reloaded_admin_subscribe', 'no') == 'no') $show_subscription_box = false;
		$html_to_show .= str_replace('[manager_link]', $manager_link,
			stripslashes(get_option('subscribe_reloaded_author_label', __("You can <a href='[manager_link]'>manage the subscriptions</a> of this entry.",'subscribe-reloaded'))));
	}
	
	if ($show_subscription_box){
		$checked_by_default = get_option('subscribe_reloaded_checked_by_default', 'no');
		$checkbox_label = str_replace('[subscribe_link]', "$manager_link&amp;sra=s", 
			stripslashes(get_option('subscribe_reloaded_checkbox_label', __("Notify me of followup comments via e-mail. You can also <a href='[subscribe_link]'>subscribe</a> without commenting.",'subscribe-reloaded'))));
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) $checkbox_label = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($checkbox_label);
		$checkbox_class = get_option('subscribe_reloaded_checkbox_class', '');
		$checkbox_inline_style = get_option('subscribe_reloaded_checkbox_inline_style', 'width:30px');
		if (!empty($checkbox_class)) $checkbox_class = " class='$checkbox_class'";
		if (!empty($checkbox_inline_style)) $checkbox_inline_style = " style='$checkbox_inline_style'";
		$checkbox_html_wrap = get_option('subscribe_reloaded_checkbox_html', '');
		$checkbox_field = "<input$checkbox_class$checkbox_inline_style type='checkbox' name='subscribe-reloaded' id='subscribe-reloaded' value='yes'".(($checked_by_default == 'yes')?" checked='checked'":'')." />";
		if (empty($checkbox_html_wrap)){
			$html_to_show = "$checkbox_field $checkbox_label" . $html_to_show;
		}
		else{
			$checkbox_html_wrap = str_replace('[checkbox_field]', $checkbox_field, $checkbox_html_wrap);
			$html_to_show = str_replace('[checkbox_label]', $checkbox_label, $checkbox_html_wrap) . $html_to_show;
		}
	}
	echo "<!-- BEGIN: subscribe to comments reloaded -->$html_to_show<!-- END: subscribe to comments reloaded -->";
}

// Show the checkbox - You can manually override this by adding the corresponding function in your template
add_action('comment_form', 'subscribe_reloaded_show');

class wp_subscribe_reloaded {

	/**
	 * Constructor -- Sets things up.
	 */
	public function __construct(){
		global $wpdb;

		$this->subscribe_version = '1.6';

		// We use a table to store the information about our subscribers
		$this->table_subscriptions = $wpdb->prefix . 'subscribe_reloaded';
		$this->salt = defined('NONCE_KEY')?NONCE_KEY:'please create a unique key in your wp-config.php';
		
		// Initialization routines that should be executed on activation/deactivation
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
		
		// Add appropriate entries in the admin menu
		add_action('admin_menu', array( &$this, 'add_config_menu' ) );
		add_action('admin_print_styles-subscribe-to-comments-reloaded/options/index.php', array( &$this, 'add_options_stylesheet') );
		add_action('admin_print_styles-edit-comments.php', array( &$this, 'add_post_comments_stylesheet') );
		add_action('admin_print_styles-post.php', array( &$this, 'add_post_comments_stylesheet') );

		// What to do when a new comment is posted
		add_action('comment_post', array( &$this, 'new_comment_posted' ) );

		// Monitor actions on existing comments
		add_action('delete_comment', array( &$this, 'comment_deleted' ) );
		add_action('wp_set_comment_status', array( &$this, 'comment_status' ) );

		// Remove subscriptions attached to a post that is being deleted
		add_action('delete_post', array( &$this, 'delete_subscription' ) );

		// Provide content for the management page using WP filters
		$manager_page_permalink = get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
		if (function_exists('qtrans_convertURL')) $manager_page_permalink = qtrans_convertURL($manager_page_permalink);
		if (empty($manager_page_permalink)) $manager_page_permalink = get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
		if (strpos($_SERVER["REQUEST_URI"], $manager_page_permalink) !== false){
			add_filter('the_posts', array(&$this, 'subscribe_reloaded_manage'),9,2);
		}

		// Create a hook to use with the daily cron job
		add_action('subscribe_reloaded_purge', array( &$this,'subscribe_reloaded_purge') );

		// Add a new column to the Edit Comments panel
		add_filter('manage_edit-comments_columns', array( &$this,'add_column_header') );
		add_action('manage_comments_custom_column', array( &$this,'add_column') );

		// Contextual help
		add_action('contextual_help', array(&$this,'contextual_help'),10,3);
	}
	// end __construct

	/**
	 * Creates the table, adds the options to the database and imports the data from other plugins
	 */
	public function activate(){
		global $wpdb;

		// Load localization files
		load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/subscribe-to-comments-reloaded/langs', '/subscribe-to-comments-reloaded/langs');

		// Table that stores the actual data about subscribers
		$subscriptions_table_sql = "
			CREATE TABLE IF NOT EXISTS `$this->table_subscriptions` (
				`email` VARCHAR(255) NOT NULL DEFAULT '',
				`status` enum('Y','C','N') NOT NULL DEFAULT 'N',
				`post_ID` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
				`dt` TIMESTAMP(10) NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY `subscription` (`email`,`post_ID`),
				INDEX (`email`)
			)";
		$is_table_created = $this->_create_table($subscriptions_table_sql, $this->table_subscriptions);

		// Import the information collected by the OLD plugin, if needed
		$result = $wpdb->get_row("DESC $wpdb->comments comment_subscribe", ARRAY_A);
		if (is_array($result) && $is_table_created){
			$import_sql = "
				INSERT INTO `$this->table_subscriptions` (`email`,`status`,`post_ID`,`dt`)
					SELECT `comment_author_email`, `comment_subscribe`, `comment_post_ID`, `comment_date`
					FROM $wpdb->comments
					WHERE `comment_author_email` LIKE '%@%.%'
					GROUP BY `comment_post_ID`, `comment_author_email`
				";
			$wpdb->query($import_sql);
		}

		// Messages related to the management page
		if (get_option('permalink_structure','') == ''){
			add_option('subscribe_reloaded_manager_page', '/?page_id=99999', '', 'no');
		}
		else{
			add_option('subscribe_reloaded_manager_page', '/comment-subscriptions', '', 'no');
		}
		add_option('subscribe_reloaded_manager_page_title', __('Manage subscriptions','subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_request_mgmt_link', __('To manage your subscriptions, please enter your email address here below. We will send you a message containing the link to access your personal management page.', 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_request_mgmt_link_thankyou', __('Thank you for using our subscription service. Your request has been completed, and you should receive an email with the management link in a few minutes.', 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscribe_without_commenting', __("You can follow the discussion on <strong>[post_title]</strong> without having to leave a comment. Cool, huh? Just enter your email address in the form here below and you're all set.", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscription_confirmed', __("Thank you for using our subscription service. Your request has been completed. You will receive a notification email every time a new comment to this article is approved and posted by the administrator.", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscription_confirmed_dci', __("Thank you for using our subscription service. In order to confirm your request, please check your email for the verification message and follow the instructions.", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_author_text', __("In order to cancel or suspend one or more notifications, select the corresponding checkbox(es) and click on the button at the end of the list.", 'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_user_text', __("In order to cancel or suspend one or more notifications, select the corresponding checkbox(es) and click on the button at the end of the list. You are currently subscribed to:", 'subscribe-reloaded'), '', 'no');
		
		// Options
		add_option('subscribe_reloaded_purge_days', '30', '', 'no');
		add_option('subscribe_reloaded_from_name', 'admin', '', 'no');
		add_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'), '', 'no');
		add_option('subscribe_reloaded_checked_by_default', 'no', '', 'no');
		add_option('subscribe_reloaded_enable_double_check', 'no', '', 'no');
		add_option('subscribe_reloaded_notify_authors', 'no', '', 'no');
		add_option('subscribe_reloaded_enable_html_emails', 'no', '', 'no');
		add_option('subscribe_reloaded_process_trackbacks', 'no', '', 'no');		
		add_option('subscribe_reloaded_enable_admin_messages', 'no', '', 'no');
		add_option('subscribe_reloaded_admin_subscribe', 'no', '', 'no');

		// Messages related to the emails generated by StCR
		add_option('subscribe_reloaded_notification_subject', __('There is a new comment to [post_title]','subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_notification_content', __("There is a new comment to [post_title].\nComment Link: [comment_permalink]\nAuthor: [comment_author]\nComment:\n[comment_content]\nPermalink: [post_permalink]\nManage your subscriptions: [manager_link]",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_checkbox_label', __("Notify me of followup comments via e-mail. You can also <a href='[subscribe_link]'>subscribe</a> without commenting.",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_checkbox_class', '', '', 'no');
		add_option('subscribe_reloaded_checkbox_inline_style', 'width:30px', '', 'no');
		add_option('subscribe_reloaded_checkbox_html', '<p>[checkbox_field] [checkbox_label]</p>', '', 'no');
		add_option('subscribe_reloaded_subscribed_label', __("You are subscribed to this entry. <a href='[manager_link]'>Manage</a> your subscriptions.",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_subscribed_waiting_label', __("Your subscription to this entry needs to be confirmed. <a href='[manager_link]'>Manage your subscriptions</a>.",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_author_label', __("You can <a href='[manager_link]'>manage the subscriptions</a> of this entry.",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_double_check_subject', __('Please confirm your subscription to [post_title]','subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_double_check_content', __("You have requested to be notified every time a new comment is added to:\n[post_permalink]\n\nPlease confirm your request by clicking on this link:\n[confirm_link]",'subscribe-reloaded'), '', 'no');
		add_option('subscribe_reloaded_management_subject', __('Manage your subscriptions on [blog_name]','subscribe-reloaded'));
		add_option('subscribe_reloaded_management_content', __("You have requested to manage your subscriptions to the articles on [blog_name]. Follow this link to access your personal page:\n[manager_link]",'subscribe-reloaded'));

		// Schedule the autopurge hook
		if (!wp_next_scheduled('subscribe_reloaded_purge'))
			wp_schedule_event(time(), 'daily', 'subscribe_reloaded_purge');
	}
	// end activate

	/**
	 * Performs some clean-up maintenance (disable cron job).
	 */
	public function deactivate() {
		// Unschedule the autopurge hook
		if (wp_next_scheduled('subscribe_reloaded_purge') > 0)
			wp_clear_scheduled_hook('subscribe_reloaded_purge');
	}
	// end deactivate

	/**
	 * Removes old entries from the database
	 */
	public function subscribe_reloaded_purge() {
		global $wpdb;

		if (($autopurge_interval = intval(get_option('subscribe_reloaded_purge_days', 0))) <= 0) return;

		// Delete old entries
		$delete_sql = "DELETE FROM `$this->table_subscriptions` WHERE `dt` <= DATE_SUB(NOW(), INTERVAL $autopurge_interval DAY) AND `status` = 'C'";
		$wpdb->query($delete_sql);
	}
	// end subscribe_reloaded_purge

	/**
	 * Sends the confirmation message to a given user
	 */
	public function confirmation_email($_email = '', $_post_ID = 0){
		// Retrieve the options from the database
		$from_name = stripslashes(get_option('subscribe_reloaded_from_name', 'admin'));
		$from_email = get_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'));
		$subject = stripslashes(get_option('subscribe_reloaded_double_check_subject', 'Please confirm your subscribtion to [post_title]'));
		$message = stripslashes(get_option('subscribe_reloaded_double_check_content', ''));
		$manager_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
		if (function_exists('qtrans_convertURL')) $manager_link = qtrans_convertURL($manager_link);
		
		$clean_email = $this->clean_email($_email);
		$subscriber_salt = $this->generate_key($clean_email);

		if (strpos($manager_link, '?') !== false){
			$confirm_link = "$manager_link&sre=".urlencode($clean_email)."&srk=$subscriber_salt&srp=$_post_ID&sra=c";
			$manager_link = "$manager_link&sre=".urlencode($clean_email)."&srk=$subscriber_salt";
		}
		else{
			$confirm_link = "$manager_link?sre=".urlencode($clean_email)."&srk=$subscriber_salt&srp=$_post_ID&sra=c";
			$manager_link = "$manager_link?sre=".urlencode($clean_email)."&srk=$subscriber_salt";
		}

		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $from_name <$from_email>\n";
		$content_type = (get_option('subscribe_reloaded_enable_html_emails', 'no') == 'yes')?'text/html':'text/plain';
		$headers .= "Content-Type: $content_type; charset=".get_bloginfo('charset')."\nX-Subscribe-to-Comments-Version: $this->subscribe_version\n";

		$post = get_post($_post_ID);
		$post_permalink = get_permalink($_post_ID);

		// Replace tags with their actual values
		$subject = str_replace('[post_title]', $post->post_title, $subject);
		
		$message = str_replace('[post_permalink]', $post_permalink, $message);
		$message = str_replace('[confirm_link]', $confirm_link, $message);
		$message = str_replace('[manager_link]', $manager_link, $message);
		
		// QTranslate support
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
			$subject = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($subject);
			$message = str_replace('[post_title]', qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post->post_title), $message);
			$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
		}
		else{
			$message = str_replace('[post_title]', $post->post_title, $message);
		}
		if($content_type == 'text/html') $message = $this->wrap_html_message($message, $subject);
		wp_mail($clean_email, $subject, $message, $headers);
	}
	// end confirmation_email

	/**
	 * Adds a new row in the subscriptions' table, when a new comment is posted
	 */
	public function new_comment_posted($_comment_ID = 0, $_comment_status = 0){
	    global $wpdb;

		// Retrieve the information about the new comment
		$info = $wpdb->get_row("SELECT `comment_post_ID`, `comment_author_email`, `comment_approved`, `comment_type` FROM $wpdb->comments WHERE `comment_ID` = '$_comment_ID' LIMIT 1", OBJECT);
		if (empty($info) || $info->comment_approved == 'spam') return $_comment_ID;
		
		// Process trackbacks and pingbacks?
		if ((get_option('subscribe_reloaded_process_trackbacks', 'no') == 'no') && 
			($info->comment_type == 'trackback' || $info->comment_type == 'pingback')) return $_comment_ID;
		
		$subscribed_emails = array();

		// Did this visitor request to be subscribed to the discussion? (and s/he is not subscribed)
		if (!empty($_POST['subscribe-reloaded']) && $_POST['subscribe-reloaded'] == 'yes'){

			// Comment has been held in the moderation queue
			if ($info->comment_approved == '0'){
				$this->add_subscription($info->comment_author_email, 'C', $info->comment_post_ID);
				return $_comment_ID;
			}

			// Are we using double check-in?
			$enable_double_check = get_option('subscribe_reloaded_enable_double_check', 'no');
			if ($enable_double_check == 'yes' && !$this->is_user_subscribed($info->comment_post_ID, $info->comment_author_email)){
				$this->add_subscription($info->comment_author_email, 'C', $info->comment_post_ID);
				$this->confirmation_email($info->comment_author_email, $info->comment_post_ID);
			}
			elseif(!$this->is_user_subscribed($info->comment_post_ID, $info->comment_author_email)){
				$this->add_subscription($info->comment_author_email, 'Y', $info->comment_post_ID);
			}
		}

		// Send a notification to all the users subscribed to this post
		if (!empty($info) && $info->comment_approved == '1'){
			$subscribed_emails = $this->_get_subscriptions($info->comment_post_ID, 'Y');
			foreach($subscribed_emails as $a_email){
				// Skip the user who posted this new comment
				if ($a_email != $info->comment_author_email) $this->_notify_user($a_email, $info->comment_post_ID, $_comment_ID);
			}
		}

		// Notify administrator, if the case
		$notify_admin = get_option('subscribe_reloaded_notify_authors', 'no');
		if ($notify_admin == 'yes'){
			$admin_email = get_bloginfo('admin_email');
			$this->_notify_user($admin_email, $info->comment_post_ID, $_comment_ID);
		}

		return $_comment_ID;
	}
	// end new_comment_posted

	/**
	 * Performs the appropriate action when a comment is edited
	 */
	public function comment_status($_comment_ID = 0, $_comment_status = 0){
	    global $wpdb;

		// Retrieve the information about the comment
		$info = $wpdb->get_row("SELECT `comment_post_ID`, `comment_author_email`, `comment_approved` FROM $wpdb->comments WHERE `comment_ID` = '$_comment_ID' LIMIT 1", OBJECT);
		if (empty($info)) return $_comment_ID;

		switch($info->comment_approved){
			case '0': // Unapproved
			case 'trash':
				$wpdb->query("UPDATE $this->table_subscriptions set `status` = 'C' WHERE `email` = '$info->comment_author_email' AND `post_ID` = '$info->comment_post_ID'");
				break;

			case '1': // Approved
				// Are we using double check-in?
				$enable_double_check = get_option('subscribe_reloaded_enable_double_check', 'no');
				if (($enable_double_check == 'yes') && $this->is_user_subscribed($info->comment_post_ID, $info->comment_author_email, 'C')){
					$this->confirmation_email($info->comment_author_email, $info->comment_post_ID);
				}
				else{
					$wpdb->query("UPDATE $this->table_subscriptions set `status` = 'Y' WHERE `email` = '$info->comment_author_email' AND `post_ID` = '$info->comment_post_ID'");
					$subscribed_emails = $this->_get_subscriptions($info->comment_post_ID, 'Y');
					foreach($subscribed_emails as $a_email){
						// Skip the user who posted this new comment
						if ($a_email != $info->comment_author_email) $this->_notify_user($a_email, $info->comment_post_ID, $_comment_ID);
					}
				}
				break;

			default:
				break;
		}
		return $_comment_ID;
	}
	// end comment_status

	/**
	 * Performs the appropriate action when a comment is deleted
	 */
	public function comment_deleted($_comment_ID){
		global $wpdb;
		
		$info = $wpdb->get_row("SELECT `comment_post_ID`, `comment_author_email`, `comment_approved` FROM $wpdb->comments WHERE `comment_ID` = '$_comment_ID' LIMIT 1", OBJECT);
		if (empty($info)) return $_comment_ID;

		// Are there any other approved comments sent by this user?
		$count_approved_comments = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE `comment_post_ID` = '$info->comment_post_ID' AND `comment_author_email` = '$info->comment_author_email' AND `comment_approved` = 1");
		if (intval($count_approved_comments) == 0){
			$this->delete_subscription($info->comment_post_ID, $info->comment_author_email);
		}
		return $_comment_ID;
	}
	// end comment_deleted

	/**
	 * Displays the appropriate management page
	 */
	public function subscribe_reloaded_manage($_posts = '', $_query = ''){
		global $current_user;
		
		if (!empty($_posts)) return $_posts;
		$post_ID = !empty($_POST['srp'])?intval($_POST['srp']):(!empty($_GET['srp'])?intval($_GET['srp']):0);

		// Is the post_id passed in the query string valid?
		$target_post = get_post($post_ID);
		if (($post_ID > 0) && !is_object($target_post)) return $_posts;
		
		$action = !empty($_POST['sra'])?$_POST['sra']:(!empty($_GET['sra'])?$_GET['sra']:0);
		$key = !empty($_POST['srk'])?$_POST['srk']:(!empty($_GET['srk'])?$_GET['srk']:0);
		
		if (!empty($current_user->user_email))
			$email = $current_user->user_email;
		else
			$email = !empty($_POST['sre'])?urldecode($_POST['sre']):(!empty($_GET['sre'])?urldecode($_GET['sre']):0);

		// Subscribe without commenting
		if (!empty($action) && ($action == 's') && ($post_ID > 0)){
			$include_post_content = include(WP_PLUGIN_DIR.'/subscribe-to-comments-reloaded/templates/subscribe.php');
		}

		// Management page for post authors
		elseif (($post_ID > 0) && $this->is_author($target_post->post_author)){
			$include_post_content = include(WP_PLUGIN_DIR.'/subscribe-to-comments-reloaded/templates/author.php');
		}

		// Confirm your subscription (double check-in)
		elseif ( ($post_ID > 0) && !empty($email) && !empty($key) && !empty($action) &&
				$this->is_user_subscribed($post_ID, $email, 'C') &&
				$this->_is_valid_key($key, $email) &&
				($action = 'c') ){
			$include_post_content = include(WP_PLUGIN_DIR.'/subscribe-to-comments-reloaded/templates/confirm.php');
		}

		// Manage your subscriptions (user)
		elseif ( !empty($email) && ((!empty($key) && $this->_is_valid_key($key, $email)) || current_user_can('read')) ){
			$include_post_content = include(WP_PLUGIN_DIR.'/subscribe-to-comments-reloaded/templates/user.php');
		}

		if (empty($include_post_content))
			$include_post_content = include(WP_PLUGIN_DIR.'/subscribe-to-comments-reloaded/templates/request-management-link.php');

		global $wp_query;
			
		$manager_page_title = get_option('subscribe_reloaded_manager_page_title', 'Manage subscriptions');
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')) $manager_page_title = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($manager_page_title);
		
		$posts[] = 
			(object)array(
				'ID' => '9999999',
				'post_author' => '1',
				'post_date' => '2010-10-27 11:38:56',
				'post_date_gmt' => '2010-10-27 00:38:56',
				'post_content' => $include_post_content,
				'post_title' => $manager_page_title,
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => $manager_page_permalink,
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => '2010-10-27 11:00:01',
				'post_modified_gmt' => '2010-10-27 00:00:01',
				'post_content_filtered' => '',
				'post_parent' => '0',
				'menu_order' => '0',
				'post_type' => 'post',
				'post_mime_type' => '',
				'post_category' => '0',
				'comment_count' => '0',
				'filter' => 'raw'
			);
			
		// Make WP believe this is a real page, with no comments attached
		$wp_query->is_page = true;
		$wp_query->is_single = false;
		$wp_query->is_home = false;
		$wp_query->comments = false;

		// Discard 404 errors thrown by other checks
		unset($wp_query->query["error"]);
		$wp_query->query_vars["error"]="";
		$wp_query->is_404=false;
			
		// Seems like WP adds its own HTML formatting code to the content, we don't need that here
		remove_filter('the_content','wpautop');
			
		return $posts;
	}
	// end subscribe_reloaded_manage

	/**
	 * Adds a new row to the table
	 */
	public function add_subscription($_email, $_status, $_post_ID){
		global $wpdb;

		// Using Wordpress local time
		$dt = date_i18n('Y-m-d H:i:s');

		$clean_email = $this->clean_email($_email);
		$wpdb->query("INSERT IGNORE INTO $this->table_subscriptions (`email`, `status`, `post_ID`, `dt`) VALUES ('$clean_email', '$_status', '$_post_ID', '$dt')");
	}
	// end add_subscription

	/**
	 * Deletes a row in the subscriptions' table
	 */
	public function delete_subscription($_post_ID = '', $_email = ''){
	    global $wpdb;

		$sql = "DELETE FROM $this->table_subscriptions WHERE `post_ID` = '$_post_ID'".(!empty($_email)?" AND `email` = '$_email'":'');
		$wpdb->query( $sql );
	}
	// end delete_subscription

	/**
	 * Checks if a given email address is subscribed to a post
	 */
	public function is_user_subscribed($_post_ID = 0, $_email = '', $_status = ''){
		global $current_user;

		if ((empty($current_user->user_email) && empty($_COOKIE['comment_author_email_'. COOKIEHASH]) && empty($_email)) || empty($_post_ID)) return false;

		$subscribed_emails = $this->_get_subscriptions($_post_ID, $_status);
		if(empty($_email))
			$user_email = !empty($current_user->user_email)?$current_user->user_email:(!empty($_COOKIE['comment_author_email_'. COOKIEHASH])?urldecode($_COOKIE['comment_author_email_'. COOKIEHASH]):'#undefined#');
		else
			$user_email = $_email;

		if (in_array($user_email, $subscribed_emails)) return true;
		return false;
	}
	// end is_user_subscribed

	/**
	 * Checks if current logged in user is the author of this post
	 */
	public function is_author($_post_author){
		global $current_user;
		return (!empty($current_user) && (($_post_author == $current_user->ID) || current_user_can('manage_options')));
	}
	// end is_author

	/**
	 * Returns an email address where some possible 'offending' strings have been removed
	 */
	public function clean_email($_email){
		$offending_strings = array(
			"/to\:/i",
			"/from\:/i",
			"/bcc\:/i",
			"/cc\:/i",
			"/content\-transfer\-encoding\:/i",
			"/content\-type\:/i",
			"/mime\-version\:/i" 
		); 
		return htmlspecialchars(stripslashes(strip_tags(preg_replace($offending_strings, '', $_email))));
	}
	// end clean_email

	public function generate_key($_clean_email){
		$day = date_i18n('Ymd');
		return md5($day.$this->salt.$_clean_email);
	}

	/**
	 * Adds a new entry in the admin menu, to manage this plugin's options
	 */
	public function add_config_menu( $_s ) {
		global $current_user;

		if (current_user_can('manage_options')){
			add_options_page( 'Subscribe to Comments', 'Subscribe to Comments', 'manage_options', WP_PLUGIN_DIR.'/subscribe-to-comments-reloaded/options/index.php' );
		}
		return $_s;
	}
	// end add_config_menu

	/**
	 * Adds a custom stylesheet file to the admin interface
	 */
	public function add_options_stylesheet() {
		// It looks like WP_PLUGIN_URL doesn't honor the HTTPS setting in wp-config.php
		$stylesheet_url = (is_ssl()?str_replace('http://', 'https://', WP_PLUGIN_URL):WP_PLUGIN_URL).'/subscribe-to-comments-reloaded/style.css';
		wp_register_style('subscribe-to-comments', $stylesheet_url);
		wp_enqueue_style('subscribe-to-comments');
	}
	public function add_post_comments_stylesheet() {
		// It looks like WP_PLUGIN_URL doesn't honor the HTTPS setting in wp-config.php
		$stylesheet_url = (is_ssl()?str_replace('http://', 'https://', WP_PLUGIN_URL):WP_PLUGIN_URL).'/subscribe-to-comments-reloaded/post-and-comments.css';
		wp_register_style('subscribe-to-comments', $stylesheet_url);
		wp_enqueue_style('subscribe-to-comments');
	}
	// end add_stylesheet
	
	/**
	 * Adds a new column header to the Edit Comments panel
	 */
	public function add_column_header($_columns) {
		load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/subscribe-to-comments-reloaded/langs', '/subscribe-to-comments-reloaded/langs');
		$_columns['subscribe-reloaded'] = __('Subscribed','subscribe-reloaded');
		return $_columns;
	}
	// end add_column_header
	
	/**
	 * Adds a new column to the Edit Comments panel
	 */	
	public function add_column($_column_name){
		if ('subscribe-reloaded' != $_column_name) return;
		
		global $comment;
		load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/subscribe-to-comments-reloaded/langs', '/subscribe-to-comments-reloaded/langs');
		if ($this->is_user_subscribed($comment->comment_post_ID, $comment->comment_author_email, 'Y'))
			echo '<a href="admin.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=1&sre='.urlencode($comment->comment_author_email).'">'.__('Yes','subscribe-reloaded').'</a>';
		elseif ($this->is_user_subscribed($comment->comment_post_ID, $comment->comment_author_email, 'C'))
			_e('Awaiting confirmation','subscribe-reloaded');
		else _e('No','subscribe-reloaded');		
	}
	// end add_column
	
	/**
	 * Contextual help (link to the support forum)
	 */
	public function contextual_help($contextual_help, $screen_id, $screen) {
		if ($screen_id == 'subscribe-to-comments-reloaded/options/index'){
			load_plugin_textdomain('subscribe-reloaded', WP_PLUGIN_DIR .'/subscribe-to-comments-reloaded/langs', '/subscribe-to-comments-reloaded/langs');
			$contextual_help = __('Need help on how to use Subscribe to Comments Reloaded? Visit the official','subscribe-reloaded').' <a href="http://lab.duechiacchiere.it/index.php?board=5.0" target="_blank">'.__('support forum','subscribe-reloaded').'</a>. ';
			$contextual_help .= __('Feeling generous?','subscribe-reloaded').' <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=Z732JS7KQ6RRL&lc=US&item_name=Subscribe%20To%20Comments%20Reloaded&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank">'.__('Donate a few bucks!','subscribe-reloaded').'</a>';
		}
		return $contextual_help;
	}
	// end contextual_help
	
	/**
	 * Creates the HTML structure to properly handle HTML messages
	 */
	public function wrap_html_message($_message = '', $_subject = ''){
		return "<html><head><title>$_subject</title></head><body>$_message</body></html>";
	}
	// end _wrap_html_message

	/**
	 * Creates a table in the database
	 */
	private function _create_table($_sql = '', $_tablename = ''){
	    global $wpdb;

		// If the table is already there, abort
		foreach ( $wpdb->get_col("SHOW TABLES", 0) as $a_table ){
			if ( $a_table == $_tablename ) {
				return false;
			}
		}

		$wpdb->query( $_sql );

		// Let's make sure this table was actually created
		foreach ( $wpdb->get_col("SHOW TABLES", 0) as $a_table ){
			if ( $a_table == $_tablename ) {
				return true;
			}
		}
		return false;
	}
	// end _create_table

	/**
	 * Checks if a key is valid for a given email address
	 */
	private function _is_valid_key($_key, $_email){
		return ($this->generate_key($_email) == $_key);
	}
	// end _is_valid_key

	/**
	 * Sends the notification message to a given user
	 */
	private function _notify_user($_email = '', $_post_ID = 0, $_comment_ID = 0){
		// Retrieve the options from the database
		$from_name = stripslashes(get_option('subscribe_reloaded_from_name', 'admin'));
		$from_email = get_option('subscribe_reloaded_from_email', get_bloginfo('admin_email'));
		$subject = stripslashes(get_option('subscribe_reloaded_notification_subject', 'There is a new comment on the post [post_title]'));
		$message = stripslashes(get_option('subscribe_reloaded_notification_content', ''));
		$manager_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '/comment-subscriptions');
		if (function_exists('qtrans_convertURL')) $manager_link = qtrans_convertURL($manager_link);
		
		$clean_email = $this->clean_email($_email);
		$subscriber_salt = $this->generate_key($clean_email);
		if (strpos($manager_link, '?') !== false){
			$manager_link = "$manager_link&sre=".urlencode($clean_email)."&srk=$subscriber_salt";
		}
		else{
			$manager_link = "$manager_link?sre=".urlencode($clean_email)."&srk=$subscriber_salt";
		}

		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: $from_name <$from_email>\n";
		$content_type = (get_option('subscribe_reloaded_enable_html_emails', 'no') == 'yes')?'text/html':'text/plain';
		$headers .= "Content-Type: $content_type; charset=".get_bloginfo('charset')."\nX-Subscribe-to-Comments-Version: $this->subscribe_version\n";

		$post = get_post($_post_ID);
		$comment = get_comment($_comment_ID);
		$post_permalink = get_permalink( $_post_ID );
		$comment_permalink = get_comment_link($_comment_ID);

		// Replace tags with their actual values
		$subject = str_replace('[post_title]', $post->post_title, $subject);
		
		$message = str_replace('[post_permalink]', $post_permalink, $message);
		$message = str_replace('[comment_permalink]', $comment_permalink, $message);
		$message = str_replace('[comment_author]', $comment->comment_author, $message);
		$message = str_replace('[comment_content]', $comment->comment_content, $message);
		$message = str_replace('[manager_link]', $manager_link, $message);
		
		// QTranslate support
		if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){
			$subject = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($subject);
			$message = str_replace('[post_title]', qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post->post_title), $message);
			$message = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($message);
		}
		else{
			$message = str_replace('[post_title]', $post->post_title, $message);
		}
		if($content_type == 'text/html') $message = $this->wrap_html_message($message, $subject);
		wp_mail($clean_email, $subject, $message, $headers);
	}
	// end _notify_user

	/**
	 * Retrieves a list of emails subscribed to this post
	 */
	private function _get_subscriptions($_post_ID = 0, $_status = ''){
		global $wpdb;
		$flat_result = array();
		
		$filter_status = (!empty($_status))?" AND `status` = '$_status'":'';

		$result = $wpdb->get_results("SELECT DISTINCT `email` FROM $this->table_subscriptions WHERE `post_ID` = '$_post_ID'$filter_status", ARRAY_N);
		if (is_array($result)){
			foreach($result as $a_result){
				$flat_result[] = $a_result[0];
			}
		}

		return $flat_result;
	}
	// end _get_subscriptions
}
// end of class declaration

// Bootstrap the whole thing
$wp_subscribe_reloaded = new wp_subscribe_reloaded();

// Set a cookie if the user just subscribed without commenting
$subscribe_to_comments_action = !empty($_POST['sra'])?$_POST['sra']:(!empty($_GET['sra'])?$_GET['sra']:0);
$subscribe_to_comments_post_ID = !empty($_POST['srp'])?intval($_POST['srp']):(!empty($_GET['srp'])?intval($_GET['srp']):0);
if (!empty($subscribe_to_comments_action) && !empty($_POST['subscribe_reloaded_email']) && ($subscribe_to_comments_action == 's') && ($subscribe_to_comments_post_ID > 0)){
	$subscribe_to_comments_clean_email = $wp_subscribe_reloaded->clean_email($_POST['subscribe_reloaded_email']);
	setcookie('comment_author_email'.COOKIEHASH, $subscribe_to_comments_clean_email, time()+1209600, '/');
}