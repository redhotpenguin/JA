<?php
/*
Plugin Name: Customize Your Community
Version: 1.2.1
Plugin URI: http://www.sugarrae.com/wordpress/cyc/
Description: Uses your current theme to display login, registration and profile pages instead of the WordPress look and feel.
Author: Joost de Valk
Author URI: http://yoast.com/
*/

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
 
// Guess the location
$cyc_pluginpath = WP_CONTENT_URL.'/plugins/'.basename(dirname(__FILE__)).'/';

// Set some defaults.
$cyc_options = array();
$cyc_options['afterheadercode'] 	= '';
$cyc_options['beforesidebarcode'] 	= '';
$cyc_options['aftersidebarcode'] 	= '';
add_option("cyc_options",$cyc_options);

$cyc_options = get_option("cyc_options");

function cyc_login_init() {
	require( ABSPATH . '/wp-load.php' );
		
	if (isset($_REQUEST["action"])) {
		$action = $_REQUEST["action"];
	} else {
		$action = 'login';
	}
	
	switch($action) {
		case 'lostpassword' :
		case 'retrievepassword' :
			cyc_password();
			break;
		case 'register':
			cyc_show_registerform();
			break;
		case 'login':
		default:
			cyc_show_loginform();
			break;
	}
	die();
}

function cyc_login_css ( ) {
?>
	<style type="text/css">
	form.loginform p label {
		width: 150px;
		display: block;
		float: left;
		clear: both;
	}
	form.loginform p input.input {
		width: 150px;
		float: left;
		clear: right;
	}
	form.loginform p img {
		width: 155px;
		float: left;		
	}
	form.loginform, form.loginform p {
		clear: both;
	}
	p.message, p#login_error {
		padding: 3px 5px;
	}
	p.message {
		background-color: lightyellow;
		border: 1px solid yellow;
	}
	p#login_error {
		background-color: lightcoral;
		border: 1px solid red;
		color: #000;
	}
	</style>
<?php
}

