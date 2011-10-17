<?php
/*
License:
 ==============================================================================

    Copyright 2006  Dan Kuykendall  (email : dan@kuykendall.org)

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-107  USA
*/
	function podPress_isAuthorized($needed = '##NOTSET##', $justChecking = false) {
		GLOBAL $user_level, $podPress;
		if($needed == '##NOTSET##') {
			$needed = $podPress->requiredAdminRights;
		}
		if (function_exists('current_user_can')) {
			$result = current_user_can($needed);
		} else {
			$result = $user_level > 5;
		}

		if(!$justChecking && !$result) {
			die(__('Access Denied', 'podpress'));
		}
		return $result;
	}

	//this is crap
	function podPress_getCapName($cap) {
		return ucwords(str_replace('_', ' ', $cap));
	}

	function podPress_DirectoriesPreview($context) {
		echo '	<fieldset class="options">'."\n";
		echo '		<legend class="podpress_directorypreview_legend">'.__('Directory Preview', 'podpress').'</legend> <a href="javascript:void(null);" id="podpress_showhide_directory_preview" class="podpress_showhide_element" onclick="podpress_showhide_adv(\'podpress_DirectoryPreviewDisplay\', true, this.id);">[ '.__('Show', 'podpress').' ]</a>';
		echo '		<div id="podpress_DirectoryPreviewDisplay" style="display:none;">'."\n";
		echo '		<a href="javascript:void(null);" onclick="javascript: podPressShowDirectoryPreview(\'iTunes\');"><img src="'.podPress_url().'images/directoryPreview_iTunes_logo.png" border="0" alt="iTunes preview" /></a> &nbsp;';
		/// YAHOO! Podcast is shutdown since 2007 - see, http://en.wikipedia.org/wiki/Yahoo!_Podcasts
		//echo '		<a href="javascript:void(null);" onclick="javascript: podPressShowDirectoryPreview(\'Yahoo\');"><img src="'.podPress_url().'images/directoryPreview_yahoo_logo.png" border="0" alt="Yahoo preview" /></a> &nbsp;';
		echo '		<a href="javascript:void(null);" onclick="javascript: podPressShowDirectoryPreview(\'PodcastAlley\');"><img src="'.podPress_url().'images/directoryPreview_PCA_logo.png" border="0" alt="PodcastAlley preview" /></a>';
		echo '		<a href="javascript:void(null);" onclick="javascript: podPressShowDirectoryPreview(\'PodcastReady\');"><img src="'.podPress_url().'images/directoryPreview_PodcastReady_logo.png" border="0" alt="PodcastAlley preview" /></a>';
		echo '		<a href="javascript:void(null);" onclick="javascript: podPressShowDirectoryPreview(\'Blubrry\');"><img src="'.podPress_url().'images/directoryPreview_Blubrry_logo.jpg" border="0" alt="Blubrry preview" /></a>';
		echo '		'."\n";
		echo '		<div id="iTunesPreviewDisplay" style="border: 0;">'."\n";
		echo '			<div style="width: 100%; text-align: center;"><a href="http://phobos.apple.com/WebObjects/MZStore.woa/wa/viewGenre?id=26" target="_new">Visit this directory</a> / <a href="https://phobos.apple.com/WebObjects/MZFinance.woa/wa/publishPodcast" target="_new">Submit to this directory</a></div>'."\n";
		echo '			<table width="100%" cellspacing="2" cellpadding="5" class="editform" style="background-color:#8CA9CB;">'."\n";
		echo '				<tr valign="top">'."\n";
		echo '					<td colspan="3">'."\n";
		echo '						<b><font size="+1"><span id="iTunesBlognamePreview"></span></font></b><br/>'."\n";
		echo '					</td>'."\n";
		echo '				</tr>'."\n";
		echo '				<tr valign="top">'."\n";
		echo '					<td width="1%">'."\n";
		echo '						<img id="iTunesPreviewImage" width="175" height="175" alt="Podcast Image" src=""/>'."\n";
		echo '					</td>'."\n";
		echo '					<td align="left">'."\n";
		echo '						<span id="itunesAuthorPreview" style="font-weight: bold;"></span><br/><br/>'."\n";
		echo '						<span style="font-size: small;">'."\n";
		echo '							Category: <span id="itunesCategoryPreview"></span><br/>'."\n";
		echo '							Language: <span id="iTunesRssLanguagePreview">English</span><br/>'."\n";
		echo '							Total: n episodes<br/><br/>'."\n";
		echo '							<img src="'.podPress_url().'images/directoryPreview_iTunes_subscribebutton.png" border="0" alt="iTunesSubscriptions" /> '."\n";
		echo '						</span>'."\n";
		echo '					</td>'."\n";
		echo '					<td rowspan="3" width="1%">'."\n";
		echo '						<img src="'.podPress_url().'images/directoryPreview_iTunes_seeallpodcasts.png" border="0" alt="iTunesSeeAllPodcasts" /> '."\n";
		echo '						<br/><br/>'."\n";
		echo '						<img src="'.podPress_url().'images/directoryPreview_iTunes_alsosubcribedto.png" border="0" alt="iTunesAlsoSubscribeTo" /> '."\n";
		echo '					</td>'."\n";
		echo '				</tr>'."\n";
		echo '				<tr valign="top">'."\n";
		echo '					<td colspan="2">'."\n";
		echo '						<b><font size="+1" color="#163B6A">Podcast Description</font></b><br/><br/>'."\n";
		echo '						<span id="itunesDescriptionPreview" style="font-size: small;"></span>'."\n";
		echo '					</td>'."\n";
		echo '				</tr>'."\n";
		echo '				<tr valign="top">'."\n";
		echo '					<td colspan="2" bgcolor="#8DAACC">'."\n";
		echo '						<table width="100%" bgcolor="#8DAACC">'."\n";
		echo '							<tr>'."\n";
		echo '								<td align="left">'."\n";
		echo '									<img src="'.podPress_url().'images/directoryPreview_iTunes_l_reviews.png" border="0" alt="iTunesReviews" /> '."\n";
		echo '								</td>'."\n";
		echo '								<td align="right">'."\n";
		echo '									<img src="'.podPress_url().'images/directoryPreview_iTunes_r_reviews.png" border="0" alt="iTunesReviews" /> '."\n";
		echo '								</td>'."\n";
		echo '							</tr>'."\n";
		echo '						</table>'."\n";
		echo '					</td>'."\n";
		echo '				</tr>'."\n";
		echo '			</table>'."\n";
		echo '		</div>'."\n";

		echo '		<div id="YahooPreviewDisplay" style="display: none;">'."\n";
		echo '			<div style="width: 100%; text-align: center;"><a href="http://podcasts.yahoo.com/" target="_new">Visit this directory</a> / <a href="http://podcasts.yahoo.com/publish" target="_new">Submit to this directory</a></div>'."\n";
		echo '			<div style="width: 100%; border: 1px solid grey;">'."\n";
		echo '				<table width="100%" border="0" cellspacing="0" cellpadding="0">'."\n";
		echo '					<tr valign="top">'."\n";
		echo '						<td colspan="3">'."\n";
		echo '							<b><font size="+1" color="#669900">Series Information:	<span id="YahooBlognamePreview"></span></font></b><br/><br/>'."\n";
		echo '						</td>'."\n";
		echo '					</tr>'."\n";
		echo '					<tr valign="top">'."\n";
		echo '						<td width="1%" rowspan="2">'."\n";
		echo '							<img id="YahooPreviewImage" width="175" height="175" alt="Podcast Image" src=""/><br/><br/>'."\n";
		echo '							<img src="'.podPress_url().'images/directoryPreview_yahoo_buttons.png" border="0" alt="YahooButtons" /> '."\n";
		echo '						</td>'."\n";
		echo '						<td colspan="2">'."\n";
		echo '							<span id="YahooDescriptionPreview" style="font-size: small;"></span>'."\n";
		echo '						</td>'."\n";
		echo '					</tr>'."\n";
		echo '					<tr valign="top">'."\n";
		echo '						<td align="left">'."\n";
		echo '							<font size="+1" color="#669900">Details</font><br/>'."\n";
		echo '							Episodes: N<br/>'."\n";
		echo '							Web Site: <font color="#2597D6">'.get_option('siteurl').'</font><br/>'."\n";
		echo '							RSS: <span id="YahooFeedURLPreview" style="color: #2597D6;"></span><br/>'."\n";
		echo '							Author: <span id="YahooAuthorPreview"></span><br/>'."\n";
		echo '							Language: <span id="YahooRssLanguagePreview">English</span><br/>'."\n";
		echo '							Copyright: --<br/>'."\n";
		echo '							Published: whatever date<br/>'."\n";
		echo '						</td>'."\n";
		echo '						<td align="left">'."\n";
		echo '							<font size="+1" color="#669900">Ratings</font><br/>'."\n";
		echo '							Be the first to rate!<br/><br/>'."\n";
		echo '						<b>Rate it:</b><br/>'."\n";
		echo '						<img src="'.podPress_url().'images/directoryPreview_yahoo_stars.png" border="0" alt="YahooStars" /> '."\n";
		echo '						<br/><font color="#2597D6">Write a Review</font><br/>'."\n";
		echo '						</td>'."\n";
		echo '					</tr>'."\n";
		echo '				</table>'."\n";
		echo '			</div>'."\n";
		echo '		</div>'."\n";

		echo '		<div id="PodcastAlleyPreviewDisplay" style="display: none; border: 0;">'."\n";
		echo '			<div style="width: 100%; text-align: center;"><a href="http://www.podcastalley.com/" target="_new">Visit this directory</a> / <a href="http://www.podcastalley.com/add_a_podcast.php" target="_new">Submit to this directory</a></div>'."\n";
		echo '			<table width="100%" border="0" cellspacing="0" cellpadding="0"  style="background-color:#FFFFFF;">'."\n";
		echo '				<tr valign="top">'."\n";
		echo '					<td width="5%">'."\n";
		echo '						<img src="'.podPress_url().'images/directoryPreview_PCA_left.png" border="0" alt="PCA Left" /> '."\n";
		echo '					</td>'."\n";
		echo '					<td width="90%">'."\n";
		echo '						<table width="100%" border="0" cellspacing="0" cellpadding="0"  style="background-color:#F2F2F2;">'."\n";
		echo '							<tr valign="top">'."\n";
		echo '								<td class="podPress_statscell" valign="top" width="140">'."\n";
		echo '									<table class="podPress_statstable" width="100%" border="0" cellspacing="0" cellpadding="0">'."\n";
		echo '										<tr>'."\n";
		echo '											<td class="podPress_statshead">Podcast Stats</td>'."\n";
		echo '										</tr>'."\n";
		echo '										<tr>'."\n";
		echo '											<td class="podPress_linkscell">'."\n";
		echo '												<p><span class="podPress_captionstats">GENRE: </span> <span id="PCACategoryPreview"></span><br/>'."\n";
		echo '												<span class="podPress_captionstats">COMMENTS:</span> 0<br/>'."\n";
		echo '												<span class="podPress_captionstats">MONTHLY VOTES:</span> 0<br/>'."\n";
		echo '												<span class="podPress_captionstats">OVERALL VOTES:</span> 0<br/>'."\n";
		echo '												<span class="podPress_captionstats">MEMBER: </span>1/2000<br/>'."\n";
		echo '												<span class="podPress_captionstats">MONTHLY RANK:</span><br/>Not Yet Ranked<br/>'."\n";
		echo '												<br/><img src="'.podPress_url().'images/directoryPreview_PCA_buttons.png" border="0" alt="PCAButtons" /> '."\n";
		echo '											</td>'."\n";
		echo '										</tr>'."\n";
		echo '									</table>'."\n";
		echo '								</td>'."\n";
		echo '								<td class="podPress_desccell" valign="top">'."\n";
		echo '									<font size="3" color="#666666"><b><span id="PCABlognamePreview"></span></b></font><br/>'."\n";
		echo '									<span style="color: #666666;" id="PCADescriptionPreview"></span>'."\n";
		echo '								</td>'."\n";
		echo '							</tr>'."\n";
		echo '						</table>'."\n";
		echo '					</td>'."\n";
		echo '					<td width="5%">'."\n";
		echo '						<img src="'.podPress_url().'images/directoryPreview_PCA_right.png" border="0" alt="PCA Right" /> '."\n";
		echo '					</td>'."\n";
		echo '				</tr>'."\n";
		echo '			</table>'."\n";
		echo '		</div>'."\n";

		echo '		<div id="PodcastReadyPreviewDisplay" style="display: none;">'."\n";
		echo '			<div style="width: 100%; text-align: center;"><a href="http://www.podcastready.com/" target="_new">Visit this directory</a> / <a href="http://www.podcastready.com/channel.php?action=subscribe&feed" target="_new" id="PodcastReadyFeedURLPreview">Submit to this directory</a></div>'."\n";
		echo '			<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#CDE4A9">'."\n";
		echo '				<tr valign="top">'."\n";
		echo '					<td width="200" align="center">'."\n";
		echo '						<br/><img id="PodcastReadyPreviewImage" width="175" height="175" alt="Podcast Image" src=""/><br/>'."\n";
		echo '						<img src="'.podPress_url().'images/directoryPreview_PodcastReady_buttons.png" border="0" alt="PodcastReadyButtons" /><br/>'."\n";
		echo '					</td>'."\n";
		echo '					<td>'."\n";
		echo '						<br/><b><font size="+1" color="#466B90">Detail for	<span id="PodcastReadyBlognamePreview"></span></font></b><br/><br/>'."\n";
		echo '						<span id="PodcastReadyDescriptionPreview" style="font-size: small;"></span><br/>'."\n";
		echo '						<b>Filed Under:</b> <span id="PodcastReadyCategoryPreview" style="color: #466B90;"></span><br/>'."\n";
		echo '						<b>Media Type:</b> MP3<br/><br/>'."\n";
		echo '						<img src="'.podPress_url().'images/directoryPreview_PodcastReady_listeners.png" border="0" alt="PodcastReadyOtherPeople" /> '."\n";
		echo '					</td>'."\n";
		echo '				</tr>'."\n";
		echo '			</table>'."\n";
		echo '		</div>'."\n";

		echo '	<div id="BlubrryPreviewDisplay" style="display: none; border: 0;">'."\n";
		echo '		<div style="width: 100%; text-align: center; "><a href="http://www.blubrry.com" target="_new">Visit this community</a> / <a href="http://www.blubrry.com/accountsettings.php?page=addpodcast" target="_new">Join this community</a></div>'."\n";
		echo '		<table width="100%" style="background-color: #F2CCFF; color: #003366; font-size:14px; font-weight:bold;" border="0" cellpadding="2" cellspacing="0">'."\n";
		echo '			<tr>'."\n";
		echo '				<td style="font-size:16px; font-weight:bold;" id="BlubrryBlognamePreview">TITLE OF PODCAST HERE</td>'."\n";
		echo '				<td style="text-decoration:underline; text-align:right; font-weight:bold;">Visit Podcast Home Page</td>'."\n";
		echo '			</tr>'."\n";
		echo '		</table>'."\n";
		echo '		<table width="100%" style="background-color: #D2E9FF; color: #003366; font-size:12px;" border="0" cellpadding="2" cellspacing="0">'."\n";
		echo '			<tr>'."\n";
		echo '				<td valign="top" style="width:230px;">'."\n";
		echo '					<table width="220" border="0" cellspacing="0" cellpadding="0" style="padding-left: 6px; padding-top: 6px;">'."\n";
		echo '						<tr>'."\n";
		echo '							<td width="100" height="100" align="left" valign="top"><img id="BlubrryPreviewImage" width="90" height="90" alt="Podcast Image" src=""/></td>'."\n";
		echo '							<td width="120" valign="top"><div id="BlubrryAuthorPreview" style="font-weight: bold;">AUTHOR NAME</div>'."\n";
		echo '								<img src="'.podPress_url().'images/directoryPreview_Blubrry_buttons.png" width="64" height="52" /></td>'."\n";
		echo '						</tr>'."\n";
		echo '						<tr>'."\n";
		echo '							<td height="28" colspan="2" valign="top" style="font-size: 14px; font-weight: bold; text-decoration:underline;">Visit Podcast Home Page</td>'."\n";
		echo '						</tr>'."\n";
		echo '						<tr>'."\n";
		echo '							<td height="36" colspan="2" valign="top" style="font-size: 16px; font-weight: bold; color: black;">Current Episodes</td>'."\n";
		echo '						</tr>'."\n";
		echo '						<tr>'."\n";
		echo '							<td colspan="2"><div style="color:#9933D5; font-weight: bold; font-size: 14px;">Latest Podcast Post Title</div>'."\n";
		echo '							<div style="font-size: 12px;">The content of your post appears here.  Wow your stuff looks good.  Hey what if someone wants to listen, they can click on the button below.</div>'."\n";
		echo '							<img src="'.podPress_url().'images/directoryPreview_Blubrry_buttons_episode.png" />'."\n";
		echo '							</td>'."\n";
		echo '						</tr>'."\n";
		echo '					</table>'."\n";
		echo '				</td>'."\n";
		echo '				<td valign="top" style="padding-left: 10px; padding-top: 10px;">'."\n";
		echo '					<div style="font-size: 18px; font-weight: bold; padding-bottom: 10px;">Description</div>'."\n";
		echo '					<div style="font-size: 12px; padding-bottom: 5px;" id="BlubrryDescriptionPreview">DESCRIPTION OF PODCAST</div>'."\n";
		echo '					<div style="text-align:right; padding-bottom: 10px;"><img src="'.podPress_url().'images/directoryPreview_Blubrry_syndication.png" /></div>'."\n";
		echo '					<div style="font-size: 18px; font-weight: bold; padding-bottom: 10px;">Tags</div>'."\n";
		echo '					<div style="padding-bottom: 10px;">mightyseek podpress awesome wordpress plugin</div>'."\n";
		echo '					<div style="font-size: 18px; font-weight: bold; padding-bottom: 10px;">Friends</div>'."\n";
		echo '					<div style="background-image:url('.podPress_url().'images/directoryPreview_Blubrry_friends.jpg); background-repeat:no-repeat; background-position: top left; height:113px;">&nbsp;</div>'."\n";
		echo '				</td>'."\n";
		echo '			</tr>'."\n";
		echo '		</table>'."\n";
		echo '	</div>'."\n";
		echo '	</div><!-- End: podpress_DirectoryPreviewDisplay -->'."\n";

		echo '</fieldset>'."\n";
	}

	/* Utility Functions */
	function podPress_getCapList($roles = true, $kill_levels = true){
		global $wp_roles;

		// Get Role List
		foreach($wp_roles->role_objects as $key => $role) {
			foreach($role->capabilities as $cap => $grant) {
				$capnames[$cap] = $cap;
			}
		}

		if ($caplist = get_option('caplist')) {
			$capnames = array_unique(array_merge($caplist, $capnames));
		}

		$capnames = apply_filters('capabilities_list', $capnames);
		if(!is_array($capnames)) $capnames = array();
		$capnames = array_unique($capnames);
		sort($capnames);

		//Filter out the level_x caps, they're obsolete
		if($kill_levels) {
			$capnames = array_diff($capnames, array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10'));
		}

		//Filter out roles if required
		if (!$roles) {
			foreach ($wp_roles->get_names() as $role) {
				$key = array_search($role, $capnames);
				if ($key !== false && $key !== null) { //array_search() returns null if not found in 4.1
					unset($capnames[$key]);
				}
			}
		}

		return $capnames;
	}

	function podPress_maybe_create_table($table_name, $create_ddl) {
		GLOBAL $wpdb;
		foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
			if ($table == $table_name) {
				return true;
			}
		}
		//didn't find it try to create it.
		$q = $wpdb->query($create_ddl);
		// we cannot directly tell that whether this succeeded!
		foreach ($wpdb->get_col("SHOW TABLES",0) as $table ) {
			if ($table == $table_name) {
				return true;
			}
		}
		return false;
	}

	function podPress_maybe_add_column($table_name, $column_name, $create_ddl) {
		GLOBAL $wpdb, $debug;
		foreach ( $wpdb->get_col("DESC $table_name", 0) as $column ) {
			if ( $debug ) { printf(__('checking %1$s == %2$s<br />', 'podpress'), $column, $column_name); }
			if ( $column == $column_name ) {
				return TRUE;
			}
		}
		// didn't find it try to create it.
		$q = $wpdb->query($create_ddl);
		// we cannot directly tell that whether this succeeded!
		foreach ( $wpdb->get_col("DESC $table_name", 0) as $column ) {
			if ( $column == $column_name ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	function podPress_filetypes() {
		$options['audio_mp3'] = __('MP3 - Standard Audio (iPod Compliant)', 'podpress');
		$options['audio_m4a'] = __('M4A - Enhanced Audio (iPod Compliant)', 'podpress');
		$options['video_m4v'] = __('M4V - Video (iPod Compliant)', 'podpress');
		$options['audio_mp4'] = __('MP4 - Enhanced Audio (iPod Compliant)', 'podpress');
		$options['video_mp4'] = __('MP4 - Video (iPod Compliant)', 'podpress');
		$options['audio_ogg'] = __('OGG - Ogg Vorbis Audio', 'podpress');
		$options['video_ogv'] = __('OGV - Ogg Theora video', 'podpress');
		$options['audio_aa'] = __('AA - Audible Encoded (iPod Compliant)', 'podpress');
		$options['audio_m3u'] = __('M3U - Streaming Audio', 'podpress');
		$options['video_mov'] = __('MOV - Video (iPod Compliant)', 'podpress');
		$options['video_qt'] = __('QT - Video (iPod Compliant)', 'podpress');
		$options['video_avi'] = __('AVI - Video', 'podpress');
		$options['video_mpg'] = __('MPG - Video', 'podpress');
		$options['video_asf'] = __('ASF - Video', 'podpress');
		$options['video_wmv'] = __('WMV - Video', 'podpress');
		$options['audio_wma'] = __('WMA - Audio', 'podpress');
		$options['video_flv'] = __('FLV - Flash Video', 'podpress');
		$options['video_swf'] = __('SWF - Flash content', 'podpress');
		$options['ebook_pdf'] = __('PDF - eBook', 'podpress');
		$options['misc_torrent'] = __('Torrent - P2P', 'podpress');
		return $options;
	}

	function podPress_mediaOptions() {
		$options = array();
		reset($options);
		$options = podPress_filetypes();
		$options['embed_youtube'] = __('YouTube - Video', 'podpress');
		$options['misc_other'] = __('??? - Other', 'podpress');
		foreach ($options as $key => $value) {
			echo '<option value="'.$key.'">'.$value.'</option>'."\n";
		}
	}


	function podPress_videoDimensionOptions($selected='320:240') {
		$dimensions = array();
		reset($dimensions);
		$dimensions_noimage = array();
		reset($dimensions_noimage);

		$dimensions['160:120'] = '160 x 120';
		$dimensions['320:240'] = '320 x 240';
		$dimensions['480:320'] = '480 x 320';
		$dimensions['640:480'] = '640 x 480';
		$dimensions['720:540'] = '720 x 540';
		$dimensions['450:252'] = '450 x 252 [16:9 - 1.78:1]';
		$dimensions['480:260'] = '480 x 260 [16:9 - 1.85:1]';
		$dimensions['720:405'] = '720 x 405 [16:9 - 1.78:1]';
		$dimensions['720:390'] = '720 x 390 [16:9 - 1.85:1]';
		
		echo '<optgroup label="'.__('Common Dimensions', 'podpress').'">'."\n";
		foreach ($dimensions as $key => $value) {
			if ($key == $selected) {
				$selected_str =' selected="selected"';
			} else {
				$selected_str ='';
			}
			echo '<option value="'.$key.'"'.$selected_str.'>'.$value.'</option>'."\n";
		}
		echo '</optgroup>'."\n";
		$dimensions_noimage['160:0'] = __('width: 160 px', 'podpress');
		$dimensions_noimage['320:0'] = __('width: 320 px', 'podpress');
		$dimensions_noimage['480:0'] = __('width: 480 px', 'podpress');
		$dimensions_noimage['640:0'] = __('width: 640 px', 'podpress');
		$dimensions_noimage['720:0'] = __('width: 720 px', 'podpress');
		echo '<optgroup label="'.__('player without preview image', 'podpress').'">'."\n";
		foreach ($dimensions_noimage as $key => $value) {
			if ($key == $selected) {
				$selected_str =' selected="selected"';
			} else {
				$selected_str ='';
			}
			echo '<option value="'.$key.'"'.$selected_str.'>'.$value.'</option>'."\n";
		}
		echo '</optgroup>'."\n";

	}

	function podPress_itunesLanguageArray() {
		$langs = array();
		$langs['af'] = 'Afrikaans';
		$langs['sq'] = 'Albanian';
		$langs['eu'] = 'Basque';
		$langs['be'] = 'Belarusian';
		$langs['bg'] = 'Bulgarian';
		$langs['ca'] = 'Catalan';
		$langs['zh-cn'] = 'Chinese (Simplified)';
		$langs['zh-tw'] = 'Chinese (Traditional)';
		$langs['hr'] = 'Croatian';
		$langs['cs'] = 'Czech';
		$langs['da'] = 'Danish';
		$langs['nl'] = 'Dutch';
		$langs['nl-be'] = 'Dutch (Belgium)';
		$langs['nl-nl'] = 'Dutch (Netherlands)';
		$langs['en'] = 'English';
		$langs['en-au'] = 'English (Australia)';
		$langs['en-bz'] = 'English (Belize)';
		$langs['en-ca'] = 'English (Canada)';
		$langs['en-ie'] = 'English (Ireland)';
		$langs['en-jm'] = 'English (Jamaica)';
		$langs['en-nz'] = 'English (New Zealand)';
		$langs['en-ph'] = 'English (Phillipines)';
		$langs['en-za'] = 'English (South Africa)';
		$langs['en-tt'] = 'English (Trinidad)';
		$langs['en-gb'] = 'English (United Kingdom)';
		$langs['en-us'] = 'English (United States)';
		$langs['en-zw'] = 'English (Zimbabwe)';
		$langs['et'] = 'Estonian';
		$langs['fo'] = 'Faeroese';
		$langs['fi'] = 'Finnish';
		$langs['fr'] = 'French';
		$langs['fr-be'] = 'French (Belgium)';
		$langs['fr-ca'] = 'French (Canada)';
		$langs['fr-fr'] = 'French (France)';
		$langs['fr-lu'] = 'French (Luxembourg)';
		$langs['fr-mc'] = 'French (Monaco)';
		$langs['fr-ch'] = 'French (Switzerland)';
		$langs['gl'] = 'Galician';
		$langs['gd'] = 'Gaelic';
		$langs['de'] = 'German';
		$langs['de-at'] = 'German (Austria)';
		$langs['de-de'] = 'German (Germany)';
		$langs['de-li'] = 'German (Liechtenstein)';
		$langs['de-lu'] = 'German (Luxembourg)';
		$langs['de-ch'] = 'German (Switzerland)';
		$langs['el'] = 'Greek';
		$langs['haw'] = 'Hawaiian';
		$langs['hu'] = 'Hungarian';
		$langs['is'] = 'Icelandic';
		$langs['in'] = 'Indonesian';
		$langs['ga'] = 'Irish';
		$langs['it'] = 'Italian';
		$langs['it-it'] = 'Italian (Italy)';
		$langs['it-ch'] = 'Italian (Switzerland)';
		$langs['ja'] = 'Japanese';
		$langs['ko'] = 'Korean';
		$langs['mk'] = 'Macedonian';
		$langs['no'] = 'Norwegian';
		$langs['pl'] = 'Polish';
		$langs['pt'] = 'Portuguese';
		$langs['pt-br'] = 'Portuguese (Brazil)';
		$langs['pt-pt'] = 'Portuguese (Portugal)';
		$langs['ro'] = 'Romanian';
		$langs['ro-mo'] = 'Romanian (Moldova)';
		$langs['ro-ro'] = 'Romanian (Romania)';
		$langs['ru'] = 'Russian';
		$langs['ru-mo'] = 'Russian (Moldova)';
		$langs['ru-ru'] = 'Russian (Russia)';
		$langs['sr'] = 'Serbian';
		$langs['sk'] = 'Slovak';
		$langs['sl'] = 'Slovenian';
		$langs['es'] = 'Spanish';
		$langs['es-ar'] = 'Spanish (Argentina)';
		$langs['es-bo'] = 'Spanish (Bolivia)';
		$langs['es-cl'] = 'Spanish (Chile)';
		$langs['es-co'] = 'Spanish (Colombia)';
		$langs['es-cr'] = 'Spanish (Costa Rica)';
		$langs['es-do'] = 'Spanish (Dominican Republic)';
		$langs['es-ec'] = 'Spanish (Ecuador)';
		$langs['es-sv'] = 'Spanish (El Salvador)';
		$langs['es-gt'] = 'Spanish (Guatemala)';
		$langs['es-hn'] = 'Spanish (Honduras)';
		$langs['es-mx'] = 'Spanish (Mexico)';
		$langs['es-ni'] = 'Spanish (Nicaragua)';
		$langs['es-pa'] = 'Spanish (Panama)';
		$langs['es-py'] = 'Spanish (Paraguay)';
		$langs['es-pe'] = 'Spanish (Peru)';
		$langs['es-pr'] = 'Spanish (Puerto Rico)';
		$langs['es-es'] = 'Spanish (Spain)';
		$langs['es-uy'] = 'Spanish (Uruguay)';
		$langs['es-ve'] = 'Spanish (Venezuela)';
		$langs['sv'] = 'Swedish';
		$langs['sv-fi'] = 'Swedish (Finland)';
		$langs['sv-se'] = 'Swedish (Sweden)';
		$langs['tr'] = 'Turkish';
		$langs['uk'] = 'Ukranian';
		return $langs;
	}

	function podPress_itunesLanguageOptions($current = 'en-us') {
		$langs = podPress_itunesLanguageArray();
		reset($langs);
		foreach ($langs as $key => $value) {
			echo '<option value="'.$key.'"';
			if($key == $current) {
				echo ' selected="selected"';
			}
			echo '>'.$value.' ['.$key.']</option>'."\n";
		}
	}

	function podPress_itunesCategoryOptions($current = '') {
		$cats = array();
		if ('' == trim($current) AND '##Global##' != $current) {
			$current='[ '.__('nothing', 'podpress').' ]';
		}
		$cats[] = '[ '.__('nothing', 'podpress').' ]';

		$cats[] = 'Arts';
		$cats[] = 'Arts:Design';
		$cats[] = 'Arts:Fashion & Beauty';
		$cats[] = 'Arts:Food';
		$cats[] = 'Arts:Literature';
		$cats[] = 'Arts:Performing Arts';
		$cats[] = 'Arts:Visual Arts';

		$cats[] = 'Business';
		$cats[] = 'Business:Business News';
		$cats[] = 'Business:Careers';
		$cats[] = 'Business:Investing';
		$cats[] = 'Business:Management & Marketing';
		$cats[] = 'Business:Shopping';

		$cats[] = 'Comedy';

		$cats[] = 'Education';
		$cats[] = 'Education:Education Technology';
		$cats[] = 'Education:Higher Education';
		$cats[] = 'Education:K-12';
		$cats[] = 'Education:Language Courses';
		$cats[] = 'Education:Training';

		$cats[] = 'Games & Hobbies';
		$cats[] = 'Games & Hobbies:Automotive';
		$cats[] = 'Games & Hobbies:Aviation';
		$cats[] = 'Games & Hobbies:Hobbies';
		$cats[] = 'Games & Hobbies:Other Games';
		$cats[] = 'Games & Hobbies:Video Games';

		$cats[] = 'Government & Organizations';
		$cats[] = 'Government & Organizations:Local';
		$cats[] = 'Government & Organizations:National';
		$cats[] = 'Government & Organizations:Non-Profit';
		$cats[] = 'Government & Organizations:Regional';

		$cats[] = 'Health';
		$cats[] = 'Health:Alternative Health';
		$cats[] = 'Health:Fitness & Nutrition';
		$cats[] = 'Health:Self-Help';
		$cats[] = 'Health:Sexuality';

		$cats[] = 'Kids & Family';

		$cats[] = 'Music';

		$cats[] = 'News & Politics';

		$cats[] = 'Religion & Spirituality';
		$cats[] = 'Religion & Spirituality:Buddhism';
		$cats[] = 'Religion & Spirituality:Christianity';
		$cats[] = 'Religion & Spirituality:Hinduism';
		$cats[] = 'Religion & Spirituality:Islam';
		$cats[] = 'Religion & Spirituality:Judaism';
		$cats[] = 'Religion & Spirituality:Other';
		$cats[] = 'Religion & Spirituality:Spirituality';

		$cats[] = 'Science & Medicine';
		$cats[] = 'Science & Medicine:Medicine';
		$cats[] = 'Science & Medicine:Natural Sciences';
		$cats[] = 'Science & Medicine:Social Sciences';

		$cats[] = 'Society & Culture';
		$cats[] = 'Society & Culture:History';
		$cats[] = 'Society & Culture:Personal Journals';
		$cats[] = 'Society & Culture:Philosophy';
		$cats[] = 'Society & Culture:Places & Travel';

		$cats[] = 'Sports & Recreation';
		$cats[] = 'Sports & Recreation:Amateur';
		$cats[] = 'Sports & Recreation:College & High School';
		$cats[] = 'Sports & Recreation:Outdoor';
		$cats[] = 'Sports & Recreation:Professional';

		$cats[] = 'Technology';
		$cats[] = 'Technology:Gadgets';
		$cats[] = 'Technology:Tech News';
		$cats[] = 'Technology:Podcasting';
		$cats[] = 'Technology:Software How-To';

		$cats[] = 'TV & Film';

		reset($cats);

		$foundit = false;
		foreach ($cats as $value) {
			$value = str_replace('&', '&amp;', $value);
			echo '<option';
			if($value == $current) {
				$foundit = true;
				echo ' selected="selected"';
			}
			//~ if ( '[ '.__('nothing', 'podpress').' ]' === $value ) {
				//~ echo ' value="nothing"';
			//~ }
			echo ' value="'.attribute_escape($value).'"';
			
			echo '>'.$value.'</option>'."\n";
		}
		
		if(!$foundit AND '##Global##' != $current) {
			$current = podPress_upgradeCategory($current);
			echo '<option selected="selected">'.$current.'</option>'."\n";
		}
	}

	function podPress_upgradeCategory($current) {
		$cats['Arts & Entertainment'] = 'Arts';
		$cats['Arts & Entertainment:Architecture'] = 'Arts:Design';
		$cats['Arts & Entertainment:Books'] = 'Arts:Literature';
		$cats['Arts & Entertainment:Design'] = 'Arts:Design';
		$cats['Arts & Entertainment:Entertainment'] = 'TV & Film';
		$cats['Arts & Entertainment:Games'] = 'Games & Hobbies:Video Games';
		$cats['Arts & Entertainment:Performing Arts'] = 'Arts:Performing Arts';
		$cats['Arts & Entertainment:Photography'] = 'Arts:Visual Arts';
		$cats['Arts & Entertainment:Poetry'] = 'Arts:Literature';
		$cats['Arts & Entertainment:Science Fiction'] = 'Arts:Literature';

		$cats['Audio Blogs'] = 'Society & Culture:Personal Journals';

		$cats['Business'] = 'Business';
		$cats['Business:Careers'] = 'Business:Careers';
		$cats['Business:Finance'] = 'Business:Business News';
		$cats['Business:Investing'] = 'Business:Investing';
		$cats['Business:Management'] = 'Business:Management & Marketing';
		$cats['Business:Marketing'] = 'Business:Management & Marketing';

		$cats['Comedy'] = 'Comedy';

		$cats['Education'] = 'Education';
		$cats['Education:Higher Education'] = 'Education:Higher Education';
		$cats['Education:K-12'] = 'Education:K-12';

		$cats['Family'] = 'Kids & Family';

		$cats['Food'] = 'Arts:Food';

		$cats['Health'] = 'Health';
		$cats['Health:Diet & Nutrition'] = 'Health:Fitness & Nutrition';
		$cats['Health:Fitness'] = 'Health:Fitness & Nutrition';
		$cats['Health:Relationships'] = 'Health:Sexuality';
		$cats['Health:Self-Help'] = 'Health:Self-Help';
		$cats['Health:Sexuality'] = 'Health:Sexuality';

		$cats['International'] = 'Government & Organizations:National';
		$cats['International:Australian'] = 'Government & Organizations:National';
		$cats['International:Belgian'] = 'Government & Organizations:National';
		$cats['International:Brazilian'] = 'Government & Organizations:National';
		$cats['International:Canadian'] = 'Government & Organizations:National';
		$cats['International:Chinese'] = 'Government & Organizations:National';
		$cats['International:Dutch'] = 'Government & Organizations:National';
		$cats['International:French'] = 'Government & Organizations:National';
		$cats['International:German'] = 'Government & Organizations:National';
		$cats['International:Hebrew'] = 'Government & Organizations:National';
		$cats['International:Italian'] = 'Government & Organizations:National';
		$cats['International:Japanese'] = 'Government & Organizations:National';
		$cats['International:Norwegian'] = 'Government & Organizations:National';
		$cats['International:Polish'] = 'Government & Organizations:National';
		$cats['International:Portuguese'] = 'Government & Organizations:National';
		$cats['International:Spanish'] = 'Government & Organizations:National';
		$cats['International:Swedish'] = 'Government & Organizations:National';

		$cats['Movies & Television'] = 'TV & Film';

		$cats['Music'] = 'Music';

		$cats['News'] = 'News & Politics';
		$cats['Politics'] = 'News & Politics';

		$cats['Public Radio'] = 'News & Politics';

		$cats['Religion & Spirituality'] = 'Religion & Spirituality';
		$cats['Religion & Spirituality:Buddhism'] = 'Religion & Spirituality:Buddhism';
		$cats['Religion & Spirituality:Christianity'] = 'Religion & Spirituality:Christianity';
		$cats['Religion & Spirituality:Islam'] = 'Religion & Spirituality:Islam';
		$cats['Religion & Spirituality:Judaism'] = 'Religion & Spirituality:Judaism';
		$cats['Religion & Spirituality:New Age'] = 'Religion & Spirituality:Spirituality';
		$cats['Religion & Spirituality:Philosophy'] = 'Religion & Spirituality:Philosophy';
		$cats['Religion & Spirituality:Spirituality'] = 'Religion & Spirituality:Spirituality';

		$cats['Science'] = 'Science & Medicine';

		$cats['Sports'] = 'Sports & Recreation';

		$cats['Talk Radio'] = 'News & Politics';

		$cats['Technology'] = 'Technology';
		$cats['Technology:IT News'] = 'Technology:Tech News';
		$cats['Technology:Computers'] = 'Technology:Tech News';
		$cats['Technology:Developers'] = 'Technology:Tech News';
		$cats['Technology:Gadgets'] = 'Technology:Gadgets';
		$cats['Technology:Information Technology'] = 'Technology:Tech News';
		$cats['Technology:News'] = 'Technology:Tech News';
		$cats['Technology:Operating Systems'] = 'Technology:Tech News';
		$cats['Technology:Podcasting'] = 'Technology:Podcasting';
		$cats['Technology:Smart Phones'] = 'Technology:Tech News';
		$cats['Technology:Text/Speech'] = 'Technology:Tech News';


		$cats['Transportation'] = 'Games & Hobbies:Automotive';
		$cats['Transportation:BBB'] = 'Games & Hobbies:Automotive';
		$cats['Transportation:BBB'] = 'Games & Hobbies:Automotive';
		$cats['Transportation:BBB'] = 'Sports & Recreation:Outdoor';
		$cats['Transportation:BBB'] = 'Games & Hobbies:Automotive';

		$cats['Travel'] = 'Society & Culture:Places & Travel';

		$current = str_replace('&amp;', '&', $current);
		if(isset($cats[$current])) {
			$result = $cats[$current];
		} else {
			$result = $current;
		}

		return str_replace('&', '&amp;', $result);
	}

	function podPress_itunesCategoryOptions2($current = '') {
		$cats = array();

		$cats['Arts &amp; Entertainment'] = 'Arts &amp; Entertainment';
		$cats['Arts &amp; Entertainment:Architecture'] = 'Arts &amp; Entertainment:Architecture';
		$cats['Arts &amp; Entertainment:Books'] = 'Arts &amp; Entertainment:Books';
		$cats['Arts &amp; Entertainment:Design'] = 'Arts &amp; Entertainment:Design';
		$cats['Arts &amp; Entertainment:Poetry'] = 'Arts &amp; Entertainment:Poetry';
		$cats['Arts &amp; Entertainment:Games'] = 'Arts &amp; Entertainment:Games';
		$cats['Arts &amp; Entertainment:Performing Arts'] = 'Arts &amp; Entertainment:Performing Arts';
		$cats['Arts &amp; Entertainment:Photography'] = 'Arts &amp; Entertainment:Photography';
		$cats['Arts &amp; Entertainment:Science Fiction'] = 'Arts &amp; Entertainment:Science Fiction';

		$cats['Audio Blogs'] = 'Audio Blogs';

		$cats['Business'] = 'Business';
		$cats['Business:Careers'] = 'Business:Careers';
		$cats['Business:Finance'] = 'Business:Finance';
		$cats['Business:Investing'] = 'Business:Investing';
		$cats['Business:Management'] = 'Business:Management';
		$cats['Business:Marketing'] = 'Business:Marketing';

		$cats['Comedy'] = 'Comedy';

		$cats['Education'] = 'Education';
		$cats['Education:K-12'] = 'Education:K-12';
		$cats['Education:Higher Education'] = 'Education:Higher Education';

		$cats['Family'] = 'Family';

		$cats['Food'] = 'Food';

		$cats['Health'] = 'Health';
		$cats['Health:Diet &amp; Nutrition'] = 'Health:Diet &amp; Nutrition';
		$cats['Health:Fitness'] = 'Health:Fitness';
		$cats['Health:Relationships'] = 'Health:Relationships';
		$cats['Health:Self-Help'] = 'Health:Self-Help';
		$cats['Health:Sexuality'] = 'Health:Sexuality';

		$cats['International'] = 'International';
		$cats['International:Australian'] = 'International:Australian';
		$cats['International:Belgian'] = 'International:Belgian';
		$cats['International:Brazilian'] = 'International:Brazilian';
		$cats['International:Canadian'] = 'International:Canadian';
		$cats['International:Chinese'] = 'International:Chinese';
		$cats['International:Dutch'] = 'International:Dutch';
		$cats['International:French'] = 'International:French';
		$cats['International:German'] = 'International:German';
		$cats['International:Hebrew'] = 'International:Hebrew';
		$cats['International:Italian'] = 'International:Italian';
		$cats['International:Japanese'] = 'International:Japanese';
		$cats['International:Norwegian'] = 'International:Norwegian';
		$cats['International:Polish'] = 'International:Polish';
		$cats['International:Portuguese'] = 'International:Portuguese';
		$cats['International:Spanish'] = 'International:Spanish';
		$cats['International:Swedish'] = 'International:Swedish';

		$cats['Movies &amp; Television'] = 'Movies &amp; Television';

		$cats['Music'] = 'Music';

		$cats['News'] = 'News';

		$cats['Politics'] = 'Politics';

		$cats['Public Radio'] = 'Public Radio';

		$cats['Religion &amp; Spirituality'] = 'Religion &amp; Spirituality';
		$cats['Religion &amp; Spirituality:Buddhism'] = 'Religion &amp; Spirituality:Buddhism';
		$cats['Religion &amp; Spirituality:Christianity'] = 'Religion &amp; Spirituality:Christianity';
		$cats['Religion &amp; Spirituality:Islam'] = 'Religion &amp; Spirituality:Islam';
		$cats['Religion &amp; Spirituality:Judaism'] = 'Religion &amp; Spirituality:Judaism';
		$cats['Religion &amp; Spirituality:New Age'] = 'Religion &amp; Spirituality:New Age';
		$cats['Religion &amp; Spirituality:Philosophy'] = 'Religion &amp; Spirituality:Philosophy';
		$cats['Religion &amp; Spirituality:Spirituality'] = 'Religion &amp; Spirituality:Spirituality';

		$cats['Science'] = 'Science';

		$cats['Sports'] = 'Sports';

		$cats['Talk Radio'] = 'Talk Radio';

		$cats['Technology'] = 'Technology';
		$cats['Technology:Computers'] = 'Technology:Computers';
		$cats['Technology:Developers'] = 'Technology:Developers';
		$cats['Technology:Gadgets'] = 'Technology:Gadgets';
		$cats['Technology:Information Technology'] = 'Technology:Information Technology';
		$cats['Technology:News'] = 'Technology:News';
		$cats['Technology:Operating Systems'] = 'Technology:Operating Systems';
		$cats['Technology:Podcasting'] = 'Technology:Podcasting';
		$cats['Technology:Smart Phones'] = 'Technology:Smart Phones';
		$cats['Technology:Text/Speech'] = 'Technology:Text/Speech';

		$cats['Travel'] = 'Travel';

		reset($cats);

		foreach ($cats as $key => $value) {
			echo '<option value="'.$key.'"';
			if($key == $current) {
				echo ' selected="selected"';
			}
			echo '>'.$value.'</option>'."\n";
		}
	}

	/**************************************************************/
	/* Functions for supporting the backend processor */
	/**************************************************************/

	function podPress_ResolveReDirects($uriFileName)
	{
		GLOBAL $podPress;

		$tries = 0;
		while($tries < 5) {
			$tries++;
			$aURL = parse_url($uriFileName);
			if($aURL['scheme'] != 'http') {
				return;
			}
			$sHost = $aURL['host'];
			$sFilepath = (isset($aURL['path']) ? $aURL['path'] : '/') . (isset($aURL['query']) ? '?' . $aURL['query'] : '');
			$nPort = isset($aURL['port']) ? $aURL['port'] : 80;
			$fpRemote = @fsockopen($sHost, $nPort, $errno, $errstr, 30);
			// Make sure the socket is open
			if(!$fpRemote) {
				return;
			}
			// Request headers
			$sHeaders = "HEAD " . $sFilepath . " HTTP/1.1\r\n";
			$sHeaders .= "Host: ". $sHost . "\r\n";
			$sHeaders .= "Connection: Close\r\n\r\n";
			// Sending headers
			fwrite($fpRemote, $sHeaders);
			// Getting back the content
			$sRemoteHeaders = '';
			while(!feof($fpRemote)) {
				$sRemoteHeaders .= fgets($fpRemote, 128);
			}
			// Closing the socket
			fclose($fpRemote);
			// parse headers
			$crlf = "\r\n";
			$headers = array();
			$lines = explode($crlf, $sRemoteHeaders);
			foreach($lines as $line) {
				if(($pos = strpos($line, ':')) !== false) {
					$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));
				}
			}
     
			// redirection?
			if(isset($headers['location'])) {
				$uriFileName = $headers['location'];
			} else {
				return $uriFileName;
			}
		}
		return $uriFileName;
	}
	
	function podPress_getID3tags($mediafile, $resolved = false, $limitDownload = false) {
		GLOBAL $podPress;
		$tmp_download_exists = FALSE;

		if($podPress->settings['enablePodangoIntegration']) {
			if(substr($mediafile, 0, strlen('Podango:')) == 'Podango:') {
				$fileNameParts = explode(':', $mediafile);
				$mediafile = 'http://download.podango.com/mediatracker/555/'.$fileNameParts[3].'/'.$fileNameParts[4];
			}
		}
		
		podPress_var_dump('podPress_getID3tags - before inclusion of the getID3 library');
		
		require_once(ABSPATH.PLUGINDIR.'/podpress/getid3/getid3.php');
		$getID3 = new getID3;
		if($getID3 == false) {
			return __('Remote MP3 File could not be read. (error loading ID3 reader)', 'podpress');
		}
		
		podPress_var_dump('podPress_getID3tags - after inclusion of the getID3 library');
		
		if(!$resolved) {
			$systemFileName = $podPress->convertPodcastFileNameToSystemPath($mediafile);
		} else {
			$systemFileName = $mediafile;
		}
		
		podPress_var_dump('podPress_getID3tags - before a possible download of the media file');

		if(file_exists($systemFileName)) {
			$uriFileName = $systemFileName;
		} else {
			if(!$resolved) {
				$uriFileName = $podPress->convertPodcastFileNameToValidWebPath($mediafile);
			} else {
				$uriFileName = $mediafile;
			}
			$local_uploadURL = $podPress->uploadURL;
			podPress_var_dump('podPress_getID3tags - local_uploadURL: '.$local_uploadURL);
			podPress_var_dump('podPress_getID3tags - local_uploadPath: '.$podPress->uploadPath);
			podPress_var_dump('podPress_getID3tags - tempFileSystemPath: '.$podPress->tempFileSystemPath);
			podPress_var_dump('podPress_getID3tags - tempFileURLPath: '.$podPress->tempFileURLPath);
			podPress_var_dump('podPress_getID3tags - uriFileName: '.$uriFileName);
			if (FALSE !== $local_uploadURL AND FALSE !== strpos($uriFileName, $local_uploadURL)) { // then it is not a real remote file - it is only an URL to a local file
				// get the absolute folder of the file from the URL
				podPress_var_dump('podPress_getID3tags - it is a local file');
				$uriFileName = $podPress->uploadPath.str_replace($local_uploadURL,'',$uriFileName);
				$uriFileName = str_replace('\\', '/',$uriFileName);
				podPress_var_dump('podPress_getID3tags - new uriFileName: '.$uriFileName);
			} else {
				podPress_var_dump('podPress_getID3tags - go to podPress_downloadFile');
				$uriFileName = podPress_downloadFile($uriFileName, false, $limitDownload);
				$tmp_download_exists = TRUE;
			}
		}
		podPress_var_dump('podPress_getID3tags - after a possible download of the media file');

		$fileinfo = @$getID3->analyze($uriFileName);
		
		podPress_var_dump('podPress_getID3tags - after getID3->analyze');
		
		getid3_lib::CopyTagsToComments($fileinfo);
		
		podPress_var_dump('podPress_getID3tags - after getid3_lib::CopyTagsToComments');
		
		if (TRUE === $tmp_download_exists) { // if the file has been downloaded to a local folder then delete the tempfile
			if ('' != $fileinfo['filenamepath']) { // take the folder and file from getID3 if possible
				$unlink_result = @unlink($fileinfo['filenamepath']);
				// If tempnam is available then tempnam creates a tempfile which has no media file extension like .mp3. But getID3 needs these extensions. 
				// That is why tempnam (in podPress_downloadFile) is only used to get a the tmp file name and path. If the tempnam has been used then 
				// then there are 2 files the tmp file and the tmp file with a media file name extension. The latter was removed in the step before and the next step is
				// to remove the tmp file which was created by tempnam in podPress_downloadFile.
				$ext = end(explode('.', $fileinfo['filenamepath']));
				if (FALSE === empty($ext)) {
					$tempnam_file = substr($fileinfo['filenamepath'], 0, (strlen($fileinfo['filenamepath'])-strlen($ext)-1));
					$unlink_result = @unlink($tempnam_file);
				}
			} else {
				$unlink_result = @unlink($uriFileName);
				$ext = end(explode('.', $uriFileName));
				if (FALSE === empty($ext)) {
					$tempnam_file = substr($uriFileName, 0, (strlen($uriFileName)-strlen($ext)-1));
					$unlink_result = @unlink($tempnam_file);
				}
			}
		}
		
		podPress_var_dump('podPress_getID3tags - the end');
		
		return $fileinfo;
	}
	
	function podPress_showID3tags($mediafile) {
		if (FALSE !== strpos($mediafile, 'http://www.youtube')) {
			return '';
		}
		
		podPress_var_dump('start of the ID3 tag retrieval');
		$fileinfo = podPress_getID3tags($mediafile, false);
		podPress_var_dump('end of the ID3 tag retrieval');
		if(!is_array($fileinfo) OR FALSE == isset($fileinfo['comments']) ) {
			return '<div class="updated message">'.__('The file has probably no information like artist, genre, album, etc.', 'podpress').'<br /><pre class="podpress_id3tags_error">'.var_export($fileinfo, true).'</pre></div>';
		}
		
		if(isset($_GET['keynum'])) {
			$randID = $_GET['keynum'];
		} else {
			mt_srand(crc32(microtime()));
			$randID = mt_rand(1, 9999);
		}
		$result .= '<table class="the-list-x widefat podpress_id3tag_details_table">'."\n";
		$result .= '	<tr class="alternate">'."\n";
		$result .= '		<th>'.__('Artist', 'podpress').'</th><td><span id="podPressMedia_'.$randID.'_tagArtist">'.(!empty($fileinfo['comments']['artist'][0]) ? htmlentities($fileinfo['comments']['artist'][0]) : '&nbsp;').'</span></td>'."\n";
		$result .= '	</tr>'."\n";
		$result .= '	<tr>'."\n";
		$result .= '		<th>'.__('Album', 'podpress').'</th><td><span id="podPressMedia_'.$randID.'_tagAlbum">'.(!empty($fileinfo['comments']['album'][0]) ? htmlentities($fileinfo['comments']['album'][0]) : '&nbsp;').'</span></td>'."\n";
		$result .= '	</tr>'."\n";
		$result .= '	<tr class="alternate">'."\n";
		$result .= '		<th>'.__('Title', 'podpress').'</th><td><span id="podPressMedia_'.$randID.'_tagTitle">'.(!empty($fileinfo['comments']['title'][0]) ? htmlentities($fileinfo['comments']['title'][0]) : '&nbsp;').'</span></td>'."\n";
		$result .= '	</tr>'."\n";
		$result .= '	<tr>'."\n";
		$result .= '		<th>'.__('Description', 'podpress').'</th><td><span id="podPressMedia_'.$randID.'_tagDescription">'.(!empty($fileinfo['comments']['comment'][0])  ? htmlentities($fileinfo['comments']['comment'][0]) : '&nbsp;').'</span></td>'."\n";
		$result .= '	</tr>'."\n";
		$result .= '	<tr class="alternate">'."\n";
		$result .= '		<th>'.__('Genre', 'podpress').'</th><td><span id="podPressMedia_'.$randID.'_tagGenre">'.(!empty($fileinfo['comments']['genre'][0]) ? htmlentities($fileinfo['comments']['genre'][0]) : '&nbsp;').'</span></td>'."\n";
		$result .= '	</tr>'."\n";
		$result .= '	<tr>'."\n";
		$result .= '		<th>'.__('Length', 'podpress').'</th><td><span id="podPressMedia_'.$randID.'_tagLength">'.(!empty($fileinfo['playtime_string']) ? $fileinfo['playtime_string'] : '&nbsp;').'</span></td>'."\n";
		$result .= '	</tr>'."\n";
		$result .= '	<tr class="alternate">'."\n";
		$result .= '		<th>'.__('Cover Art', 'podpress').'</th><td><span id="podPressMedia_'.$randID.'_tagCoverArt"><img src="'.podPress_url().'podpress_backend.php?action=id3image&filename='.urlencode($mediafile).'" alt="."/></span></td>'."\n";
		$result .= '	</tr>'."\n";
		//~ $result .= '	<tr>'."\n";
		//~ $result .= '		<td colspan="2"><a href="#" onclick="podPressID3ToPost(\''.$randID.'\'); return false;">'.__('Copy contents to post details', 'podpress').'</a></td>'."\n";
		//~ $result .= '	</tr>'."\n";
		$result .= '</table>'."\n";
		return $result;
	}

	function podPress_getCoverArt($mediafile) {
		$fileinfo = podPress_getID3tags($mediafile, false, 500000);
		if(isset($fileinfo['id3v2']['APIC'][0])) {
			$ref = $fileinfo['id3v2']['APIC'][0];
		} elseif(isset($fileinfo['id3v2']['PIC'][0])) {
			$ref = $fileinfo['id3v2']['PIC'][0];
		} else {
			$ref['image_mime'] = 'image/jpeg';
			$ref['datalength'] = @filesize('images/powered_by_podpress.png');
			$ref['data'] = @file_get_contents('images/powered_by_podpress.png');
		}
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // some day in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Content-type: '.$ref['image_mime']);
//		header('Content-Disposition: attachment; filename="coverart.png"');
		header("Content-Length: ".$ref['datalength']);
		set_time_limit(0);
		echo $ref['data'];
	}

	function podPress_getDuration($mediafile)
	{
		GLOBAL $podPress;
		if($podPress->settings['enablePodangoIntegration']) {
			if(substr($mediafile, 0, strlen('Podango:')) == 'Podango:') {
				$fileNameParts = explode(':', $mediafile);
				$mediafile = 'http://download.podango.com/mediatracker/555/'.$fileNameParts[3].'/'.$fileNameParts[4];
			}
		}
		$id3formats = array('mp3', 'ogg', 'avi', 'mov', '.qt', 'mp4', 'm4v', 'm4a', 'wma', 'wmv', 'mpg', 'peg', 'flv', 'swf');
		if(in_array(strtolower(substr($mediafile, -3, 3)), $id3formats)) {
			podPress_var_dump('start of the duration retrieval');
			$systemFileName = $podPress->convertPodcastFileNameToSystemPath($mediafile);
			if(file_exists($systemFileName)) {
				$uriFileName = $systemFileName;
			} else {
				$systemFileName = $podPress->TryToFindAbsFileName($mediafile);
				if(FALSE !== $systemFileName) {
					$uriFileName = $systemFileName;
				} else {
					$uriFileName = $podPress->convertPodcastFileNameToValidWebPath($mediafile);
				}
			}
			$fileinfo = podPress_getID3tags($uriFileName, true);
			podPress_var_dump('podPress_getDuration - file: '.$uriFileName);
			podPress_var_dump('podPress_getDuration - playtime_string: '.$fileinfo['playtime_string']);
			podPress_var_dump('end of the duration retrieval');

			return $fileinfo['playtime_string'];
		}
	}

	function podPress_downloadFile($uriFileName, $getHeaders = false, $limitDownload = false) {
		GLOBAL $podPress;
		
		// some downloads may hit time limits -> lets find out these limits, stop the process a moment before and return an error message
		// search for PHP time limits
		$max_execution_time = intval(ini_get('max_execution_time'));
		podPress_var_dump('podPress_downloadFile - max_execution_time: '.$max_execution_time);
		$safety = 2; // seconds
		
		// get the OS type on the server
		$hairstick = php_uname();
		$is_windows = stristr( $hairstick, 'win' );
		// search for CPU time limits
		if (FALSE === $is_windows) { 
			@exec('ulimit -t', $output) or $output[0] = 10;
			$int_output = intval($output[0]);
			podPress_var_dump('podPress_downloadFile - ulimit -t: '. $output[0].' - (int): '.$int_output);
			if (is_string($output[0]) AND 10 > $int_output) {
				$max_time = max(($safety+2), $max_execution_time); // if max_execution_time is somehow weird small
			} else {
				$max_time = min(intval($output[0]), $max_execution_time); // if the CPU limit is bigger than 10 then take the max. of the CPU limit and the max_execution_time
			}
		} else {
			$max_time = $max_execution_time; // on Windows systems
		}
		podPress_var_dump('podPress_downloadFile - time limit: '.$max_time);

		$aborttimelimit = FALSE;
		$start_time = array_sum(explode(chr(32), microtime()));
		
		podPress_var_dump('podPress_downloadFile - given URL: '.$uriFileName);
		$uriFileName = podPress_ResolveReDirects($uriFileName);
		//$aURL = parse_url(($uriFileName));
		//$aURL = parse_url(rawurldecode($uriFileName));
		$aURL = @parse_url(urldecode($uriFileName));
		if($aURL['scheme'] != 'http') {
			podPress_var_dump('podPress_downloadFile - returning - not a http:// URL : '.$uriFileName);
			return;
		}
		$sHost = $aURL['host'];
		$sFilepath = (isset($aURL['path']) ? $aURL['path'] : '/') . (isset($aURL['query']) ? '?' . $aURL['query'] : '');
		$sFilepath = str_replace(' ', '%20', $sFilepath);
		$nPort = isset($aURL['port']) ? $aURL['port'] : 80;
		$fpRemote = @fsockopen($sHost, $nPort, $errno, $errstr, 30);
		if( $fpRemote ) {
			if($getHeaders) {
				podPress_var_dump('podPress_downloadFile - get only the header');
				podPress_var_dump('podPress_downloadFile - ' . $sHost . $sFilepath);

				$sHeaders = "HEAD " . $sFilepath . " HTTP/1.1\r\n";
				$sHeaders .= "Host: ". $sHost . "\r\n";
				$sHeaders .= "Connection: Close\r\n\r\n";
				podPress_var_dump($sHeaders);
				
				// Sending headers
				fwrite($fpRemote, $sHeaders);
				
				// Getting back the header information
				$header = '';
				$line = '';
				$continue = true;
				$soFar = 0;
				// Processing the server answer (header) line by line
				while(!feof($fpRemote) && $continue) {
					$line = fgets($fpRemote, 1024);
					$soFar = $soFar+1024;
					if ($limitDownload !== false && $soFar > $limitDownload) {
						$continue = false;
					}
					$header .= $line;
				}

				// Closing the socket
				fclose($fpRemote);
				
				podPress_var_dump('podPress_downloadFile - (header) before return');
				podPress_var_dump($header);

				return $header;
			} else {
				podPress_var_dump('podPress_downloadFile - download the file');
				podPress_var_dump('podPress_downloadFile - ' . $sHost . $sFilepath);

				$sHeaders = "GET " . $sFilepath . " HTTP/1.1\r\n";
				$sHeaders .= "Host: ". $sHost . "\r\n";
				$sHeaders .= "Connection: Close\r\n\r\n";

				// Sending headers
				fwrite($fpRemote, $sHeaders);
				
				// determine a temp folder and temp file name
				$ext = podPress_getFileExt($uriFileName);
				$pos = strpos($ext, '?');
				if($pos) {
					$ext = substr($ext, 0, $pos);
				}
				// Trying to write into the servers temp folder. The temp file has to have the media files extension. Because getID3 does not work without it.
				$localtempfilename = @tempnam('/tmp', 'getID3').'.'.$ext;
				if (FALSE == $localtempfilename OR '.'.$ext == $localtempfilename) {
					// If it is not possible to get the temp folder via tempnam() then try to write into the temp folder of podPress
					$podPress->checkWritableTempFileDir();
					$localtempfilename = $podPress->tempFileSystemPath.'/podpress_tmp.'.$ext;
				}
				if (!$fp_local = @fopen($localtempfilename, 'wb')) {
					// if it is not possible to open a temp file then return
					// Closing the socket
					fclose($fpRemote);
					return '';
				}
				podPress_var_dump('podPress_downloadFile - (file) localtempfilename: '.$localtempfilename);
				
				// Getting back header + content
				$continue = true;
				$is_body = false;
				$soFar = 0;
				$line = "";
				$trimmed_line = "";
				$step_time = array_sum(explode(chr(32), microtime()));
				
				// Processing the server answer (header + content) line by line
				while( !feof($fpRemote) && $continue ) {
					$line = fgets($fpRemote, 1024);
					$soFar = $soFar+1024;
					// some times the answer of the remote server contains a HTTP header which should not be a part of the temp file
					// after the HTTP should always be a empty line which is followed by the remote file - see http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4
					if ($is_body) {
						if($limitDownload !== false && $soFar > $limitDownload) {
							$continue = false;
						}
						// write the content of the remote file into the temp file
						fwrite($fp_local, $line);
					} else {
						$trimmed_line = Trim($line);
						if ( FALSE !== empty($trimmed_line) ) {
							// if the line is empty then the last line of the HTTP header of the server answer and the file content begins
							$is_body = true;
						} 
						$j++;
					}
					$step_time = array_sum(explode(chr(32), microtime()));
					if (($step_time - $start_time) >= ($max_time-$safety)) {
						$continue = FALSE;
						$aborttimelimit = TRUE;
						podPress_var_dump('podPress_downloadFile - aborting download because of a time limit');
					}
				}
				// Closing the temp file
				fclose($fp_local);
				
				// if the download was stop due to a timelimit then it is most likely not complete. Delete the file that getID3 will produce an error message
				if (TRUE === $aborttimelimit) {
					@unlink($localtempfilename);
					$localtempfilename = 'stop due to time limit: '.$max_time;
				}
				
				// Closing the socket
				fclose($fpRemote);
				
				podPress_var_dump('podPress_downloadFile - (file) before return');
				
				// return the name of the temp file
				return $localtempfilename;
			}
		} else {
			if ($getHeaders) {
				podPress_var_dump('podPress_downloadFile - fpRemote = false | getHeaders = true');

				if ($fp_remote = @fopen($uriFileName, 'rb')) {
					$content = fread($fp_remote, 2048);
					fclose($fp_remote);
					if(!empty($content)) {
						return $content;
					}
				}

				if (function_exists('curl_init') && $ch = curl_init($aURL['scheme'].'://'.$aURL['host'])) {
					$fp = fopen($sFilepath, 'w');

					curl_setopt($ch, CURLOPT_FILE, $fp);
					curl_setopt($ch, CURLOPT_HEADER, 0);

					$content = curl_exec($ch);
					curl_close($ch);
					fclose($fp);
					return $content;
				}
				return '';
			} else {
				podPress_var_dump('podPress_downloadFile - fpRemote = false | getHeaders = false');
				return '';
			}
		}
	}

	function podPress_getFileSize($mediafile) {
		GLOBAL $podPress;
		podPress_var_dump('start of the file size retrieval');
		if($podPress->settings['enablePodangoIntegration']) {
			if(substr($mediafile, 0, strlen('Podango:')) == 'Podango:') {
				$fileNameParts = explode(':', $mediafile);
				$mediafile = 'http://download.podango.com/mediatracker/555/'.$fileNameParts[3].'/'.$fileNameParts[4];
			}
		}

		podPress_var_dump('podPress_getFileSize - media file: '.$mediafile);
		
		$systemFileName = $podPress->convertPodcastFileNameToSystemPath($mediafile);
		if(file_exists($systemFileName)) {
			$filesize = filesize($systemFileName);
			podPress_var_dump('end of the file size retrieval - (local file) file size: '.$filesize);
			return $filesize;
		}
		// if it is a remote file then get the file size from the header information:
		$uriFileName = $podPress->convertPodcastFileNameToValidWebPath($mediafile);
		
		// Request headers
		$sRemoteHeaders = podPress_downloadFile($uriFileName, TRUE);

		// Parsing the headers
		preg_match('/Content-Length:\s([0-9].+?)\s/', $sRemoteHeaders, $aMatches);
		if(isset($aMatches[1])) {
			podPress_var_dump('end of the file size retrieval - file size: '.$aMatches[1]);
			return (int)$aMatches[1];
		} else {
			podPress_var_dump('end of the file size retrieval - unable to retrieve the file size');
			return;
		}
	}
	
	/**
	* podPress_var_dump - writes a variable into a log file in the podPress folder (helper function - only for development purposes)
	*
	* @package podPress
	* @since 8.8.5 RC 2
	*
	* @param mixed $var
	*/
	function podPress_var_dump($var) {
		if ( defined( 'PODPRESS_DEBUG_LOG' ) AND TRUE === constant( 'PODPRESS_DEBUG_LOG' ) ) { 
			// write the out put to the log file
			$filename = PODPRESS_DIR.'/podpress_log.dat';
			if ( is_file($filename) ) {
				$result = @chmod($filename, 0777);
				if (FALSE === $result) {		
					return sprintf(__('This PHP script has not the permission to use chmod for the %1$s file.', 'podpress'), $filename);
				}
				if ( (filesize($filename)/1024) > 100 ) { // delete the Logfile if it is bigger than 100 kByte
					$result = @unlink($filename); 
					if (FALSE === $result) {
						$returnmsg = sprintf(' '.__('This PHP script has not the permission to delete the %1$s file (unlink).', 'podpress'), $filename);
					}
				} 
			}
			$handle = @fopen($filename, "a");
			if ( FALSE !== $handle ) { 
				@fputs($handle, '['.date('j.m.Y - H:i:s', time()).'] '.var_export($var, TRUE)."\n");
				$status = @fclose($handle);
				$returnmsg .= '';
			} else {
				$returnmsg .= sprintf(__('This PHP script has not the permission to use create or open the %1$s file for writing (fopen).', 'podpress'), $filename);
			}
			if ( is_file($filename) ) { 
				$result = @chmod($filename, 0644); 
				if (FALSE === $result) {
					$returnmsg = sprintf(' '.__('This PHP script has not the permission to use chmod for the %1$s file.', 'podpress'), $filename);
				}
			}
			return $returnmsg;
		}
	}
?>