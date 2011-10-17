<?php
/**
Plugin Name: Customized Recent Comments
Version: 1.1
Plugin URI: http://justmyecho.com/2010/07/customized-recent-comments/
Description: A recent comments widget that allows for customizing, format changes and other options.
Author: Robin Dalton
Author URI: http://justmyecho.com
**/

class jme_recent_comments {
	
	var $jme_options = 'jme_options';
	var	$jme_options_default = array(	'num_of_comments' => 10,
										'word_limit' => 20,
										'c_template' => '<div class="avatar">%AVATAR%</div><h3>%AUTHORLINK% on <a href="%PERMALINK%">%POSTTITLE%</a></h3>%COMMENT%<div class="comment-meta">Posted %POSTDATE%</div>',
										'include_cat' => '',
										'exclude_cat' => '',
										'date_format' => 'M d, Y',
										'avatar_size' => 40,
										'cat_archive' => 0,
										'limit_type' => 0,
										'load_css' => 0,
										'facebook_avatar' => 0
									);
	
	var $search = array (	
							'%ID%',
							'%AUTHOR%',
							'%AUTHORLINK%',
							'%COMMENT%',
							'%POSTDATE%',
							'%AVATAR%',
							'%PERMALINK%',
							'%POSTTITLE%'
						);
	
	function jme_recent_comments() {
	}
	
	function jme_comments_activate() {
		if (!get_option($this->jme_options))	{
			add_option($this->jme_options , $this->jme_options_default);
		} else {
			update_option($this->jme_options , $this->jme_options_default);
		}
	}
	
	function jme_comments_deactivate() {
		delete_option($this->jme_options);
	}
	
	function jme_add_options_page() {
		add_options_page("Recent Comments", "Recent Comments", 'edit_themes', basename(__FILE__), array(&$this, 'jme_the_options_page') );
	}
	