function cyc_profile_init() {

	function cyc_profile_js ( ) {
	?>
	<script type="text/javascript">
		function update_nickname ( ) {

			var nickname = jQuery('#nickname').val();
			var display_nickname = jQuery('#display_nickname').val();

			if ( nickname == '' ) {
				jQuery('#display_nickname').remove();
			}
			jQuery('#display_nickname').val(nickname).html(nickname);

		}

		jQuery(function($) { 
			$('#pass1').keyup( check_pass_strength ) 
			$('.color-palette').click(function(){$(this).siblings('input[name=admin_color]').attr('checked', 'checked')});
		} );

		jQuery(document).ready( function() {
			jQuery('#pass1,#pass2').attr('autocomplete','off');
			jQuery('#nickname').blur(update_nickname);
	    });
	</script>
	<?php
	}

	function cyc_profile_css ( ) {
	?>
		<style type="text/css">
		table.form-table th, table.form-table td {
			padding: 0;
		}
		table.form-table th {
			width: 150px;
			vertical-align: text-top;
			text-align: left;
		}
		p.message {
			padding: 3px 5px;
			background-color: lightyellow;
			border: 1px solid yellow;
		}
		#display_name {
			width: 250px;
		}
		.field-hint {
			display: block;
			clear: both;
		}
		</style>
	<?php
	}

	if ( !$user_id ) {
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
	}
	// If current user can see more of the admin area then just his profile, doing all this makes no sense.
	if ($current_user->has_cap('edit_posts') === false) {
		$is_profile_page = true;
	    add_filter('wp_title','cyc_title');
		add_action('wp_head', 'cyc_profile_js');
		add_action('wp_head', 'cyc_profile_css');
		
		wp_enqueue_script('jquery');

		wp_reset_vars(array('action', 'redirect', 'profile', 'user_id', 'wp_http_referer'));
		$wp_http_referer = remove_query_arg(array('update', 'delete_count'), stripslashes($wp_http_referer));
		$user_id = (int) $user_id;
	
		$profileuser = get_user_to_edit($user_id);
		if ( !current_user_can('edit_user', $user_id) )
				wp_die(__('You do not have permission to edit this user.'));

		cyc_head(__('Your Profile'));
		if ($_GET['updated'] == true) {
			echo '<p class="message">Your profile has been updated.</p>';
		}
 		?>

		<form name="profile" id="your-profile" action="" method="post">
		<?php wp_nonce_field('update-user_' . $user_id) ?>
		<?php if ( $wp_http_referer ) : ?>
			<input type="hidden" name="wp_http_referer" value="<?php echo clean_url($wp_http_referer); ?>" />
		<?php endif; ?>
		<p>
		<input type="hidden" name="from" value="profile" />
		<input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
		</p>

		<h3><?php _e('Name') ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="user_login"><?php _e('Username'); ?></label></th>
				<td><input type="text" name="user_login" id="user_login" value="<?php echo $profileuser->user_login; ?>" disabled="disabled" /> <?php _e('Your username cannot be changed'); ?></td>
			</tr>
			<tr>
				<th><label for="first_name"><?php _e('First name') ?></label></th>
				<td><input type="text" name="first_name" id="first_name" value="<?php echo $profileuser->first_name ?>" /></td>
			</tr>
			<tr>
				<th><label for="last_name"><?php _e('Last name') ?></label></th>
				<td><input type="text" name="last_name" id="last_name" value="<?php echo $profileuser->last_name ?>" /></td>
			</tr>
			<tr>
				<th><label for="nickname"><?php _e('Nickname') ?></label></th>
				<td><input type="text" name="nickname" id="nickname" value="<?php echo $profileuser->nickname ?>" /></td>
			</tr>
			<tr>
				<th><label for="display_name"><?php _e('Display name publicly&nbsp;as') ?></label></th>
				<td>
					<select name="display_name" id="display_name">
					<?php
						$public_display = array();
						$public_display['display_displayname'] = $profileuser->display_name;
						$public_display['display_nickname'] = $profileuser->nickname;
						$public_display['display_username'] = $profileuser->user_login;
						$public_display['display_firstname'] = $profileuser->first_name;
						$public_display['display_firstlast'] = $profileuser->first_name.' '.$profileuser->last_name;
						$public_display['display_lastfirst'] = $profileuser->last_name.' '.$profileuser->first_name;
						$public_display = array_unique(array_filter(array_map('trim', $public_display)));
						foreach($public_display as $id => $item) {
					?>
						<option id="<?php echo $id; ?>" value="<?php echo $item; ?>"><?php echo $item; ?></option>
					<?php
						}
					?>
					</select>
				</td>
			</tr>
		</table>

		<h3><?php _e('Contact Info') ?></h3>

		<table class="form-table">
		<tr>
			<th><label for="email"><?php _e('E-mail') ?></label></th>
			<td><input type="text" name="email" id="email" value="<?php echo $profileuser->user_email ?>" /> <?php _e('Required'); ?></td>
		</tr>

		<tr>
			<th><label for="url"><?php _e('Website') ?></label></th>
			<td><input type="text" name="url" id="url" value="<?php echo $profileuser->user_url ?>" /></td>
		</tr>

		<tr>
			<th><label for="aim"><?php _e('AIM') ?></label></th>
			<td><input type="text" name="aim" id="aim" value="<?php echo $profileuser->aim ?>" /></td>
		</tr>

		<tr>
			<th><label for="yim"><?php _e('Yahoo IM') ?></label></th>
			<td><input type="text" name="yim" id="yim" value="<?php echo $profileuser->yim ?>" /></td>
		</tr>

		<tr>
			<th><label for="jabber"><?php _e('Jabber / Google Talk') ?></label></th>
			<td><input type="text" name="jabber" id="jabber" value="<?php echo $profileuser->jabber ?>" /></td>
		</tr>
		</table>

		<h3><?php _e('About Yourself'); ?></h3>

		<table class="form-table">
		<tr>
			<th><label for="description"><?php _e('Biographical Info'); ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="30"><?php echo $profileuser->description ?></textarea><br /><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?><br/><br/></td>
		</tr>

		<?php
		$show_password_fields = apply_filters('show_password_fields', true);
		if ( $show_password_fields ) :
		?>
		<tr>
			<th><label for="pass1"><?php _e('New Password'); ?></label></th>
			<td>
				<input type="password" name="pass1" id="pass1" size="16" value="" /><br/><?php _e("If you would like to change the password type a new one. Otherwise leave this blank."); ?><br />
				<input type="password" name="pass2" id="pass2" size="16" value="" /><br/><?php _e("Type your new password again."); ?><br />
			</td>
		</tr>
		<?php endif; ?>
		</table>

		<?php
			do_action('profile_personal_options');
			do_action('show_user_profile');
		?>

		<?php if (count($profileuser->caps) > count($profileuser->roles)): ?>
		<br class="clear" />
			<table width="99%" style="border: none;" cellspacing="2" cellpadding="3" class="editform">
				<tr>
					<th scope="row"><?php _e('Additional Capabilities') ?></th>
					<td><?php
					$output = '';
					foreach($profileuser->caps as $cap => $value) {
						if(!$wp_roles->is_role($cap)) {
							if($output != '') $output .= ', ';
							$output .= $value ? $cap : "Denied: {$cap}";
						}
					}
					echo $output;
					?></td>
				</tr>
			</table>
		<?php endif; ?>

		<p class="submit">
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
			<input type="submit" id="cycsubmit" value="<?php $is_profile_page? _e('Update Profile') : _e('Update User') ?>" name="submit" />
		 </p>
		</form>
	<?php
		cyc_footer();
		die();
	}
}

