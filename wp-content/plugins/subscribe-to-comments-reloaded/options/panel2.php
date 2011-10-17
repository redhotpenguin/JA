<?php
// Avoid direct access to this piece of code
if (strpos($_SERVER['SCRIPT_FILENAME'], basename(__FILE__))){
	header('Location: /');
	exit;
}
if (isset($_GET['ot']) && $_GET['ot']=='yes'){
	$wpdb->query("OPTIMIZE TABLE `$wp_subscribe_reloaded->table_subscriptions`");
	echo '<div class="updated fade"><p>';
	_e('StCR table has been successfully optimized.','subscribe-reloaded');		
	echo '</p></div>';
}

$subscribe_plugin_url = is_ssl()?str_replace('http://', 'https://', WP_PLUGIN_URL):WP_PLUGIN_URL;
$sql = "SELECT DATE_FORMAT(`dt`,'%e') s_day, COUNT(*) s_count
	FROM $wp_subscribe_reloaded->table_subscriptions
	WHERE `dt` > '".date_i18n('Y').'-'.date_i18n('m')."-01'
	GROUP BY s_day ASC";

$subscribe_result_set = $wpdb->get_results($sql, ARRAY_A);
$subscribe_xml = "<graph canvasBorderThickness='0' yaxisminvalue='1' canvasBorderColor='ffffff' decimalPrecision='0' divLineAlpha='20' formatNumberScale='0' lineThickness='2' showNames='1' showShadow='0' showValues='0' yAxisName='".__('Subscriptions','subscribe-reloaded')."'><categories><category name='1'/><category name='2'/><category name='3'/><category name='4'/><category name='5'/><category name='6'/><category name='7'/><category name='8'/><category name='9'/><category name='10'/><category name='11'/><category name='12'/><category name='13'/><category name='14'/><category name='15'/><category name='16'/><category name='17'/><category name='18'/><category name='19'/><category name='20'/><category name='21'/><category name='22'/><category name='23'/><category name='24'/><category name='25'/><category name='26'/><category name='27'/><category name='28'/><category name='29'/><category name='30'/><category name='31'/></categories><dataset seriesname='".__('Subscriptions for','subscribe-reloaded').' '.date_i18n('F').' '.date_i18n('Y')."' color='00aaff' showValue='1'>";
$subscribe_reorganized_set = array();
foreach($subscribe_result_set as $a_day){
	$subscribe_reorganized_set[$a_day['s_day']] = $a_day['s_count'];
}
for($i=1;$i<=31;$i++)
	if (isset($subscribe_reorganized_set[$i])){
		$subscribe_xml .= "<set value='{$subscribe_reorganized_set[$i]}'/>";
	}
	else{
		$subscribe_xml .= "<set/>";
	}
$subscribe_xml .= '</dataset></graph>';
$details_subscribe_reloaded = $wpdb->get_results("SHOW TABLE STATUS LIKE '$wp_subscribe_reloaded->table_subscriptions'", ARRAY_A);
?>
<!-- p><?php _e('Filter by date','subscribe-reloaded') ?>:
<select name="month" style="width:6em">
				<option value=""><?php _e('Month','wp-slimstat-view') ?></option>
				<option>01</option><option>02</option><option>03</option><option>04</option><option>05</option>
				<option>06</option><option>07</option><option>08</option><option>09</option><option>10</option>
				<option>11</option><option>12</option>
			</select>
			<select name="year" style="width:6em">
				<option value=""><?php _e('Year','wp-slimstat-view') ?></option>
				<?php
					$current_year = date_i18n('Y'); 
					for($i=$current_year;$i>$current_year-3;$i--)
						echo "<option>$i</option>";
				?>
			</select>
</p -->
<div class="postbox wide">
	<h3><?php _e( 'Subscriptions by day', 'subscribe-reloaded' ) ?></h3>
	<?php 
	if (empty($subscribe_xml)){ ?>
		<p class="nodata"><?php _e('No data to display','subscribe-reloaded') ?></p>
	<?php } else { ?>
	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase=https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="775" height="175">
        <param name="movie" value="<?php echo $subscribe_plugin_url ?>/subscribe-to-comments-reloaded/options/swf/fcf.swf" />
        <param name="FlashVars" value="&dataXML=<?php echo $subscribe_xml ?>&chartWidth=765&chartHeight=170">
        <param name="quality" value="high" />
        <embed src="<?php echo $subscribe_plugin_url ?>/subscribe-to-comments-reloaded/options/swf/fcf.swf" flashVars="&dataXML=<?php echo $subscribe_xml ?>&chartWidth=775&chartHeight=175" quality="high" width="765" height="170" name="line" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
	<?php } ?>