	function jme_the_options_page() {
		
		if(($_GET['reset'] == 'options') && (!$_POST)) {
			$this->jme_comments_activate();
			echo '<div id="message" class="updated fade"><p>Options have been reset to defaults.</p></div>';
		}
		
		if($_POST['save_settings']) {
			foreach($_POST as $option => $val) {
				if($option != 'save_settings' || $option != 'generate_code') {
					$options[$option] = htmlentities($val);
				}
			}
			$options['cat_archive'] = ($options['cat_archive'] == 1) ? 1 : 0;
			$options['limit_type'] = ($options['limit_type'] == 1) ? 1 : 0;
			$options['load_css'] = ($options['load_css'] == 1) ? 1 : 0;
			$options['fb_avatar'] = ($options['fb_avatar'] == 1) ? 1 : 0;
			$options['c_template'] = stripslashes($_POST['c_template']);
			update_option($this->jme_options, $options);
			
			echo '<div id="message" class="updated fade"><p>Your options have been saved.</p></div>';			
		}
					
		$options = get_option($this->jme_options);
	
?>
<style type="text/css">
.wrap #poststuff .postbox .inside td {padding:4px 0;font-size:12px;}
.wrap #poststuff .postbox .inside td div {padding:3px 0;}
.wrap #poststuff .postbox .inside td div.s {font-size:10px;}
.wrap #poststuff .postbox .inside td div.s div span {background-color:#d1eaff;} 
.st { width:70px;}
.lt {width:400px;}
</style>
<script type="text/javascript">
function jme_generate_code(form) {
	var output = '';
	
	var template = addslashes(form.c_template.value);
	if(template == '<?php echo $this->jme_options_default['c_template']; ?>') {
		template = '__default__';
	}
	var catarch = 0;
	if(form.cat_archive.checked == 1) catarch = 1;
	
	output = "<\?php if(function_exists('jme_display_comments')) { jme_display_comments( array( ";
	if( form.num_of_comments.value != 10 ) {
		output += "1 => " + form.num_of_comments.value + ", ";
	}
	if( form.word_limit.value != 20 ) {
		output += "2 => " + form.word_limit.value + ", ";
	}
	if( addslashes(form.include_cat.value) != '' ) {
		output += "4 => '" + addslashes(form.include_cat.value) + "', ";
	}
	if( addslashes(form.exclude_cat.value) != '') {
		output += "5 => '" + addslashes(form.exclude_cat.value) + "', ";
	}
	if( catarch == 1 ) {
		output += "8 => " + catarch + ", ";
	}
	if( form.avatar_size.value != 40 ) {
		output += "7 => " + form.avatar_size.value + ", ";
	}
	if( addslashes(form.date_format.value) != 'M d, Y' ) {
		output += "6 => '" + addslashes(form.date_format.value) + "', ";
	}
	for( i = 0; i < form.limit_type.length; i++ ) {
		if( form.limit_type[i].checked == true ) {
			limittype = form.limit_type[i].value;
		}
	}
	if( limittype == 1 ) {
		output += "9 => 1, ";
	}
	output += "3 => '" + template + "'));} ?>";
	document.getElementById('generated_code').value = output;	
}
function addslashes(str) {
	str=str.replace(/\'/g,'\\\'');
	return str;
}
</script>
<div class="wrap">
<div id="poststuff">
	<h2>Customized Recent Comments</h2>
	<div>This plugin has a Recent Comments widget. Go to your Widgets section to add Customized Recent Comments to your widget sidebars.</div>
	<p>Or you can add recent comments anywhere on your blog by selecting your options below, click "Generate Code" and paste the code into your theme template.</p>
	
	<form method="post" name="jme_options">

	<div class="postbox">
		<h3 class="hndle"><span><?php _e("Recent Comments Widget Options",'jme-rc'); ?></span></h3>
		<div class="inside">
		<table>
			<tr>
				<td width="280"><?php _e('Number of Comments','jme-rc'); ?>:</td>
				<td><input type="text" class="st" id="num_of_comments" name="num_of_comments" value="<?php echo htmlentities($options['num_of_comments']); ?>" /></td>
			</tr>
			<tr>
				<td><?php _e('Limit comment text by','jme-rc'); ?>:</td>
				<td><label><input type="radio" name="limit_type" value="0" <?php if($options['limit_type'] == 0) echo 'checked="checked" '; ?>/> <?php _e('Words','jme-rc'); ?></label> &nbsp; &nbsp; 
					<label><input type="radio" name="limit_type" value="1" <?php if($options['limit_type'] == 1) echo 'checked="checked" '; ?>/> <?php _e('Characters','jme-rc'); ?></label></td>
			</tr>
			<tr>
				<td><?php _e('Word/Character limit for each comment','jme-rc'); ?>:</td>
				<td><input type="text" class="st" id="word_limit" name="word_limit" value="<?php echo htmlentities($options['word_limit']); ?>" /></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td valign="top"><?php _e('Comment Template','jme-rc'); ?>:
					<div class="s"><div><strong><?php _e('Available Tags for Template','jme-rc'); ?>:</strong></div>
					<div><span>%ID%</span>: Comment ID</div>
					<div><span>%AUTHOR%</span>: Comment Author</div>
					<div><span>%AUTHORLINK%</span>: Author Link (Outputs: <span style="background:#e9e9e9;"><?php echo htmlspecialchars('<a href="__URL__">%AUTHOR%</a>'); ?></span>, or just <span style="background:#e9e9e9;">%AUTHOR%</span> if URL doesn't exist.)</div>
					<div><span>%COMMENT%</span>: Comment Text</div>
					<div><span>%POSTDATE%</span>: Comment Date</div>
					<div><span>%AVATAR%</span>: Commenter Avatar</div>
					<div><span>%POSTTITLE%</span>: Post Title</div>
					<div><span>%PERMALINK%</span>: Post Permalink</div>
					</div>
				</td>
				<td><textarea style="width:400px;height:200px;" id="c_template" name="c_template"><?php echo htmlspecialchars($options['c_template']); ?></textarea></td>
			</tr>
			
			<tr>
				<td><?php _e('Date format for comments','jme-rc'); ?>:
				<div class="s"><?php _e('Used in %POSTDATE% tag in the template','jme-rc'); ?></div></td>
				<td><input type="text" class="st" id="date_format" name="date_format" value="<?php echo htmlentities($options['date_format']); ?>" /></td>
			</tr>
			<tr>
				<td><?php _e('Avatar Size in pixels','jme-rc'); ?>:</td>
				<td><input type="text" class="st" id="avatar_size" name="avatar_size" value="<?php echo htmlentities($options['avatar_size']); ?>" /></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td><?php _e('Include comments from Categories','jme-rc'); ?>:
				<div class="s"><?php _e('Separate categories by comma','jme-rc'); ?></div></td>
				<td valign="top"><input type="text" class="lt" id="include_cat" name="include_cat" value="<?php echo htmlentities($options['include_cat']); ?>" /></td>	
			</tr>
			<tr>
				<td><?php _e('Exclude comments from Categories','jme-rc'); ?>:
				<div class="s"><?php _e('Only applied if Include category list is empty.','jme-rc'); ?></div></td>
				<td valign="top"><input type="text" class="lt" id="exclude_cat" name="exclude_cat" value="<?php echo htmlentities($options['exclude_cat']); ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><label><input type="checkbox" id="cat_archive" name="cat_archive" value="1"<?php if($options['cat_archive'] == 1) echo ' checked="checked"'; ?> /> <?php _e('If on a Category Archive page, show recent comments from that Category only.','jme-rc'); ?></label></td>
			</tr>		
			<tr>
				<td></td>
				<td><p class="submit"><input type="submit" name="save_settings" value="<?php _e('Save These Settings','jme-rc'); ?>" /> &nbsp; <input type="button" name="generate_code" value="<?php _e('Generate Code','jme-rc'); ?>" onClick="jme_generate_code(this.form);return false;" /></p></td>
			</tr>
			<tr>
				<td></td>
				<td><?php _e('Copy and paste the generated code below into your Theme template where you want the comment list to display','jme-rc'); ?>:
				<div><textarea id="generated_code" style="width:400px;height:120px;font-size:11px;"></textarea></div>
				<div class="s"><a href="?page=customized-recent-comments.php&reset=options"><?php _e('Click here to reset all options back to default.','jme-rc'); ?></a></div>
				</td>
			</tr>
		</table>
	
	</div>
	</div>
	
	<div class="postbox">
		<h3 class="hndle"><span><?php _e("Recent Comments Plugin Options",'jme-rc'); ?></span></h3>
		<div class="inside">
		<table>
			<tr>
				<td width="280"></td>
				<td><label for="load_css"><input type="checkbox" id="load_css" name="load_css" value="1" <?php if($options['load_css'] == 1) echo 'checked="checked" '; ?>/> <?php _e('Don\'t load default CSS file.','jme-rc'); ?></label>
				<div class="s"><?php _e('If you check this option, you will need to create your own CSS styles for the comment list.','jme-rc'); ?></div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><label for="fb_avatar"><input type="checkbox" id="fb_avatar" name="fb_avatar" value="1" <?php if($options['fb_avatar'] == 1) echo 'checked="checked" '; ?>/> <?php _e('Use FaceBook Avatar if user comments with their Facebook identity.','jme-rc'); ?></label>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><p class="submit"><input type="submit" name="save_settings" value="<?php _e('Save Options', 'jme-rc'); ?>" /></p></td>
			</tr>
			<tr>
				<td colspan="2"><?php _e('Support and feature requests can be made on the','jme-rc'); ?> <a href="http://justmyecho.com/2010/07/customized-recent-comments/"><?php _e('plugin webpage','jme-rc'); ?></a>.</td>
			</tr>
		</table>
		</div>
	</div>
	
	</form>
</div>
		
</div>
	<?php	
	}
}

class jme_Custom_Comments_Widget extends WP_Widget {