function cyc_show_errors($wp_error) {
	global $error;
	
	if ( !empty( $error ) ) {
		$wp_error->add('error', $error);
		unset($error);
	}

	if ( !empty($wp_error) ) {
		if ( $wp_error->get_error_code() ) {
			$errors = '';
			$messages = '';
			foreach ( $wp_error->get_error_codes() as $code ) {
				$severity = $wp_error->get_error_data($code);
				foreach ( $wp_error->get_error_messages($code) as $error ) {
					if ( 'message' == $severity )
						$messages .= '	' . $error . "<br />\n";
					else
						$errors .= '	' . $error . "<br />\n";
				}
			}
			if ( !empty($errors) )
				echo '<p id="login_error">' . apply_filters('login_errors', $errors) . "</p>\n";
			if ( !empty($messages) )
				echo '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
		}
	}
}

function cyc_title($title) {
	global $pagenow;
	if ($pagenow == "wp-login.php") {
		switch($_GET['action']) {
			case 'register':
				$title = "Register at ";
				break;
			case 'lostpassword':
				$title = "Retrieve your lost password for ";
				break;
			case 'login':
			default:
				$title = "Login at ";
				break;
		}
	} else if ($pagenow == "profile.php") {
		$title = "Your Profile at ";
	}
	$title .= get_bloginfo('name');
	return $title;
}

function cyc_head($cyc_msg) {
	global $cyc_options;
	include(TEMPLATEPATH . '/header.php');
	$code = str_replace("%%title%%", $cyc_msg, $cyc_options['afterheadercode']);
	echo $code;
	echo "<h1>".__($cyc_msg)."</h1>";
}

