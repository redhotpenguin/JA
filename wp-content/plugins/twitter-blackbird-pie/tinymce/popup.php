<?php

// look up for the path
require_once(  dirname(__FILE__).'/wpload.php');

global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Twitter Blackbird Pie</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>	
	<script language="javascript" type="text/javascript">
	
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertblackbirdpieLink() {
		
		var tagtext;
		
		var panel1 = document.getElementById('blackbirdpie_panel1');
		var panel2 = document.getElementById('blackbirdpie_panel2');
		var panel3 = document.getElementById('blackbirdpie_panel3');
		
		
		// who is active ?
		if (panel1.className.indexOf('current') != -1) {
			var tweeturl = document.getElementById('tweeturl').value;
				
			if (tweeturl != '' ) {
                            tagtext = '[blackbirdpie url="' + tweeturl + '"]';
                        } else {
                            tinyMCEPopup.close();
                        }
				
		} else if (panel2.className.indexOf('current') != -1) {
			var tweetid = document.getElementById('tweetid').value;
			
			if (tweetid != '' ) {
                            tagtext = '[blackbirdpie id="' + tweetid + '"]';
                        } else {
                            tinyMCEPopup.close();
                        }
		}
		
		if(window.tinyMCE) {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
			//Peforms a clean up of the current editor HTML. 
			//tinyMCEPopup.editor.execCommand('mceCleanup');
			//Repaints the editor. Sometimes the browser has graphic glitches. 
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
		
		return;
	}
	
	jQuery(document).ready(function() {
		jQuery("#lnkSearch").click(function(e) {
			e.preventDefault();
			
			var $link = jQuery(this);
			
			var username = jQuery("#username").val();
			if (username.length == 0) { 
				alert('Please enter a username');
			} else {
				var tinymce_url = tinyMCEPopup.editor.baseURI.getURI();
				
				tinyMCEPopup.editor.addCommand('set_tweet', function(ui, v) {
					mcTabs.displayTab('blackbirdpie_tab2','blackbirdpie_panel2');
					jQuery("#tweetid").val(v);
					insertblackbirdpieLink();
					return true;
				});
				
				tinyMCEPopup.editor.windowManager.open({
					file : '<?php echo WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ); ?>/search.php?u=' + escape(username),
					width : 450,
					height : 360,
					inline : 1
				}, {
					plugin_url : tinymce_url // Plugin absolute URL
				});
			}
		});
	});
	

	</script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('tweeturl').focus();" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	Choose a method to embed a tweet into your post<br /><br />
	<form name="frmBlackbirdPir" action="#">
	<div class="tabs">
		<ul>
			<li id="blackbirdpie_tab1" class="current"><span><a href="javascript:mcTabs.displayTab('blackbirdpie_tab1','blackbirdpie_panel1');" onmousedown="return false;"><?php _e("URL", 'blackbirdpie'); ?></a></span></li>		
			<li id="blackbirdpie_tab2"><span><a href="javascript:mcTabs.displayTab('blackbirdpie_tab2','blackbirdpie_panel2');" onmousedown="return false;"><?php _e("ID", 'blackbirdpie'); ?></a></span></li>
			<li id="blackbirdpie_tab3"><span><a href="javascript:mcTabs.displayTab('blackbirdpie_tab3','blackbirdpie_panel3');" onmousedown="return false;"><?php _e("Search", 'blackbirdpie'); ?></a></span></li>			
		</ul>
	</div>
	
	<div class="panel_wrapper"> 
		<!-- blackbirdpie panel 1-->
		<div id="blackbirdpie_panel1" class="panel current">
		<br />
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap"><label for="tweeturl"><?php _e("Tweet URL:", 'blackbirdpie'); ?></label></td>
            <td><input type="text" id="tweeturl" name="tweeturl" style="width: 180px" />
            </td>
          </tr>
		  <tr>
			<td colspan="2">e.g. 'http://twitter.com/jack/status/20'</td>
		  </tr>
		  <tr>
			<td colspan="2" align="center" style="background:#eee">
				<a target="_blank" title="Donate to Blakbird Pie WordPress Plugin" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BQKQXLQ72EWJ2"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" alt="Donate" style="border:none !important">
				</a>
				<br />Please support the future development of this plugin by donating. Thank you.
			</td>
		  </tr>
        </table>
		</div>
		<!-- end blackbirdpie panel 1-->
		
		<!-- blackbirdpie panel 2-->
		<div id="blackbirdpie_panel2" class="panel">
		<br />
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap"><label for="tweetid"><?php _e("Tweet ID:", 'blackbirdpie'); ?></label></td>
            <td><input type="text" id="tweetid" name="tweetid" style="width: 120px" />
            </td>
          </tr>
		  <tr>
			<td colspan="2">e.g. '20446117533'</td>
		  </tr>          
        </table>
		</div>
		<!-- end blackbirdpie panel 2-->		
		
		<!-- blackbirdpie panel 3-->
		<div id="blackbirdpie_panel3" class="panel">
		<br />
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
            <td nowrap="nowrap"><label for="username"><?php _e("Twitter username:", 'blackbirdpie'); ?></label></td>
            <td><input type="text" id="username" name="username" style="width: 120px" />
			<a href="#" id="lnkSearch">Search</a>
            </td>
          </tr>
        </table>
		</div>
		<!-- end blackbirdpie panel -->		
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'blackbirdpie'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'blackbirdpie'); ?>" onclick="insertblackbirdpieLink();" />
		</div>
	</div>
</form>
</body>
</html>
<?php

?>
