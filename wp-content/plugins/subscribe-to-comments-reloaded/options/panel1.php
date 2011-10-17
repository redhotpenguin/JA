<?php
// Avoid direct access to this piece of code
if (strpos($_SERVER['SCRIPT_FILENAME'], basename(__FILE__))){
	header('Location: /');
	exit;
}
$manager_link = get_bloginfo('url').get_option('subscribe_reloaded_manager_page', '');
$manager_link = (strpos($manager_link, '?') !== false)?"$manager_link&amp;srp=":"$manager_link?srp=";
$clean_email = !empty($_POST['sre'])?$wp_subscribe_reloaded->clean_email($_POST['sre']):(!empty($_GET['sre'])?$wp_subscribe_reloaded->clean_email($_GET['sre']):'');

// Update the information
if (!empty($_POST['subscription_list']) && !empty($_POST['action_type'])){	
	foreach($_POST['subscription_list'] as $a_subscription){
		list($post_list_array[],$email_list_array[]) = explode(',', $a_subscription);
	}
	$post_list = implode("','", $post_list_array);
	$email_list = implode("','", $email_list_array);
	$rows_affected = 0;
	switch($_POST['action_type']){
		case 'd':
			$rows_affected = $wpdb->query("DELETE FROM $wp_subscribe_reloaded->table_subscriptions WHERE `post_ID` IN ('$post_list') AND `email` IN ('$email_list')");
			break;
		case 's':
			$rows_affected = $wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'N' WHERE `post_ID` IN ('$post_list') AND `email` IN ('$email_list')");
			break;
		case 'a':
			$rows_affected = $wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'Y' WHERE `post_ID` IN ('$post_list') AND `email` IN ('$email_list')");
			break;
		default:
			break;
	}
	echo '<div class="updated fade"><p>'.__('The status has been successfully updated. Rows affected:', 'subscribe-reloaded')." $rows_affected</p></div>";
}
elseif (!empty($_POST['old_sre']) && !empty($_POST['action_type']) && !empty($_POST['post_ID']) && $_POST['action_type'] == 'u'){
	$clean_old_email = !empty($_POST['old_sre'])?$wp_subscribe_reloaded->clean_email($_POST['old_sre']):(!empty($_GET['old_sre'])?$wp_subscribe_reloaded->clean_email($_GET['old_sre']):'');
	$post_ID = !empty($_POST['post_ID'])?intval($_POST['post_ID']):(!empty($_GET['post_ID'])?intval($_GET['srt']):0);
	$rows_affected = $wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `email` = '$clean_email' WHERE `email` = '$clean_old_email' AND `post_ID` = '$post_ID'");
	echo '<div class="updated fade"><p>'.__('The status has been successfully updated. Rows affected:', 'subscribe-reloaded')." $rows_affected</p></div>";
}

// Mass-update an email address
elseif (!empty($_POST['old_sre']) && !empty($_POST['action_type']) && $_POST['action_type'] == 'u'){
	$clean_old_email = !empty($_POST['old_sre'])?$wp_subscribe_reloaded->clean_email($_POST['old_sre']):(!empty($_GET['old_sre'])?$wp_subscribe_reloaded->clean_email($_GET['old_sre']):'');
	$rows_affected = $wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `email` = '$clean_email' WHERE `email` = '$clean_old_email'");
	echo '<div class="updated fade"><p>'.__('The status has been successfully updated. Rows affected:', 'subscribe-reloaded')." $rows_affected</p></div>";
}

// Mass-update the status of a subscriber
elseif (!empty($clean_email) && !empty($_POST['action_type'])){
	switch($_POST['action_type']){
		case 'd':
			$rows_affected = $wpdb->query("DELETE FROM $wp_subscribe_reloaded->table_subscriptions WHERE `email` = '$clean_email'");
			break;
		case 's':
			$rows_affected = $wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'N' WHERE `email` = '$clean_email'");
			break;
		case 'a':
			$rows_affected = $wpdb->query("UPDATE $wp_subscribe_reloaded->table_subscriptions SET `status` = 'Y' WHERE `email` = '$clean_email'");
			break;
		default:
			break;
	}
	echo '<div class="updated fade"><p>'.__('The status has been successfully updated. Rows affected:', 'subscribe-reloaded')." $rows_affected</p></div>";
}
?>