</div>

<div class="postbox small">
	<span class="note"><?php if (!empty($details_subscribe_reloaded[0]['Data_free'])) echo '[<a href="options-general.php?page=subscribe-to-comments-reloaded/options/index.php&subscribepanel=6&ot=yes">'.__('optimize','subscribe-reloaded').'</a>]'; ?></span>
	<h3><?php _e( 'Database', 'subscribe-reloaded' ) ?></h3>
	<?php 
	if (empty($subscribe_xml)){ ?>
		<p class="nodata"><?php _e('No data to display','subscribe-reloaded') ?></p>
	<?php } else { 
		if (count($details_subscribe_reloaded) == 1) {
			$overhead_suffix = 'bytes';
			if ($details_subscribe_reloaded[0]['Data_free'] > 1024){
				$details_subscribe_reloaded[0]['Data_free'] = intval($details_subscribe_reloaded[0]['Data_free']/1024);
				$overhead_suffix = 'KB';
			}
			if ($details_subscribe_reloaded[0]['Data_free'] > 1024){
				$details_subscribe_reloaded[0]['Data_free'] = intval($details_subscribe_reloaded[0]['Data_free']/1024);
				$overhead_suffix = 'MB';
			}
			echo '<p class="boxline">'.__('Engine','subscribe-reloaded')." <span class='right'>{$details_subscribe_reloaded[0]['Engine']}</span></p>";
			echo '<p class="boxline">'.__('Created on','subscribe-reloaded')." <span class='right'>{$details_subscribe_reloaded[0]['Create_time']}</span></p>";
			echo '<p class="boxline">'.__('Index length','subscribe-reloaded')." <span class='right'>{$details_subscribe_reloaded[0]['Index_length']}</span></p>";
			echo '<p class="boxline">'.__('Records','subscribe-reloaded')." <span class='right'>{$details_subscribe_reloaded[0]['Rows']}</span></p>";
			echo '<p class="boxline">'.__('Average Record Length','subscribe-reloaded')." <span class='right'>{$details_subscribe_reloaded[0]['Avg_row_length']} bytes</span></p>";
			echo '<p>'.__('Approximate Overhead','subscribe-reloaded')." <span class='right'>{$details_subscribe_reloaded[0]['Data_free']} $overhead_suffix</span></p>";
		}
	} ?>
</div>
<p>More stats coming soon...</p>
<!--
<div class="postbox small">
	<h3><?php _e( 'Analysis for', 'subscribe-reloaded' ) ?> MM/YYYY</h3>
	<?php 
	if (empty($subscribe_xml)){ ?>
		<p class="nodata"><?php _e('No data to display','subscribe-reloaded') ?></p>
	<?php } else { ?>
	<p class="boxline">Active subscriptions</p>
	<p class="boxline">Average per post</p>
	<p class="boxline">Average per category</p>
	<p class="boxline">Total [month name]</p>
	<p class="boxline">Total [previous month name]</p>
	<p>Total [this year]</p>
	<?php } ?>
</div>
<div class="postbox small">
	<h3><?php _e( 'Most active users', 'subscribe-reloaded' ) ?></h3>
	<?php 
	if (empty($subscribe_xml)){ ?>
		<p class="nodata"><?php _e('No data to display','subscribe-reloaded') ?></p>
	<?php } else { ?>
	<p class="boxline small">email1 (link to filter)</p>
	<?php } ?>
</div>
<div class="postbox medium">
	<h3><?php _e( 'Popular posts', 'subscribe-reloaded' ) ?></h3>
	<?php 
	if (empty($subscribe_xml)){ ?>
		<p class="nodata"><?php _e('No data to display','subscribe-reloaded') ?></p>
	<?php } else { ?>
	<p class="boxline small">post title (link to management)</p>
	<?php } ?>
</div>
-->