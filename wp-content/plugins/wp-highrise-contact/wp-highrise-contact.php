<?php
/*
Plugin Name: WP Highrise Contact
Plugin URI: http://inboxinternational.com/wp-highrise-contact/
Description: This plugin generates a form which you can drop on any page or post. When a visitor fills the form, the data will be sent to you via email and it will also be sent to your 37signals Highrise account where a contact, a note and a task will be created
Author: INBOX International inc.
Author URI: http://inboxinternational.com
Version: 1.1.2
*/

 /**
 * WP Highrise Contact Plugin Main File
 *
 * @copyright	Copyright 2010 INBOX International http://inboxinternational.com
 * @since		1.0
 * @package		WP Highrise Contact Wordpress Plugin
 * @credit		Original code by Ryan Duff and Peter Westwood from WP-ContactForm
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		$Id: wphc.php 1010 2010-02-16 02:46:36Z marcan $
 */

include_once('common.php');

/*Wrapper function which calls the form.*/
function wphc_callback( $content ) {
	global $wpcf_strings;

	if (false === strpos($content, '[wp-highrise-contact]')) {
		return $content;
	}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// check honey trap
		if (!empty($_POST['honeytrap'])) {
			/**
			 * Send notification email
			 *
			 * By default, this is commented out. Simply remove the comments to enable it and reeive a
			 * notifiation when a robot fills the form. Yes, I know, it defeats the purpose, but it's only
			 * if you want to see how the Honey Pot spam control is effective :-)
			 */
			/*$to = get_option('wphc_email_address');
			$subject = 'SPAM - ' . get_option('wphc_email_subject');

			$body = print_r($_POST, true);
			$body .= "\r\n";
			$body .= 'Form: ' . get_permalink() . "\r\n";

			wp_mail($to, $subject, $body);
			*/
			header('location: ' . get_bloginfo('url'));
			die;
		}

		require_once( dirname( __FILE__ ) . "/highrise/highrise.php" );

		foreach ( array_keys( $_POST) as $k ) {
			$$k = filter_input( INPUT_POST, $k, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW );
		}

		$server = new inbox_highrise_CurlConnection(get_option('wphc_highrise_account'), get_option('wphc_highrise_token'));
		$server->useHttps = get_option('wphc_highrise_account_ssl');

		// enabling highrise debug
		//$server->debug = true;

		$highrise_connected = $server->accepted();
		if ($highrise_connected) {
			$contact = new inbox_highrise_Contact();
			$contact->firstName		= $firstName;
			$contact->lastName		= $lastName;
			$contact->title			= $title;
			if ( $company ) {
				$contact->company = new inbox_highrise_Company( null, $company );
			}
			if ( $city || $country ) {
				$address = new inbox_highrise_Address();
				if ( $city )		$address->city		= $city;
				if ( $country )		$address->country	= $country;
				$contact->contact->addComponent( "addresses",		$address,		"Work" );
			}
			if ( $phoneNumber ) {
				$contact->contact->addComponent( "phoneNumbers",	new inbox_highrise_PhoneNumber( null, $phoneNumber ),	"Work" );
			}
			if ( $email ) {
				$contact->contact->addComponent( "emails",			new inbox_highrise_EmailAddress( null, $email ),		"Work" );
			}
			if ( $website ) {
				$contact->contact->addComponent( "websites",		new inbox_highrise_WebAddress( null, $website ),		"Work" );
			}
			$contact->background = "Contacted us via: " . get_permalink();
			$server->postEntity( $contact );

			// Associate the created contact with the "lead" tag
			$tag = new inbox_highrise_Tag( null, "lead" );			// id=268079
			$server->postEntity( $tag, "/people/$contact->id" );

			// Create associated note, with "message" field content
			$note = new inbox_highrise_Note( null, $message );
			$server->postEntity( $note, "/people/$contact->id" );

			if (get_option('wphc_highrise_task')) {
				// Create a task associated to the contact
				$task = new inbox_highrise_Task( null, "Follow up - " . get_option('wphc_email_subject'));
				$task->category = get_option('wphc_highrise_task_category');
				$task->dateDue = date( "Y-m-d 10:00", time() + get_option('wphc_highrise_task_hours') * 3600 );
				$task->recording = $note->id;
				$server->postEntity( $task, "/people/$contact->id" );
			}
		}

		// send notification email
		$to = get_option('wphc_email_address');
		$subject = get_option('wphc_email_subject');

		$body = __('First name: ', 'wphc') . ': ' . $firstName . "\r\n";
		$body .= 'Last name: ' . $lastName . "\r\n";
		$body .= 'Title: ' . $title . "\r\n";
		$body .= 'Company: ' . $company . "\r\n";
		$body .= 'Email: ' . $email . "\r\n";
		$body .= 'Message: ' . $message . "\r\n";
		$body .= "\r\n";
		if ($highrise_connected)
			$body .= "Link: https://" . get_option('wphc_highrise_account') .".highrisehq.com/people/$contact->id" . "\r\n"; ;
		$body .= 'Form: ' . get_permalink() . "\r\n";

		wp_mail($to, $subject, $body);

    	if (file_exists(TEMPLATEPATH . '/plugins/wp-highrise-contact/form.inc.php'))
    		$form = include_once(TEMPLATEPATH . '/plugins/wp-highrise-contact/form.inc.php');
    	else
        	$form = include_once(dirname( __FILE__ ) . '/form.inc.php');
        $content = str_replace('[wp-highrise-contact]', $form, $content);
		return '<div class="wphc-message">' . get_option('wphc_success_message') . '</div>' . $content;
    }
    else {
    	// do we have a custom template
    	if (file_exists(TEMPLATEPATH . '/plugins/wp-highrise-contact/form.inc.php'))
    		$form = include_once(TEMPLATEPATH . '/plugins/wp-highrise-contact/form.inc.php');
    	else
        	$form = include_once(dirname( __FILE__ ) . '/form.inc.php');
        return str_replace('[wp-highrise-contact]', $form, $content);
    }
}

function wphc_head() {
	?>
<!-- inserted by the WP Highrise Contact plugin //-->
<link rel="stylesheet" type="text/css" href="<?php bloginfo('wpurl'); ?>/wp-content/plugins/wp-highrise-contact/wp-highrise-contact.css" />
<script type='text/javascript' src='<?php bloginfo('wpurl'); ?>/wp-content/plugins/wp-highrise-contact/js/jquery.validate.min.js?ver=2.9.1'></script>
<!-- end code inserted by the WP Highrise Contact plugin //-->
	<?php
}

function wphc_add_options_page() {
	add_options_page(__('Contact Form Options', 'wphc'), __('WP Highrise Contact', 'wphc'), 'manage_options', 'wp-highrise-contact/wp-highrise-contact-admin.php');
}

function wphc_text_domain() {
	load_plugin_textdomain( 'wphc', dirname(__FILE__) . '/languages', basename( dirname( __FILE__ ) ) . '/languages' );
}

/* Action calls for all functions */
add_action('init', 'wphc_text_domain');
add_action('admin_menu', 'wphc_add_options_page');
add_filter('wp_head', 'wphc_head');
add_filter('the_content', 'wphc_callback', 7);


?>