<div class="postbox one-third">
<h3 class='hndle'><?php _e('Update subscription','subscribe-reloaded') ?></h3>
<p><?php _e('Update the email address associated to a specific subscription (by post ID).','subscribe-reloaded') ?></p>
<form action="options-general.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=1" method="post" id="update_address_form"
	onsubmit="return confirm('<?php _e('Please remember: this operation cannot be undone. Are you sure you want to proceed?', 'subscribe-reloaded') ?>')">
<fieldset style="border:0">
<p style="float:left"><?php _e('From','subscribe-reloaded') ?> &nbsp;<input type='text' size='15' name='old_sre' value='' /></p>
<p style="float:left;padding-left:0"><?php _e('PID','subscribe-reloaded') ?> &nbsp;<input type='text' size='6' name='post_ID' value='' /></p>
<p style="float:left"><?php _e('To','subscribe-reloaded') ?> &nbsp; <input type='text' size='20' name='sre' value='' /></p>
<p style="float:left"><input type='submit' class='subscribe-form-button' value='<?php _e('Update','subscribe-reloaded') ?>' /></p>
<input type='hidden' name='action_type' value='u'/>
</fieldset>
</form>
</div>

<div class="postbox one-third">
<h3><?php _e('Change status','subscribe-reloaded') ?></h3>
<p><?php _e('Change the status of an email address or permanently delete all its subscriptions.','subscribe-reloaded') ?></p>
<form action="options-general.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=1" method="post">
<p style="float:left"><input type="text" size="35" name="sre" value="" /></p>
<p style="float:left"><?php _e('Action','subscribe-reloaded') ?>: <select name="action_type" style="width:10em">
	<option value=''>-------------</option>
	<option value='s'><?php _e('Suspend','subscribe-reloaded') ?></option>
	<option value='a'><?php _e('Resume','subscribe-reloaded') ?></option>
	<option value='d'><?php _e('Delete forever','subscribe-reloaded') ?></option>
</select></p>
<p style="float:left"><input type="submit" class="subscribe-form-button" value="<?php _e('Update','subscribe-reloaded') ?>" /></p>
</form>
</div>

<div class="postbox one-third">
<h3 class='hndle'><?php _e('Update email address','subscribe-reloaded') ?></h3>
<p><?php _e('You can "mass update" all the occurrences of a given email address (exact matches only).','subscribe-reloaded') ?></p>
<form action="options-general.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=1" method="post" id="update_address_form"
	onsubmit="return confirm('<?php _e('Please remember: this operation cannot be undone. Are you sure you want to proceed?', 'subscribe-reloaded') ?>')">
<fieldset style="border:0">
<p style="float:left"><?php _e('From','subscribe-reloaded') ?> &nbsp;<input type='text' size='20' name='old_sre' value='' /></p>
<p style="float:left"><?php _e('To','subscribe-reloaded') ?> &nbsp; <input type='text' size='20' name='sre' value='' /></p>
<p style="float:left"><input type='submit' class='subscribe-form-button' value='<?php _e('Update','subscribe-reloaded') ?>' /></p>
<input type='hidden' name='action_type' value='u'/>
</fieldset>
</form>
</div>