function cyc_footer() {
	global $pagenow, $user_ID, $cyc_options;

	if ($pagenow == "wp-login.php") {
			// Show the appropriate options
			echo '<ul id="cycnav">'."\n";
			if (isset($_GET['action']) && $_GET['action'] != 'login') 
				echo '<li><a href="'.site_url('wp-login.php', 'login').'">'.__('Log in').'</a></li>'."\n";
			if (get_option('users_can_register') && $_GET['action'] != 'register')
				echo '<li><a href="'.site_url('wp-login.php?action=register', 'login').'">'.__('Register').'</a></li>'."\n";
			if ($_GET['action'] != 'lostpassword')
				echo '<li><a href="'.site_url('wp-login.php?action=lostpassword', 'login').'" title="'.__('Password Lost and Found').'">'.__('Lost your password?').'</a></li>'."\n";		
			echo '</ul>'."\n";

			// autofocus the username field  ?>
			<script type="text/javascript">try{document.getElementById('user_login').focus();}catch(e){}</script>
		<?php		
	} else if (isset($user_ID)){
		echo '<ul id="cycnav">'."\n";
		if (function_exists('wp_logout_url')) {
			echo '<li><a href="'.wp_logout_url().'">'.__('Log out').'</a></li>'."\n";
		} else {
			echo '<li><a href="'.site_url('wp-login.php?action=logout', 'logout').'">'.__('Log out').'</a></li>'."\n";			
		}
		echo '</ul>'."\n";
	}
	echo $cyc_options['beforesidebarcode'];
	if (function_exists('thesis_get_sidebars')) {
		thesis_get_sidebars();
	} else {
		include(TEMPLATEPATH . '/sidebar.php');
	}
	echo $cyc_options['aftersidebarcode'];
	include(TEMPLATEPATH . '/footer.php');
}

function cyc_password() {
	$errors = new WP_Error();
	if ( $_POST['user_login'] ) {
		$errors = retrieve_password();
		if ( !is_wp_error($errors) ) {
			wp_redirect('wp-login.php?checkemail=confirm');
			exit();
		}
	}
	
	if ( 'invalidkey' == $_GET['error'] ) 
		$errors->add('invalidkey', __('Sorry, that key does not appear to be valid.'));

	$errors->add('registermsg', __('Please enter your username or e-mail address. You will receive a new password via e-mail.'), 'message');
	do_action('lost_password');
	do_action('lostpassword_post');
	cyc_head("Lost Password");

	cyc_show_errors($errors);
?>
	<form class="loginform" name="lostpasswordform" id="lostpasswordform" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" method="post">
		<p>
			<label><?php _e('Username or E-mail:') ?></label>
			<input type="text" name="user_login" id="user_login" class="input" value="<?php echo attribute_escape(stripslashes($_POST['user_login'])); ?>" size="20" tabindex="10" />
		</p>
		<br/>
		<?php do_action('lostpassword_form'); ?>
		<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Get New Password'); ?>" tabindex="100" /></p>
	</form>
<?php
	cyc_footer();
}

