<?php
/*
Plugin Name: Qwerty Admin Panel Theme
Plugin URI: http://qwertyuiopia.com/2008/04/23/qwerty-admin-panel-theme-plugin-for-wordpress
Description: This plugin customizes the admin panel style sheet for all users (Appearance --> Qwerty Admin Panel Theme).
Author: Tasos Alvas
Version: 0.3
Author URI: http://qwertyuiopia.com/
*/

/*
	Copyrights of the theme and related images remain the property of Qwertyuiopia.com
	
	The Qwerty admin css plugin is released under the GNU General Public License.

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
    
*/


// Hooked to the admin theme headers
function qwerty_admin_header() {
	global $qwerty_admin_options, $parent_file;
	$site_url = get_settings('siteurl');
	$plugin_url = $site_url . '/wp-content/plugins/qwerty-admin-panel-theme-plugin/';

	echo '<link rel="stylesheet" type="text/css" href="' . $plugin_url . 'qwerty-admin.css?version=0.3" />';
	echo '<link rel="Shortcut Icon" href="' . $siteurl . '/favicon.ico"/>';

// Custom image check
	if (file_exists('./wp-content/plugins/qwerty-admin-panel-theme-plugin/images/logo-login.png')) {
		$qwerty_png_logologin = 'logo-login.png';
	} else {
		$qwerty_png_logologin = 'qwerty-logo-login.png';
	}

	if (file_exists('../wp-content/plugins/qwerty-admin-panel-theme-plugin/images/logo-ghost.png')) {
		$qwerty_png_logoghost = 'logo-ghost.png';
	} else {
		$qwerty_png_logoghost = 'qwerty-logo-ghost.png';
	}

	if (file_exists('../wp-content/plugins/qwerty-admin-panel-theme-plugin/images/logo-head.png')) {
		$qwerty_png_logohead = 'logo-head.png';
	} else {
		$qwerty_png_logohead = 'qwerty-logo-head.png';
	}

// Limited interface
	if (current_user_can('limited_interface')) {

		if ($qwerty_admin_options['nodashboard'] == 'true') {
			if ($parent_file == 'index.php') {
				if ( !headers_sent() ) {
					wp_redirect('post-new.php');
					exit();
				} else {
					$qwerty_hide_dest_url = get_option('siteurl') . "/wp-admin/post-new.php";
	
					?>

					<meta http-equiv="Refresh" content="0; URL=<?php echo $qwerty_hide_dest_url; ?>">
					<script type="text/javascript">
					<!--
						document.location.href = "<?php echo $qwerty_hide_dest_url; ?>"
					//-->
					</script>
					</head>
					<body>
					Sorry. Please use this <a href="<?php echo $qwerty_hide_dest_url; ?>" title="Your Profile">link</a>.
					</body>
					</html>
	
					<?php
					exit();
				}
			}
		}

	echo '<link rel="stylesheet" type="text/css" href="' . $plugin_url . 'qwerty-admin-limited.css" />';
	echo "<style type='text/css'>";

	if ($qwerty_admin_options['nomedia'] == 'true') {
		echo "li#menu-media { display: none; }";
	}
		
	if ($qwerty_admin_options['nocomments'] == 'true') {
		echo "li#menu-comments { display:none; }";
	}

	if ($qwerty_admin_options['nodashboard'] == 'true') {
		echo "li#menu-dashboard { display:none; }";
	}

	if ($qwerty_admin_options['noactions'] == 'true') {
		echo "div#favorite-actions { display:none; }";
	}

	echo "</style>";
	}

//Styles
	echo "
	<style type='text/css'>
	#wphead h1 a, .login #backtoblog a, #user_info a:link, #user_info a:visited, #footer a:link, #footer a:visited { color: " . $qwerty_admin_options['headerone'] . ";}

	#adminmenu a { color: " . $qwerty_admin_options['color'] . "; }

	a { color: " . $qwerty_admin_options['linkcolor'] . "; }

	a.hover { color: " . $qwerty_admin_options['currenthover'] . "; }

	body, #wpbody, .form-table .pre { color: " . $qwerty_admin_options['bodytextcolor'] . "; }

	#wphead { background-color: " . $qwerty_admin_options['headbackground'] . " !important;}

	#sidemenu a, body.login { border-top-color: " . $qwerty_admin_options['headbackground'] . ";}

	.login, html, body, #wpbody { background-color: " . $qwerty_admin_options['subbackground'] . "; }

	.wp_themeSkin tr.mceFirst td.mceToolbar, .alternate, .alt, #adminmenu a.menu-top { background-color: " . $qwerty_admin_options['lightwash'] . " !important; }

	.wp_themeSkin tr.mceFirst td.mceToolbar, .tablenav .next:hover, .tablenav .prev:hover, .tablenav .next, .tablenav .prev, div.ui-tabs-panel, .tablenav .dots { border-color: " . $qwerty_admin_options['lightwash'] . "; }

	.mceMenu .mceMenuItemActive { background-color: " . $qwerty_admin_options['darkwash'] . "; }
	
	#footer, #footer-upgrade { background-color: " . $qwerty_admin_options['stripe'] . "; }

.login h1 a {
	background: url(" . $plugin_url . "images/" . $qwerty_png_logologin . ") no-repeat !important;
	width: 292px;
	height: 66px;
	text-indent: -9999px;
	overflow: hidden;
	padding-bottom: 15px;
	display: block;
}

#wphead {
	background: url(" . $plugin_url . "images/" . $qwerty_png_logohead . ") no-repeat right;
	}