<div class="postbox" style="min-height:0">
<p class="subscribe-list-navigation"><?php 
if (!empty($clean_email)){
	$search_type = !empty($_POST['srt'])?$_POST['srt']:(!empty($_GET['srt'])?$_GET['srt']:'');
	$starting_from = !empty($_POST['starting'])?intval($_POST['starting']):(!empty($_GET['starting'])?intval($_GET['starting']):0);
	$order = !empty($_POST['sro'])?$_POST['sro']:(!empty($_GET['sro'])?$_GET['sro']:'DESC');
	if ($order != 'ASC' && $order != 'DESC') $order = 'DESC';
	$order_by = !empty($_POST['srob'])?$_POST['srob']:(!empty($_GET['srob'])?$_GET['srob']:'dt');
	if ($order_by != 'dt' && $order_by != 'status' && $order_by != 'post_ID') $order_by = 'dt';

	switch($search_type){
		case 'c':
			$where_clause = "`email` LIKE '%$clean_email%'";
			break;
		case 'n':
			$where_clause = "`email` NOT LIKE '%$clean_email%'";
			break;
		default:
			$where_clause = "`email` = '$clean_email'";					
			break;
	}
	$sql = "
		SELECT `email`, `status`, `post_ID`, `dt`
		FROM $wp_subscribe_reloaded->table_subscriptions
		WHERE $where_clause
		ORDER BY `$order_by` $order
		LIMIT $starting_from,25";
	$sql_total = "
		SELECT COUNT(*)
		FROM $wp_subscribe_reloaded->table_subscriptions
		WHERE $where_clause";
				
	$subscriptions = $wpdb->get_results($sql, OBJECT);
	$count_total = $wpdb->get_var($sql_total);

	$count_results = count($subscriptions); // 0 if $results is null
	$ending_to = min($count_total, $starting_from+25);
	if ($starting_from > 0){
		$new_starting = ($starting_from > 25)?$starting_from-25:0;
		echo "<a href='options-general.php?page=subscribe-to-comments-reloaded/options/index.php&amp;subscribepanel=1&amp;starting=$new_starting&amp;sre=$clean_email&amp;srt=$search_type'>".__('&laquo; Previous','subscribe-reloaded')."</a> ";
	}
	if (($ending_to < $count_total) && ($count_results > 0)){
		$new_starting = $starting_from+25;
		echo "<a href='options-general.php?page=subscribe-to-comments-reloaded/options/index.php&amp;subscribepanel=1&amp;starting=$new_starting&amp;sre=$clean_email&amp;srt=$search_type'>".__('Next &raquo;','subscribe-reloaded')."</a> ";
	}
} ?></p>
<h3><?php _e('Search email address','subscribe-reloaded') ?></h3>
<form action="options-general.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=1" method="post">
<p><?php printf(__('You can either <a href="%s">list all the subscriptions</a> or find those where the <b>email</b>','subscribe-reloaded'), 
	'options-general.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=1&sre=@&srt=c') ?>&nbsp;
<select name="srt">
	<option value='e'><?php _e('equals','subscribe-reloaded') ?></option>
	<option value='c'><?php _e('contains','subscribe-reloaded') ?></option>
	<option value='n'><?php _e('does not contain','subscribe-reloaded') ?></option>
</select>
<input type="text" size="20" name="sre" value="" />
<input type="submit" class="subscribe-form-button" value="<?php _e('Search','subscribe-reloaded') ?>" />
</form>

<?php if (empty($clean_email) && empty($_POST['post_list']) && empty($_POST['action_type'])) return; ?>

<form action="options-general.php?page=subscribe-to-comments-reloaded/options/index.php<?php if(!empty($current_panel)) echo '&subscribepanel='.$current_panel; ?>" method="post" id="post_list_form"
	onsubmit="return confirm('<?php _e('Please remember: this operation cannot be undone. Are you sure you want to proceed?', 'subscribe-reloaded') ?>')">