function cyc_show_registerform() {
	global $cyc_pluginpath, $cyc_options;
	if ( !get_option('users_can_register') ) {
		wp_redirect(get_bloginfo('wpurl').'/wp-login.php?registration=disabled');
		exit();
	}

	$user_login = '';
	$user_email = '';
   
	if ( isset($_POST['user_login']) ) {
		if( !$cyc_options['captcha'] || ( $cyc_options['captcha'] && ($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code']) ) ) 
			) {
			unset($_SESSION['security_code']);
			require_once( ABSPATH . WPINC . '/registration.php');

			$user_login = $_POST['user_login'];
			$user_email = $_POST['user_email'];
			$errors = register_new_user($user_login, $user_email);
			if ( !is_wp_error($errors) ) {
				wp_redirect('wp-login.php?checkemail=registered');
				exit();
			}
		} else {
			$user_login = $_POST['user_login'];
			$user_email = $_POST['user_email'];
			$errors = new WP_error();
			$errors->add('captcha', __("<strong>ERROR</strong>: You didn't correctly enter the captcha, please try again."));		
		}
	}
	
	cyc_head("Register");
	cyc_show_errors($errors);
?>
	<form class="loginform" name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" method="post">
		<p>
			<label><?php _e('Username') ?>:</label>
			<input tabindex="1" type="text" name="user_login" id="user_login" class="input" value="<?php echo attribute_escape(stripslashes($user_login)); ?>" size="20" tabindex="10" />
			<label><?php _e('E-mail') ?>:</label>
			<input tabindex="2" type="text" name="user_email" id="user_email" class="input" value="<?php echo attribute_escape(stripslashes($user_email)); ?>" size="25" tabindex="20" />
<?php if ($cyc_options['captcha']) { ?>
			<label>&nbsp;</label>
			<img alt="captcha" width="155" height="30" src="<?php echo $cyc_pluginpath; ?>captcha.php?width=155&amp;height=30&amp;characters=5" /><br/>
			<label for="security_code">Type the code above:</label>
			<input tabindex="3" id="security_code" name="security_code" class="input" type="text" />
<?php } ?>
		</p>
		<?php do_action('register_form'); ?>
		<p id="reg_passmail"><?php _e('A password will be e-mailed to you.') ?></p>
		<p class="submit"><input tabindex="4" type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Register'); ?>" tabindex="100" /></p>
	</form>
<?php
	cyc_footer();
}

function cyc_show_loginform() {
	if ( isset( $_REQUEST['redirect_to'] ) )
		$redirect_to = $_REQUEST['redirect_to'];
	else
		$redirect_to = admin_url();

	if ( is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;
	else
		$secure_cookie = '';

	$user = wp_signon('', $secure_cookie);

	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);

	if ( !is_wp_error($user) ) {
		// If the user can't edit posts, send them to their profile.
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' ) )
			$redirect_to = admin_url('profile.php');
		wp_safe_redirect($redirect_to);
		exit();
	}

	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) )
		$errors = new WP_Error();

	cyc_head("Login");	

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));		
	if	( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )			$errors->add('loggedout', __('You are now logged out.'), 'message');
	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )	$errors->add('registerdisabled', __('User registration is currently not allowed.'));
	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )	$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )	$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )	$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');

	cyc_show_errors($errors);

	// login form
	?>
	<form class="loginform" action="<?php bloginfo('wpurl'); ?>/wp-login.php" method="post" >
		<p>
			<label for="user_login"><?php _e('Username:') ?></label>
			<input name="log" value="<?php echo attribute_escape(stripslashes($_POST['log'])); ?>" class="mid" id="user_login" type="text" />
			<br/>
			<label for="user_pass"><?php _e('Password:') ?></label>
			<input name="pwd" class="mid" id="user_pass" type="password" />
			<br/>
			<input name="rememberme" class="checkbox" id="rememberme" value="forever" type="checkbox" checked="checked"/>
			<label for="rememberme"><?php _e('Remember me'); ?></label>
		</p>
		<p class="submit">
			<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Login'); ?> &raquo;" />
			<input type="hidden" name="testcookie" value="1" />
		</p>
	</form>
	<?php	
	cyc_footer();
}

function cyc_redirect($redirect_to, $request_redirect_to, $user) {
	if (is_a($user, 'WP_User') && $user->has_cap('edit_posts') === false) {
		return get_bloginfo('wpurl').'/wp-admin/profile.php'; 
	}
	return $redirect_to;
}

global $pagenow; 
/* 
Main Plugin Call For Login:
Init the script when current page is wp-login.php, but don't init it when: 
- the form has been submitted to either log in or register
- the user is trying to log out
- the user is trying to reset his pass

In any of these cases, let wp-login.php itself take care of the actions. It will automatically redirect back to wp-login.php
with a message in most cases, at which point the plugin CAN handle it.

Also, make sure the title is set properly for the current page and action.
*/