	function jme_Custom_Comments_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'comments', 'description' => __('A customizable Recent Comments list.', 'comments') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 380, 'height' => 350, 'id_base' => 'comment-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'comment-widget', __('Customized Recent Comments', 'comments'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract($args);
		$data[1] = $instance['num_of_comments'];
		$data[2] = $instance['comment_length'];
		$data[3] = $instance['comment_list_template'];
		$data[4] = $instance['include_cat'];
		$data[5] = $instance['exclude_cat'];
		$data[6] = $instance['post_time_format'];
		$data[7] = $instance['avatar_size'];
		$data[8] = $instance['cat_archive'];
		$data[9] = $instance['limit_type'];
		
		echo $before_widget;
		if($instance['title'] != '') {
			echo $before_title . $instance['title'] . $after_title;
		}
		jme_display_comments( $data );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		global $jme_RC;
		$instance = $old_instance;
		
		$options = get_option($jme_RC->jme_options);

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		foreach($new_instance as $key => $val) {
			$instance[$key] = strip_tags( $new_instance[$key] );
		}
		$instance['cat_archive'] = ($new_instance['cat_archive'] == 1) ? 1 : 0;
		$instance['limit_type'] = ($new_instance['limit_type'] == 1) ? 1 : 0;
		$instance['comment_list_template'] = $new_instance['comment_list_template'];