<fieldset style="border:0">
<?php
	
	if (is_array($subscriptions) && !empty($subscriptions)){
		echo '<p>'.__('Subscriptions for:','subscribe-reloaded')." <b>$clean_email</b> &raquo; ".__('Rows:','subscribe-reloaded').' '.($starting_from+1)." - $ending_to ".__('of','subscribe-reloaded')." $count_total [$order_by $order]</p>";
		echo '<p>'.__('Legend: Y: subscribed, N: suspended, C: awaiting confirmation','subscribe-reloaded').'</p>';
		echo '<ul>';
		$order_dt = "<a style='text-decoration:none' title='".__('Reverse the order by Date/Time','subscribe-reloaded')."' href='options-general.php?page=subscribe-to-comments-reloaded/options/index.php&amp;subscribepanel=1&amp;sre=$clean_email&amp;srt=$search_type&amp;srob=dt&amp;sro=".(($order=='ASC')?"DESC'>&or;":"ASC'>&and;")."</a>";
		$order_status = "<a style='text-decoration:none' title='".__('Reverse the order by Date/Time','subscribe-reloaded')."' href='options-general.php?page=subscribe-to-comments-reloaded/options/index.php&amp;subscribepanel=1&amp;sre=$clean_email&amp;srt=$search_type&amp;srob=status&amp;sro=".(($order=='ASC')?"DESC'>&or;":"ASC'>&and;")."</a>";
		$order_post_id = "<a style='text-decoration:none' title='".__('Reverse the order by Post ID','subscribe-reloaded')."' href='options-general.php?page=subscribe-to-comments-reloaded/options/index.php&amp;subscribepanel=1&amp;sre=$clean_email&amp;srt=$search_type&amp;srob=post_ID&amp;sro=".(($order=='ASC')?"DESC'>&or;":"ASC'>&and;")."</a>";
		$show_email_column = (($search_type == 'c') || ($search_type == 'n'))?"<span class='subscribe-column subscribe-column-2'>".__('Email','subscribe-reloaded')."</span>":'';
		$first_column_width = empty($show_email_column)?' style="width:50%"':'';
		echo "<li class='subscribe-list-header'><input class='checkbox' type='checkbox' name='subscription_list_select_all' id='stcr_select_all' onchange='t=document.getElementById(\"post_list_form\").elements;for(i in t)if(i>0){if(t[i].checked==1){t[i].checked=0} else{t[i].checked=1}}'/> <span$first_column_width class='subscribe-column subscribe-column-1'>".__('Post (ID)','subscribe-reloaded')." &nbsp;&nbsp;$order_post_id</span> $show_email_column <span class='subscribe-column subscribe-column-3'>".__('Date and Time','subscribe-reloaded')." &nbsp;&nbsp;$order_dt</span> <span class='subscribe-column subscribe-column-4'>".__('Status','subscribe-reloaded')." &nbsp;&nbsp;$order_status</span></li>\n";
		foreach($subscriptions as $a_subscription){
			$title = get_the_title($a_subscription->post_ID);
			$specific_email = (($search_type == 'c') || ($search_type == 'n'))?"<a href='options-general.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=1&sre=$a_subscription->email&srt=e'>$a_subscription->email</a> ":'';
			$alternate = empty($alternate)?' class="alternate"':'';
			echo "<li$alternate><input class='checkbox' type='checkbox' name='subscription_list[]' value='$a_subscription->post_ID,$a_subscription->email' id='sub_{$a_subscription->dt}'/> <label$first_column_width class='subscribe-column subscribe-column-1' for='sub_{$a_subscription->dt}'><a href='$manager_link$a_subscription->post_ID'>$title</a> ($a_subscription->post_ID)</label> <span class='subscribe-column subscribe-column-2'>$specific_email</span> <span class='subscribe-column subscribe-column-3'>$a_subscription->dt</span> <span class='subscribe-column subscribe-column-4'>$a_subscription->status</span></li>\n";
		}
		echo '</ul>';
		echo '<p>'.__('Action:','subscribe-reloaded').' <input type="radio" name="action_type" value="d" id="action_type_delete" /> <label for="action_type_delete">'.__('Delete forever','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp; ';
		echo '<input type="radio" name="action_type" value="s" id="action_type_suspend" checked="checked" /> <label for="action_type_suspend">'.__('Suspend','subscribe-reloaded').'</label> &nbsp;&nbsp;&nbsp;&nbsp; ';
		echo '<input type="radio" name="action_type" value="a" id="action_type_activate" /> <label for="action_type_activate">'.__('Resume','subscribe-reloaded').'</label></p>';
		echo '<p><input type="submit" class="subscribe-form-button" value="'.__('Update subscriptions','subscribe-reloaded').'" /></p>';
		if (!empty($clean_email)) echo "<input type='hidden' name='sre' value='$clean_email'/>";
		if (!empty($search_type)) echo "<input type='hidden' name='srt' value='$search_type'/>";
		if (!empty($starting_from)) echo "<input type='hidden' name='starting' value='$starting_from'/>";
		if (!empty($order)) echo "<input type='hidden' name='sro' value='$order'/>";
		if (!empty($order_by)) echo "<input type='hidden' name='srob' value='$order_by'/>";
	}
	else{
		echo '<p>'.__('Sorry, no subscriptions found for','subscribe-reloaded')." <b>$clean_email</b></p>";
	}
?>
</fieldset>
</form>
</div>