if ( $pagenow == "wp-login.php"  && $_GET['action'] != 'logout' && !isset($_GET['key']) ) {
	add_action('init', 'cyc_login_init', 98);
	add_filter('wp_title','cyc_title');
	add_action('wp_head', 'cyc_login_css');
}

/*
Main Plugin call for Profile Page:
Init the script when current page is profile.php, but don't init it when:
- a form has been submitted (the original PHP file should take care of form submissions)
- user has write and/or edit rights on the blog, he/she can see the backend anyway, so why not for this page?
*/

if ( !isset($_POST['from']) && $_POST['from'] != 'profile' ) {
	add_action('load-profile.php', 'cyc_profile_init', 98);
}

// If the current user has no edit rights, redirect them to their profile page instead of the dashboard
add_filter('login_redirect', 'cyc_redirect', 10, 3);

if ( ! class_exists( 'CYC_Admin' ) ) {

	class CYC_Admin {
		
		function add_config_page() {
			global $wpdb;
			if ( function_exists('add_submenu_page') ) {
				add_options_page('CYC Configuration', 'CYC', 10, basename(__FILE__), array('CYC_Admin','config_page'));
			}
		}
		
		function config_page() {			
			// Overwrite defaults with saved settings
			if ( isset($_POST['submit']) ) {
				if (!current_user_can('manage_options')) die(__('You cannot edit the CYC options.'));
				check_admin_referer('cyc-config');

				foreach (array('captcha') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = true;
					} else {
						$options[$option_name] = false;
					}
				}

				foreach (array('afterheadercode', 'beforesidebarcode', 'aftersidebarcode') as $option_name) {
					if (isset($_POST[$option_name])) {
						$options[$option_name] = stripslashes($_POST[$option_name]);
					}
				}

				update_option('cyc_options', $options);
				echo "<div id=\"message\" class=\"updated fade\"><p>Settings Updated.</p></div>\n";					
			}
			
			$options = get_option('cyc_options');
			?>
			<div class="updated">
				<p>
					If you like this plugin, please help us keeping it up to date by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=17698">donating a small token of your appreciation through PayPal</a>! 
				</p>
			</div>
			<div class="wrap">
				<h2>CYC options</h2>
				<form action="" method="post" id="cyc-conf">
					<?php
					if ( function_exists('wp_nonce_field') )
						wp_nonce_field('cyc-config');
					?>
					<table class="form-table" style="width: 100%;">
						<tr valign="top">
							<th scrope="row" width="10%">
								<label for="afterheadercode">Code to insert after the header has been loaded:</label>
							</th>
							<td width="90%">
								<textarea name="afterheadercode" id="afterheadercode" rows="6" cols="70"><?php echo $options['afterheadercode']; ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scrope="row">
								<label for="beforesidebarcode">Code to insert before the sidebar is loaded:</label>
							</th>
							<td>
								<textarea name="beforesidebarcode" id="beforesidebarcode" rows="6" cols="70"><?php echo $options['beforesidebarcode']; ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scrope="row">
								<label for="aftersidebarcode">Code to insert after the sidebar has been loaded:</label>
							</th>
							<td>
								<textarea name="aftersidebarcode" id="aftersidebarcode" rows="6" cols="70"><?php echo $options['aftersidebarcode']; ?></textarea>
							</td>
						</tr>
						<tr valign="top">
							<th scrope="row">
								<label for="captcha">Show a Captcha?</label>
							</th>
							<td>
								<input type="checkbox" name="captcha" id="captcha" <?php if ($options['captcha']) { echo 'checked="checked"'; } ?>/>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" name="submit" value="Update Settings &raquo;" /></p>
				</form>
				<h3>Help</h3>
				<p>
					If you have any issues with this plugin, please check the FAQ and manual, found <a href="http://www.sugarrae.com/wordpress/cyc/">here</a>.
				</p>
			</div>
<?php		}	
	}
}

add_action('admin_menu', array('CYC_Admin','add_config_page'));
?>