		return $instance;
	}

	function form( $instance ) {
		global $jme_RC;
		
		/* Set up some default widget settings. */
		$defaults = array( 	'title' => 'Recent Comments',
							'num_of_comments' => '10',
							'comment_length' => '20',
							'avatar_size' => '40',
							'post_time_format' => 'M d, Y',
							'comment_list_template' => $jme_RC->jme_options_default['c_template'],
							'include_cat' => '',
							'exclude_cat' => '',
							'cat_archive' => 0,
							'limit_type' => 0
						 );
							
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'comments'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:250px;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'num_of_comments' ); ?>"><?php _e('Number of Comments:', 'comments'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'num_of_comments' ); ?>" name="<?php echo $this->get_field_name( 'num_of_comments' ); ?>" value="<?php echo $instance['num_of_comments']; ?>" style="width:50px;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'limit_type' ); ?>"><?php _e('Limit comment text by:', 'comments'); ?></label>
			<label><input type="radio" name="<?php echo $this->get_field_name( 'limit_type' ); ?>" value="0" <?php if($instance['limit_type'] == 0) echo 'checked="checked" '; ?>/>Words</label> &nbsp; <label><input type="radio" name="<?php echo $this->get_field_name( 'limit_type' ); ?>" value="1" <?php if($instance['limit_type'] == 1) echo 'checked="checked" '; ?>/>Characters</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'comment_length' ); ?>"><?php _e('Word/Character limit for each comment:', 'comments'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'comment_length' ); ?>" name="<?php echo $this->get_field_name( 'comment_length' ); ?>" value="<?php echo $instance['comment_length']; ?>" style="width:50px;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'comment_list_template' ); ?>"><?php _e('Comment Template:', 'comments'); ?></label>
			<textarea id="<?php echo $this->get_field_id( 'comment_list_template' ); ?>" name="<?php echo $this->get_field_name( 'comment_list_template' ); ?>" style="width:375px;height:125px;"><?php echo htmlspecialchars($instance['comment_list_template']); ?></textarea><br />
			<span style="font-size:.9em;">
				View all available tags under "Settings > Recent Comments"
			</span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'post_time_format' ); ?>"><?php _e('Comment date format:', 'comments'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'post_time_format' ); ?>" name="<?php echo $this->get_field_name( 'post_time_format' ); ?>" value="<?php echo $instance['post_time_format']; ?>" style="width:80px;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'avatar_size' ); ?>"><?php _e('Avatar size:', 'comments'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'avatar_size' ); ?>" name="<?php echo $this->get_field_name( 'avatar_size' ); ?>" value="<?php echo $instance['avatar_size']; ?>" style="width:50px;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'include_cat' ); ?>"><?php _e('Include Comments from Categories:', 'comments'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'include_cat' ); ?>" name="<?php echo $this->get_field_name( 'include_cat' ); ?>" value="<?php echo $instance['include_cat']; ?>" style="width:375px;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'exclude_cat' ); ?>"><?php _e('Exclude Comments from Categories:', 'comments'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'exclude_cat' ); ?>" name="<?php echo $this->get_field_name( 'exclude_cat' ); ?>" value="<?php echo $instance['exclude_cat']; ?>" style="width:375px;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'cat_archive' ); ?>"><input type="checkbox" id="<?php echo $this->get_field_id( 'cat_archive' ); ?>" name="<?php echo $this->get_field_name( 'cat_archive' ); ?>" value="1"<?php if($instance['cat_archive'] == 1) echo ' checked="checked"'; ?> /><?php _e(' If on Category Archive page, show recent comments from that Category only.', 'comments'); ?></label>
		</p>

	<?php
	}
}