img#header-logo {
	background: url(" . $plugin_url . "images/" . $qwerty_png_logoghost . ") no-repeat;
	}

	</style>";
}

// hook up functions
add_action('admin_head', 'qwerty_admin_header');
add_action('login_head', 'qwerty_admin_header');

// Default options - my current option names are crap, I know.
$qwerty_admin_defaultOptions = array(
	'headerone'=>'#f29100'
	,'color'=>'#516fdb'
	,'bodytextcolor'=>'#333333'
	,'linkcolor'=>'#9c5d00'
	,'currenthover'=>'#9966cc'
	,'headbackground'=>'#f7f7f7'
	,'subbackground'=>'#ffffff'
	,'lightwash'=>'#ffebcc'
	,'darkwash'=>'#d5d9e8'
	,'stripe'=>'#f7f7f7'
	,'nomedia'=>'false'
	,'nocomments'=>'false'
	,'nodashboard'=>'false'
	,'noactions'=>'false'
	);

add_option('qwerty_admin_options',$qwerty_admin_defaultOptions,'Qwerty admin css Plugin Options');

// Retrieve current options
$qwerty_admin_options = get_option('qwerty_admin_options');

// Checking all options are in
$qwerty_admin_missingOption = false;
foreach($qwerty_admin_defaultOptions as $k=>$v) {
    if(!isset($qwerty_admin_options[$k])) {
        $qwerty_admin_options[$k] = $v;
        $qwerty_admin__missingOption = true;
    }
}
if(qwerty_admin_missingOption) {
    update_option('qwerty_admin_options', $qwerty_admin_options);
}

function qwerty_admin_options_page() {
	if (function_exists('add_options_page')) {
		add_theme_page('Qwerty Admin Panel Theme', 'Qwerty Admin Panel Theme', 8, basename(__FILE__), 'qwerty_admin_optionsPage');
	}
}

// Options Page
function qwerty_admin_optionsPage() {
	global $qwerty_admin_options;
	global $qwerty_admin_defaultOptions;
	
	if (isset($_POST['update_qwerty'])) { ?>
	<div id="message" class="updated fade"><p><strong>
		<?php 
		// Verify color values and update submitted info
		if(!(preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_headerone']) &&
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_color']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_bodytextcolor']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_linkcolor']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_currenthover']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_headbackground']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_subbackground']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_lightwash']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_darkwash']) && 
				preg_match('%^#[ABCDEFabcdef\d]{6}$%',$_POST['qwerty_admin_stripe']))) {
			echo 'Colour options must be in valid RGB format. (Ex: #996627) Try again.';
		} else {
			// Options are valid, save them
			foreach($qwerty_admin_options as $k=>$v) {
				$qwerty_admin_options[$k] = $_POST['qwerty_admin_'.$k];
			}
			update_option('qwerty_admin_options', $qwerty_admin_options);
			echo 'Qwerty admin css options have been updated successfully. You might need to refresh the page once to see the changes.';
		}
	?>
	</strong></p></div>
	<?php } ?>
	<div class=wrap>
		<form method="post">
			<h2>Qwerty Admin Panel Theme Options</h2>
			<p><fieldset name="qwerty_colour">
				<legend><strong>Colours</strong></legend>
				<h4>Text</h4>
				<table>

				<tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_headerone">Header and Footer:</label></td>
				<td><input name="qwerty_admin_headerone" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['headerone']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['headerone']; ?>)</td>

				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_bodytextcolor">Page body</label></td>
				<td><input name="qwerty_admin_bodytextcolor" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['bodytextcolor']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['bodytextcolor']; ?>)</td>

				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_color">Navigation Links</label></td>
				<td><input name="qwerty_admin_color" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['color']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['color']; ?>)</td>
				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_linkcolor">Content Links</label></td>
				<td><input name="qwerty_admin_linkcolor" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['linkcolor']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['linkcolor']; ?>)</td>

				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_currenthover">Current menu tab and links on Hover</label></td>
				<td><input name="qwerty_admin_currenthover" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['currenthover']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['currenthover']; ?>)</td>

				</tr>
				</table>
				<h4>Backgrounds</h4>
				<table>

				<tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_headbackground">Header stripe:</label></td>
				<td><input name="qwerty_admin_headbackground" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['headbackground']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['headbackground']; ?>)</td>

				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_subbackground">Page body:</label></td> 
				<td><input name="qwerty_admin_subbackground" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['subbackground']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['subbackground']; ?>)</td>

				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_lightwash">Light wash:</label></td> 
				<td><input name="qwerty_admin_lightwash" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['lightwash']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['lightwash']; ?>)</td>

				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_darkwash">Dark wash:</label></td> 
				<td><input name="qwerty_admin_darkwash" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['darkwash']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['darkwash']; ?>)</td>

				</tr><tr><td class="qwerty_fieldnametd"><label for="qwerty_admin_stripe">Footer stripe:</label></td> 
				<td><input name="qwerty_admin_stripe" type="text" maxlength"7" class="colorpicker" value="<?php echo $qwerty_admin_options['stripe']; ?>">(default: <?php echo $qwerty_admin_defaultOptions['stripe']; ?>)</td>

				</table>
				<h4>Limited interface</h4>
