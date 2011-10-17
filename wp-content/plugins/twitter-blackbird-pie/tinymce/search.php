<?php

// look up for the path
require_once(  dirname(__FILE__).'/wpload.php');

global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));
	
	$username = $_GET['u'];
	$page = ($_GET['p']) ? $_GET['p'] : 1;
	$prev = $page + 1;
	$next = $page - 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Twitter Blackbird Pie Search Results</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/jquery/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo BBP_URL; ?>tinymce/date.format.js"></script>	
	<script language="javascript" type="text/javascript">
	
	jQuery(document).ready(function() {
		
		jQuery("#results tr td a").live("click", function(e) {
			
			e.preventDefault();
			var tweet_id = this.id;
			tinyMCEPopup.execCommand('set_tweet', false, tweet_id);
			tinyMCEPopup.close();
			
		});
		
		load_tweets();
	});
	
	function load_tweets() {
		var num_per_page = 8;
		var timeline_url = "http://twitter.com/statuses/user_timeline/<?php echo $username; ?>.json?callback=?&page=<?php echo $page; ?>&count="+num_per_page;
		var $results = jQuery("#results");
		
		jQuery.ajax({
			url: timeline_url, 
			dataType: 'json',
			success: function(data) {
				$results.empty();
				var cnt = 0;
				var cls = "";
				//Step through each tweet.
				jQuery.each(data, function(i, item) {
					if (item.id) {
						cls = (cnt % 2 == 0) ? " style='background:#ddd' " : "";
						cnt++;
						var num = ((<?php echo $page; ?>-1)*num_per_page) + cnt;
						//place the tweet within a paragraph tag within the main div. 
						var result = "<tr"+cls+"><td><a href='#' id="+item.id_str+"><b>"+num+".</b>"+item.text+"<span>("+dateFormat(item.created_at, 'd mmm yyyy HH:MM')+")</span></a></td></tr>";
						$results.append(result);
					}
				});
				
				$results.show();
				
				jQuery("#lnkprev").show();
				if (<?php echo $page; ?> > 1) { jQuery("#lnknext").show(); }
				
				jQuery("#msg").text("Click on the tweet you want to embed.");
			},
			error: function() {
				alert('There was a problem connecting to Twitter');
			}
		});	
	}
	
	</script>
	<base target="_self" />
	<style>
		#msg { font-weight:bold; margin-bottom:5px; }
		#results { display:none; border:solid 1px #000; }
		#results tr td a { display:block; padding:5px; text-decoration:none; }
		#results tr td a b { padding-right:5px; color:#369; }
		#results tr td a span { font-size:0.8em; color:#666; padding-left:5px; }
		#results tr td a:hover { background:#9cf; color:#369; }
	</style>
</head>
<body id="link">
	<form name="frmResults" action="#">
		<span id="msg">loading tweets for <?php echo $username; ?>...</span><br /><br />
		<table callpadding="5" cellspacing="0" id="results">
			<tr>
			<td>
			
			</td>
			</tr>
		</table>
		<br />
		<a style="display:none;padding-right:10px;" id="lnkprev" href="?u=<?php echo $username; ?>&p=<?php echo $prev; ?>">Previous</a>
		<a style="display:none" id="lnknext" href="?u=<?php echo $username; ?>&p=<?php echo $next; ?>">Next</a>
	</form>
</body>
</html>
<?php

?>