function jme_custom_comments_load_widget() {
	register_widget( 'jme_Custom_Comments_Widget' );
}

function jme_custom_comment_style() {
	global $jme_RC;
	$options = get_option($jme_RC->jme_options);
	if($options['load_css'] == 0) {
	$plugin_path = WP_CONTENT_URL . '/plugins/'. plugin_basename(dirname(__FILE__)) . '/';
	echo '<link rel="stylesheet" href="' . $plugin_path . 'css/style.css" type="text/css" />' . "\r\n";
	}
}

function jme_display_comments( $args ) {
	global $wpdb, $jme_RC;
	
	$options = get_option($jme_RC->jme_options);
	
	//set defaults if not defined
	if(!isset($args[1])) $args[1] = $jme_RC->jme_options_default['num_of_comments'];
	if(!isset($args[2])) $args[2] = $jme_RC->jme_options_default['word_limit'];
	if(!isset($args[3]) || ($args[3] == '__default__')) $args[3] = $jme_RC->jme_options_default['c_template'];
	if(!isset($args[4])) $args[4] = $jme_RC->jme_options_default['include_cat'];
	if(!isset($args[5])) $args[5] = $jme_RC->jme_options_default['exclude_cat'];
	if(!isset($args[6])) $args[6] = $jme_RC->jme_options_default['date_format'];
	if(!isset($args[7])) $args[7] = $jme_RC->jme_options_default['avatar_size'];
	if(!isset($args[8])) $args[8] = $jme_RC->jme_options_default['cat_archive'];
	if(!isset($args[9])) $args[9] = $jme_RC->jme_options_default['limit_type'];
	
	if(($args[4] == '') && ($args[5] == '') && ($args[8] == 0)) {
		// do basic comment query

		$query = "SELECT * FROM $wpdb->comments
							WHERE comment_approved = '1' 
							AND comment_type = ''
							ORDER BY comment_date_gmt DESC 
							LIMIT 0, $args[1]";
							
		// else if inc/exc categories, do advanced query
	} else {
		$sql_cat = '';
		if($args[8] == 1) {
			if(is_category()) {
				$catid = get_cat_id(single_cat_title("", false));
				$sql_cat = "AND t.term_id = '" . $catid . "'";
			}		
		} else if($args[4] != '') {
			$cats = explode(",",$args[4]);
			for($i=0;$i<count($cats);$i++) {
				$thecats[] = get_cat_id(trim(stripslashes($cats[$i])));
			}
			$catids = implode(",", $thecats);
			$sql_cat = "AND t.term_id IN (" . $catids . ")";
		} else if ($args[5] != '') {
			$cats = explode(",",$args[5]);
			for($i=0;$i<count($cats);$i++) {
				$thecats[] = get_cat_id(trim(stripslashes($cats[$i])));
			}
			$catids = implode(",", $thecats);
			$sql_cat = "AND t.term_id NOT IN (" . $catids . ")";
		}
		
		$query = "SELECT 	c.comment_ID,
							c.comment_post_ID,
							c.comment_author,
							c.comment_author_email,
							c.comment_author_url,
							c.comment_content
						FROM $wpdb->comments c
						LEFT JOIN $wpdb->posts p
						ON c.comment_post_ID = p.ID
						LEFT JOIN $wpdb->term_relationships r
						ON p.ID = r.object_id
						LEFT JOIN $wpdb->term_taxonomy t
						ON r.term_taxonomy_id = t.term_taxonomy_id
						WHERE c.comment_approved = '1' 
						AND c.comment_type = ''
						AND t.taxonomy = 'category'
						$sql_cat
						ORDER BY c.comment_date_gmt DESC 
						LIMIT 0, $args[1]";
						
	}
			
	$comments = $wpdb->get_results($query);

	if (!$comments) {
		$result = "none";
	}
	
	$w_out = '<ul class="customized-recent-comments">';
		
	if($result == "none") {
		$w_out .= '<p>';
		$w_out .= __('No comments to display','jme-rc');
		$w_out .= '</p>';
	} else {

		foreach ($comments as $com) {
			if($com->comment_author_url != '') {
				$authorlink = '<a href="'.$com->comment_author_url.'">'.$com->comment_author.'</a>';
			} else {
				$authorlink = $com->comment_author;
			}
			
			$content = strip_tags( $com->comment_content );	
			
			if($args[9] == 0) {			
				$words = explode(' ',$content);
				
				if(count($words) > $args[2]) {
					array_splice($words, $args[2]);
    				$output = implode(' ', $words) . '...';
    			} else {
    				$output = $content;
    			}
    		} else if($args[9] == 1) {
    			if(strlen($content) > $args[2]) {
    				$output = substr($content,0,$args[2]).'...';
    			} else {
    				$output = $content;
				}			
    		}
    		
    		// if facebook avatar option is enabled //
    		if(($options['fb_avatar'] == 1) && 
				(preg_match("@^(?:http://)?(?:www\.)?facebook@i",trim($com->comment_author_url)))) {

			        $parse_author_url = (parse_url($com->comment_author_url));
   				    $parse_author_url_q = $parse_author_url['query'];
         				if(preg_match('/id[=]([0-9]*)/', $parse_author_url_q, $match)) {
                			$fb_id = "/".$match[1];
						} else { 
							$fb_id = $parse_author_url['path'];
						}
				$the_avatar = '<img src="http://graph.facebook.com'.$fb_id.'/picture?type=square" width="'.$args[7].'" height="'.$args[7].'" />';
		 	} else {
    			$the_avatar = get_avatar( $com->comment_author_email , $args[7] );    			
    		}
    		
			
			$replace = array (	$com->comment_ID,
								$com->comment_author,
								$authorlink,
								$output,
								get_comment_date( $args[6], $com->comment_ID ),
								$the_avatar,
								get_permalink( $com->comment_post_ID ),
								get_the_title( $com->comment_post_ID )
							);
			$w_out .= '<li class="recentcomment">';
			$w_out .= stripslashes( str_replace( $jme_RC->search, $replace, $args[3]) );
			$w_out .= '</li>';
		}
	}
	$w_out .= '</ul>';

	echo $w_out;
}

$jme_RC = new jme_recent_comments();

add_action('widgets_init', 'jme_custom_comments_load_widget' );
add_action('wp_head', 'jme_custom_comment_style');
add_action('admin_menu', array(&$jme_RC, 'jme_add_options_page') );

register_activation_hook( __FILE__, array(&$jme_RC, 'jme_comments_activate') );
register_deactivation_hook( __FILE__, array(&$jme_RC, 'jme_comments_deactivate') );
?>