<p>These options allow you to hide specific parts of the admin interface for users with the "<strong>Limited Interface</strong>" capability. You will need a <a href="http://codex.wordpress.org/Roles_and_Capabilities#Resources">plugin to manage capabilities</a> to use this feature.</p>
				<table>

<tr><td class="qwerty_fieldnametd">Hide "Dashboard" and redirect to New Post</td> 
				<td><label for="qwerty_admin_nodashboard"><input type="radio" id="qwerty_admin_nodashboard_yes" name="qwerty_admin_nodashboard" value="true" <?php if ($qwerty_admin_options['nodashboard'] == "true") { echo " checked"; }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="qwerty_admin_nodashboard_no"><input type="radio" id="qwerty_admin_nodashboard_no" name="qwerty_admin_nodashboard" value="false" <?php if ($qwerty_admin_options['nodashboard'] == "false") { echo " checked"; }?>/> No</label></td>


<tr><td class="qwerty_fieldnametd">Hide "Media"</td> 
				<td><label for="qwerty_admin_nomedia"><input type="radio" id="qwerty_admin_nomedia_yes" name="qwerty_admin_nomedia" value="true" <?php if ($qwerty_admin_options['nomedia'] == "true") { echo " checked"; }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="qwerty_admin_nomedia_no"><input type="radio" id="qwerty_admin_nomedia_no" name="qwerty_admin_nomedia" value="false" <?php if ($qwerty_admin_options['nomedia'] == "false") { echo " checked"; }?>/> No</label></td>

<tr><td class="qwerty_fieldnametd">Hide "Comments"</td> 
				<td><label for="qwerty_admin_nocomments"><input type="radio" id="qwerty_admin_nocomments_yes" name="qwerty_admin_nocomments" value="true" <?php if ($qwerty_admin_options['nocomments'] == "true") { echo " checked"; }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="qwerty_admin_nocomments_no"><input type="radio" id="qwerty_admin_nocomments_no" name="qwerty_admin_nocomments" value="false" <?php if ($qwerty_admin_options['nocomments'] == "false") { echo " checked"; }?>/> No</label></td>

<tr><td class="qwerty_fieldnametd">Hide "Favorite actions" box</td> 
				<td><label for="qwerty_admin_noactions"><input type="radio" id="qwerty_admin_noactions_yes" name="qwerty_admin_noactions" value="true" <?php if ($qwerty_admin_options['noactions'] == "true") { echo " checked"; }?> /> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="qwerty_admin_noactions_no"><input type="radio" id="qwerty_admin_noactions_no" name="qwerty_admin_noactions" value="false" <?php if ($qwerty_admin_options['noactions'] == "false") { echo " checked"; }?>/> No</label></td>
				</table>

			</fieldset></p>
			<div class="submit"><input type="submit" name="update_qwerty" value="Update Options" /></div>
		</form>
	</div>
<?php
}

// hook up options page
add_action('admin_menu', 'qwerty_admin_options_page');
?>
