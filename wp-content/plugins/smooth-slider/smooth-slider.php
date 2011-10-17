<?php
/*
Plugin Name: Smooth Slider
Plugin URI: http://www.clickonf5.org/smooth-slider
Description: Smooth Slider adds a smooth content and image slideshow with customizable background and slide intervals to any location of your blog
Version: 2.3.2	
Author: Internet Techies
Author URI: http://www.clickonf5.org/
Wordpress version supported: 2.9 and above
*/

/*  Copyright 2009-2010  Internet Techies  (email : tedeshpa@gmail.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//Please visit Plugin page http://www.clickonf5.org/smooth-slider for Changelog
//on activation
define('SLIDER_TABLE','smooth_slider'); //Slider TABLE NAME
define('PREV_SLIDER_TABLE','slider'); //Slider TABLE NAME
define('SLIDER_META','smooth_slider_meta'); //Meta TABLE NAME
define('SLIDER_POST_META','smooth_slider_postmeta'); //Meta TABLE NAME
define("SMOOTH_SLIDER_VER","2.3.2",false);//Current Version of Smooth Slider
if ( ! defined( 'SMOOTH_SLIDER_PLUGIN_BASENAME' ) )
	define( 'SMOOTH_SLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'SMOOTH_SLIDER_CSS_DIR' ) )
	define( 'SMOOTH_SLIDER_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/styles/' );

function install_smooth_slider() {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id int(5) NOT NULL AUTO_INCREMENT,
					post_id int(11) NOT NULL,
					date datetime NOT NULL,
					slider_id int(5) NOT NULL DEFAULT '1',
					UNIQUE KEY id(id)
				);";
		$rs = $wpdb->query($sql);
		
		$prev_table_name = $table_prefix.PREV_SLIDER_TABLE;
		
		if($wpdb->get_var("show tables like '$prev_table_name'") == $prev_table_name) {
			$prev_slider_data = ss_get_prev_slider();
			foreach ($prev_slider_data as $prev_slider_row){
				$prev_post_id = $prev_slider_row['id'];
				$prev_date_time = $prev_slider_row['date'];
				if ($prev_post_id) {
					$sql = "INSERT INTO $table_name (post_id,date) VALUES('$prev_post_id','$prev_date_time');";
					$result = $wpdb->query($sql);
				}
			}
		}
	}
	add_column_if_not_exist($table_name, 'slide_order', "ALTER TABLE ".$table_name." ADD slide_order int(5) NOT NULL DEFAULT '0';");

   	$meta_table_name = $table_prefix.SLIDER_META;
	if($wpdb->get_var("show tables like '$meta_table_name'") != $meta_table_name) {
		$sql = "CREATE TABLE $meta_table_name (
					slider_id int(5) NOT NULL AUTO_INCREMENT,
					slider_name varchar(100) NOT NULL default '',
					UNIQUE KEY slider_id(slider_id)
				);";
		$rs2 = $wpdb->query($sql);
		
		$sql = "INSERT INTO $meta_table_name (slider_id,slider_name) VALUES('1','Smooth Slider');";
		$rs3 = $wpdb->query($sql);
	}
	
	$slider_postmeta = $table_prefix.SLIDER_POST_META;
	if($wpdb->get_var("show tables like '$slider_postmeta'") != $slider_postmeta) {
		$sql = "CREATE TABLE $slider_postmeta (
					post_id int(11) NOT NULL,
					slider_id int(5) NOT NULL default '1',
					UNIQUE KEY post_id(post_id)
				);";
		$rs4 = $wpdb->query($sql);
	}
   // Need to delete the previously created options in old versions and create only one option field for Smooth Slider
   $default_slider = array();
   $default_slider = array('speed'=>'7', 
	                       'no_posts'=>'5', 
						   'bg_color'=>'#ffffff', 
						   'height'=>'200',
						   'width'=>'450',
						   'border'=>'1',
						   'brcolor'=>'#999999',
						   'prev_next'=>'0',
						   'goto_slide'=>'1',
						   'title_text'=>'Featured Posts',
						   'title_font'=>'Georgia',
						   'title_fsize'=>'20',
						   'title_fstyle'=>'bold',
						   'title_fcolor'=>'#000000',
						   'ptitle_font'=>'Trebuchet MS',
						   'ptitle_fsize'=>'14',
						   'ptitle_fstyle'=>'bold',
						   'ptitle_fcolor'=>'#000000',
						   'img_align'=>'left',
						   'img_height'=>'120',
						   'img_width'=>'165',
						   'img_border'=>'1',
						   'img_brcolor'=>'#000000',
						   'content_font'=>'Verdana',
						   'content_fsize'=>'12',
						   'content_fstyle'=>'normal',
						   'content_fcolor'=>'#333333',
						   'content_from'=>'content',
						   'content_chars'=>'300',
						   'bg'=>'0',
						   'image_only'=>'0',
						   'allowable_tags'=>'',
						   'more'=>'Read More',
						   'img_size'=>'1',
						   'img_pick'=>array('1','slider_thumbnail','1','1','1','1'), //use custom field/key, name of the key, use post featured image, pick the image attachment, attachment order,scan images
						   'user_level'=>'edit_others_posts',
						   'custom_nav'=>'',
						   'crop'=>'0',
						   'transition'=>'5',
						   'autostep'=>'1',
						   'multiple_sliders'=>'0',
						   'navimg_w'=>'32',
						   'navimg_ht'=>'32',
						   'content_limit'=>'50',
						   'stylesheet'=>'default.css',
						   'shortcode'=>'1'
			              );
   
	   $smooth_slider = get_option('smooth_slider_options');
	   $img_pick = $smooth_slider['img_pick'];
  
       if(is_array($img_pick)) {
	   //if(isset($smooth_slider['img_pick'][1])) {
	    $cskey = $smooth_slider['img_pick'][1];
	   }
	   else{
	    $cskey = 'slider_thumbnail';
	   }
      
	   if(!is_array($img_pick)) {
	   //if(!isset($smooth_slider['img_pick'][0])) {
		   if($smooth_slider['img_pick'] == '1') {
			  $smooth_slider['img_pick'] = array('0',$cskey,'0','0','1','1');
		   }
		   elseif($smooth_slider['img_pick'] == '0'){
			  $smooth_slider['img_pick'] = array('1',$cskey,'0','0','1','0');
		   }
		   else {
			  $smooth_slider['img_pick'] = array('1',$cskey,'1','1','1','1');
		   }
	   }
	   
/*	    if(is_array($img_pick) and (count($img_pick)<6 or count($img_pick)>6)) {
		  $smooth_slider['img_pick'] = array('1',$cskey,'1','1','1','1');
		}
*/	   
	   if(!$smooth_slider) {
	     $smooth_slider = array();
	   }
	   foreach($default_slider as $key=>$value) {
	      if(!isset($smooth_slider[$key])) {
		     $smooth_slider[$key] = $value;
		  }
	   }
     
	 if($smooth_slider['user_level']<=10 and $smooth_slider['user_level'] >=1) {
		 if($smooth_slider['user_level']<=10 and $smooth_slider['user_level'] >7) {
		  $smooth_slider['user_level']='manage_options';
		 }
		 elseif($smooth_slider['user_level']<=7 and $smooth_slider['user_level'] >2){
		  $smooth_slider['user_level']='edit_others_posts';
		 } 
		  elseif($smooth_slider['user_level']==2){
		  $smooth_slider['user_level']='publish_posts';
		 } 
		 else {
		  $smooth_slider['user_level']='edit_posts';
		 }
	 }
	  
	   delete_option('smooth_slider_options');	  
	   update_option('smooth_slider_options',$smooth_slider);
	
	 delete_option('smooth_slider_speed');
	 delete_option('smooth_slider_no_posts');
	 delete_option('smooth_slider_bg_color');
	 delete_option('smooth_slider_height');
	 delete_option('smooth_slider_width');
	 delete_option('smooth_slider_border');
	 delete_option('smooth_slider_brcolor');
	 delete_option('smooth_slider_prev_next');
	 delete_option('smooth_slider_goto_slide');
	 delete_option('smooth_slider_title_text');
	 delete_option('smooth_slider_title_font');
	 delete_option('smooth_slider_title_fsize');
	 delete_option('smooth_slider_title_fstyle');
	 delete_option('smooth_slider_title_fcolor');
	 delete_option('smooth_slider_ptitle_font');
	 delete_option('smooth_slider_ptitle_fsize');
	 delete_option('smooth_slider_ptitle_fstyle');
	 delete_option('smooth_slider_ptitle_fcolor');
	 delete_option('smooth_slider_img_align');
	 delete_option('smooth_slider_img_height');
	 delete_option('smooth_slider_img_width');
	 delete_option('smooth_slider_img_border');
	 delete_option('smooth_slider_img_brcolor');
	 delete_option('smooth_slider_content_font');
	 delete_option('smooth_slider_content_fsize');
	 delete_option('smooth_slider_content_fstyle');
	 delete_option('smooth_slider_content_fcolor');
	 delete_option('smooth_slider_content_from');	
	 delete_option('smooth_slider_content_chars');
	 delete_option('smooth_slider_bg');	
	 delete_option('smooth_slider_clear');	
	 delete_option('smooth_slider_image_only');	
}
register_activation_hook( __FILE__, 'install_smooth_slider' );
//defined global variables and constants here
global $smooth_slider;
$smooth_slider = get_option('smooth_slider_options');
require_once (dirname (__FILE__) . '/includes/smooth-slider-functions.php');
require_once (dirname (__FILE__) . '/includes/sslider-get-the-image-functions.php');

//This adds the post to the slider
function add_to_slider($post_id) {
global $smooth_slider;
 if(isset($_POST['sldr-verify']) and current_user_can( $smooth_slider['user_level'] ) ) {
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.SLIDER_TABLE;
	
	if(isset($_POST['slider']) and !isset($_POST['slider_name'])) {
	  $slider_id = '1';
	  if(is_post_on_any_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	  }
	  
	  if(isset($_POST['slider']) and $_POST['slider'] == "slider" and !slider($post_id,$slider_id)) {
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
	  }
	}
	if(isset($_POST['slider']) and $_POST['slider'] == "slider" and isset($_POST['slider_name'])){
	  $slider_id_arr = $_POST['slider_name'];
	  $post_sliders_data = ss_get_post_sliders($post_id);
	  
	  foreach($post_sliders_data as $post_slider_data){
		if(!in_array($post_slider_data['slider_id'],$slider_id_arr)) {
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		  $wpdb->query($sql);
		}
	  }
	    /*if(is_post_on_any_slider($post_id)){
	     $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	    }*/
		foreach($slider_id_arr as $slider_id) {
			if(!slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			}
		}
	}
	
	$table_name = $table_prefix.SLIDER_POST_META;
	if(isset($_POST['display_slider']) and !isset($_POST['display_slider_name'])) {
	  $slider_id = '1';
	}
	if(isset($_POST['display_slider']) and isset($_POST['display_slider_name'])){
	  $slider_id = $_POST['display_slider_name'];
	}
  	if(isset($_POST['display_slider'])){	
		  if(!ss_post_on_slider($post_id,$slider_id)) {
		    $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
			$sql = "INSERT INTO $table_name (post_id, slider_id) VALUES ('$post_id', '$slider_id')";
			$wpdb->query($sql);
		  }
	}
	$slider_style = get_post_meta($post_id,'slider_style',true);
	if($slider_style != $_POST['slider_style']) {
	  update_post_meta($post_id, 'slider_style', $_POST['slider_style']);	
	}
	
	$thumbnail_key = $smooth_slider['img_pick'][1];
	$sslider_thumbnail = get_post_meta($post_id,$thumbnail_key,true);
	if($sslider_thumbnail != $_POST['sslider_thumbnail']) {
	  update_post_meta($post_id, $thumbnail_key, $_POST['sslider_thumbnail']);	
	}
	
	$sslider_link = get_post_meta($post_id,'slide_redirect_url',true);
	$link=$_POST['sslider_link'];
	if(!isset($link) or empty($link)){$link=get_permalink($post_id);}
	if($sslider_link != $link) {
	  update_post_meta($post_id, 'slide_redirect_url', $link);	
	}
	
	$sslider_nolink = get_post_meta($post_id,'sslider_nolink',true);
	if($sslider_nolink != $_POST['sslider_nolink']) {
	  update_post_meta($post_id, 'sslider_nolink', $_POST['sslider_nolink']);	
	}
	
  } //sldr_verify
}

//Removes the post from the slider, if you uncheck the checkbox from the edit post screen
function remove_from_slider($post_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	
	// authorization
	if (!current_user_can('edit_post', $post_id))
		return $post_id;
	// origination and intention
	if (!wp_verify_nonce($_POST['sldr-verify'], 'SmoothSlider'))
		return $post_id;
	
    if(empty($_POST['slider']) and is_post_on_any_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	
	$display_slider = $_POST['display_slider'];
	$table_name = $table_prefix.SLIDER_POST_META;
	if(empty($display_slider) and ss_slider_on_this_post($post_id)){
	  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
	}
} 
  
function delete_from_slider_table($post_id){
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
    if(is_post_on_any_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	$table_name = $table_prefix.SLIDER_POST_META;
    if(ss_slider_on_this_post($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
}

// Slider checkbox on the admin page

function smooth_slider_edit_custom_box(){
   add_to_slider_checkbox();
}
/* Prints the edit form for pre-WordPress 2.5 post/page */
function smooth_slider_old_custom_box() {

  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="myplugin_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'Smooth Slider', 'smooth-slider' ) . "</h3></div>";   
   
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  // output editing form

  smooth_slider_edit_custom_box();

  // end wrapper

  echo "</div></div></fieldset></div>\n";
}

function smooth_slider_add_custom_box() {
 global $smooth_slider;
 if (current_user_can( $smooth_slider['user_level'] )) {
	if( function_exists( 'add_meta_box' ) ) {
	    $post_types=get_post_types(); 
		foreach($post_types as $post_type) {
		  add_meta_box( 'sslider_box1', __( 'Smooth Slider' ), 'smooth_slider_edit_custom_box', $post_type, 'advanced' );
		}
		//add_meta_box( $id,   $title,     $callback,   $page, $context, $priority ); 
	} else {
		add_action('dbx_post_advanced', 'smooth_slider_old_custom_box' );
		add_action('dbx_page_advanced', 'smooth_slider_old_custom_box' );
	}
 }
}
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'smooth_slider_add_custom_box');

function add_to_slider_checkbox() {
	global $post, $smooth_slider;
	if (current_user_can( $smooth_slider['user_level'] )) {
		$extra = "";
		
		$post_id = $post->ID;
		
		if(isset($post->ID)) {
			$post_id = $post->ID;
			if(is_post_on_any_slider($post_id)) { $extra = 'checked="checked"'; }
		} 
		
		$post_slider_arr = array();
		
		$post_sliders = ss_get_post_sliders($post_id);
		if($post_sliders) {
			foreach($post_sliders as $post_slider){
			   $post_slider_arr[] = $post_slider['slider_id'];
			}
		}
		
		$sliders = ss_get_sliders();
?>
		<div id="slider_checkbox">
				<input type="checkbox" class="sldr_post" name="slider" value="slider" <?php echo $extra;?> />
				<label for="slider">Add this post/page to </label>
				<select name="slider_name[]" multiple="multiple" size="2" style="height:4em;">
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(in_array($slider['slider_id'],$post_slider_arr)){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select>
                
         <?php if($smooth_slider['multiple_sliders'] == '1') {?>
                <br />
                <br />
                <br />
                
                <input type="checkbox" class="sldr_post" name="display_slider" value="1" <?php if(ss_slider_on_this_post($post_id)){echo "checked";}?> />
				<label for="display_slider">Display 
				<select name="display_slider_name">
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(ss_post_on_slider($post_id,$slider['slider_id'])){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select> on this Post/Page (you need to add the Smooth Slider template tag manually on your page.php/single.php or whatever page template file)</label>
          <?php } ?>
          
				<input type="hidden" name="sldr-verify" id="sldr-verify" value="<?php echo wp_create_nonce('SmoothSlider');?>" />
	    </div>
        <br />
        <div>
        <?php
        $slider_style = get_post_meta($post->ID,'slider_style',true);
        ?>
         <select name="slider_style" >
			<?php 
            $directory = WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/styles/';
            if ($handle = opendir($directory)) {
                while (false !== ($file = readdir($handle))) { 
                 if($file != '.' and $file != '..') { ?>
                  <option value="<?php echo $file;?>" <?php if ($slider_style == $file){ echo "selected";}?> ><?php echo $file;?></option>
             <?php  } }
                closedir($handle);
            }
            ?>
        </select> <label for="slider_style">Stylesheet to use if slider is displayed on this Post/Page </label><br /> <br />
        
  <?php         $thumbnail_key = $smooth_slider['img_pick'][1];
                $sslider_thumbnail= get_post_meta($post_id, $thumbnail_key, true); 
				$sslider_link= get_post_meta($post_id, 'slide_redirect_url', true);  
				$sslider_nolink=get_post_meta($post_id, 'sslider_nolink', true);
  ?>
                <label for="sslider_thumbnail">Custom Thumbnail Image(url)
                <input type="text" name="sslider_thumbnail" class="sslider_thumbnail" value="<?php echo $sslider_thumbnail;?>" size="75" />
                <br /> </label> <br /><br />
                <fieldset>
                <label for="sslider_link">Slide Link URL 
                <input type="text" name="sslider_link" class="sslider_link" value="<?php echo $sslider_link;?>" size="50" /> </label>
                <label for="sslider_nolink"> 
                <input type="checkbox" name="sslider_nolink" class="sslider_nolink" value="1" <?php if($sslider_nolink=='1'){echo "checked";}?>  /> Do not link this slide to any page(url)</label>
                 </fieldset>
                 </div>
        
<?php }
}

//CSS for the checkbox on the admin page
function slider_checkbox_css() {
?><style type="text/css" media="screen">#slider_checkbox{margin: 5px 0 10px 0;padding:3px;font-weight:bold;}#slider_checkbox input,#slider_checkbox select{font-weight:bold;}#slider_checkbox label,#slider_checkbox input,#slider_checkbox select{vertical-align:top;}</style>
<?php
}

add_action('publish_post', 'add_to_slider');
add_action('publish_page', 'add_to_slider');
add_action('edit_post', 'add_to_slider');
add_action('publish_post', 'remove_from_slider');
add_action('edit_post', 'remove_from_slider');
add_action('deleted_post','delete_from_slider_table');

function smooth_slider_plugin_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

function get_string_limit($output, $max_char)
{
    $output = str_replace(']]>', ']]&gt;', $output);
    $output = strip_tags($output);

  	if ((strlen($output)>$max_char) && ($espacio = strpos($output, " ", $max_char )))
	{
        $output = substr($output, 0, $espacio).'...';
		return $output;
   }
   else
   {
      return $output;
   }
}

function smooth_slider_get_first_image($post) {
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];
	return $first_img;
}

function carousel_posts_on_slider($max_posts, $offset=0, $slider_id = '1',$out_echo = '1') {
    global $smooth_slider;
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
	$post_table = $table_prefix."posts";
	
	$posts = $wpdb->get_results("SELECT a.post_id, a.date FROM 
	                             $table_name a LEFT OUTER JOIN $post_table b 
								 ON a.post_id = b.ID 
								 WHERE (b.post_status = 'publish' OR (b.post_type='attachment' AND b.post_status = 'inherit')) AND a.slider_id = '$slider_id' 
	                             ORDER BY a.slide_order ASC, a.date DESC LIMIT $offset, $max_posts", OBJECT);
	
	$html = '';
	$smooth_sldr_j = 0;
	
	foreach($posts as $post) {
		$id = $post->post_id;
		$posts_table = $table_prefix.'posts'; 
		$sql_post = "SELECT * FROM $posts_table WHERE ID = $id";
		$rs_post = $wpdb->get_results("SELECT * FROM $posts_table WHERE ID = $id", OBJECT);
		$data = $rs_post[0];
		
		$post_title = stripslashes($data->post_title);
		$post_title = str_replace('"', '', $post_title);
		$slider_content = $data->post_content;

//2.3 changes		
//		$permalink = get_permalink($data->ID);
		
		$post_id = $data->ID;
		
//2.1 changes start
            $slide_redirect_url = get_post_meta($post_id, 'slide_redirect_url', true);
			$sslider_nolink = get_post_meta($post_id,'sslider_nolink',true);
			trim($slide_redirect_url);
			if(!empty($slide_redirect_url) and isset($slide_redirect_url)) {
			   $permalink = $slide_redirect_url;
			}
			else{
			   $permalink = get_permalink($post_id);
			}
			if($sslider_nolink=='1'){
			  $permalink='';
			}
//2.1 changes end	
	   		$smooth_sldr_j++;
		$html .= '<div class="smooth_slideri">
			<!-- smooth_slideri -->';
			
		$thumbnail = get_post_meta($post_id, $smooth_slider['img_pick'][1], true);
		//$image_control = get_post_meta($post_id, 'slider_image_control', true);
		
		if ($smooth_slider['content_from'] == "slider_content") {
		    $slider_content = get_post_meta($post_id, 'slider_content', true);
		}
		if ($smooth_slider['content_from'] == "excerpt") {
		    $slider_content = $data->post_excerpt;
		}
		
		$slider_content = stripslashes($slider_content);
		$slider_content = str_replace(']]>', ']]&gt;', $slider_content);

		$slider_content = str_replace("\n","<br />",$slider_content);
        $slider_content = strip_tags($slider_content, $smooth_slider['allowable_tags']);
				
		if($smooth_slider['img_pick'][0] == '1'){
		 $custom_key = array($smooth_slider['img_pick'][1]);
		}
		else {
		 $custom_key = '';
		}
		
		if($smooth_slider['img_pick'][2] == '1'){
		 $the_post_thumbnail = true;
		}
		else {
		 $the_post_thumbnail = false;
		}
		
		if($smooth_slider['img_pick'][3] == '1'){
		 $attachment = true;
		 $order_of_image = $smooth_slider['img_pick'][4];
		}
		else{
		 $attachment = false;
		 $order_of_image = '1';
		}
		
		if($smooth_slider['img_pick'][5] == '1'){
			 $image_scan = true;
		}
		else {
			 $image_scan = false;
		}
		
		if($smooth_slider['img_size'] == '1'){
		 $gti_width = $smooth_slider['img_width'];
		}
		else {
		 $gti_width = false;
		}
		
		if($smooth_slider['crop'] == '0'){
		 $extract_size = 'full';
		}
		elseif($smooth_slider['crop'] == '1'){
		 $extract_size = 'large';
		}
		elseif($smooth_slider['crop'] == '2'){
		 $extract_size = 'medium';
		}
		else{
		 $extract_size = 'thumbnail';
		}
		
		$img_args = array(
			'custom_key' => $custom_key,
			'post_id' => $post_id,
			'attachment' => $attachment,
			'size' => $extract_size,
			'the_post_thumbnail' => $the_post_thumbnail,
			'default_image' => false,
			'order_of_image' => $order_of_image,
			'link_to_post' => false,
			'image_class' => 'smooth_slider_thumbnail',
			'image_scan' => $image_scan,
			'width' => $gti_width,
			'height' => false,
			'echo' => false,
			'permalink' => $permalink
		);
			
		$html .=  sslider_get_the_image($img_args);
		
		if(!$smooth_slider['content_limit'] or $smooth_slider['content_limit'] == '' or $smooth_slider['content_limit'] == ' ') 
		  $slider_excerpt = substr($slider_content,0,$smooth_slider['content_chars']);
		else 
		  $slider_excerpt = smooth_slider_word_limiter( $slider_content, $limit = $smooth_slider['content_limit'] );
		  		
		if ($smooth_slider['image_only'] == '1') { 
			$html .= '<!-- /smooth_slideri -->
			</div>';
		}
		else {
		   if($permalink!='') {
			$html .= '<h2 ><a href="'.$permalink.'">'.$post_title.'</a></h2><span> '.$slider_excerpt.'</span>
				<p class="more"><a href="'.$permalink.'">'.$smooth_slider['more'].'</a></p>
			
				<!-- /smooth_slideri -->
			</div>'; }
		   else{
		   $html .= '<h2 >'.$post_title.'</h2><span> '.$slider_excerpt.'</span>
				<!-- /smooth_slideri -->
			</div>';    }
		}
	}
	if($out_echo == '1') {
	   echo $html;
	}
	$r_array = array( $smooth_sldr_j, $html);
	return $r_array;
}

function carousel_posts_on_slider_cat($max_posts, $catg_slug, $offset=0) {
    global $smooth_slider;
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;
    $post_table = $table_prefix."posts";
	
	$myposts = $wpdb->get_results("SELECT a.post_id, a.date FROM 
	                             $table_name a LEFT OUTER JOIN $post_table b 
								 ON a.post_id = b.ID 
								 WHERE b.post_status = 'publish' OR (b.post_type='attachment' AND b.post_status = 'inherit')    
	                             ORDER BY a.slide_order ASC, a.date DESC LIMIT $offset, $max_posts", OBJECT);
	
	$html = '';
	$smooth_sldr_i = 0;
	
	if (!empty($catg_slug)) {
		$category = get_category_by_slug($catg_slug); 
		$slider_cat = $category->term_id;
	}
	else {
		$category = get_the_category();
		$slider_cat = $category[0]->cat_ID;
	}
	
	foreach($myposts as $mypost) {
		$post = get_post($mypost->post_id);
		$post_cats_arr = get_the_category($post->ID);
		
		$post_cats = array();
		foreach($post_cats_arr as $post_cat_arr) {
		  $post_cats[] = $post_cat_arr->cat_ID;
		}
		
    	if ((isset($slider_cat) and in_array($slider_cat,$post_cats)) or (empty($catg_slug) and (is_home() or (is_paged() and !is_category()) or is_tag() or is_author() or (is_archive() and !is_category()))))
		{
			$post_title = stripslashes($post->post_title);
			$post_title = str_replace('"', '', $post_title);
			$slider_content = $post->post_content;
			
//			$permalink = get_permalink($post->ID);
			
			$post_id = $post->ID;
//2.1 changes start
            $slide_redirect_url = get_post_meta($post_id, 'slide_redirect_url', true);
			$sslider_nolink = get_post_meta($post_id,'sslider_nolink',true);
			trim($slide_redirect_url);
			if(!empty($slide_redirect_url) and isset($slide_redirect_url)) {
			   $permalink = $slide_redirect_url;
			}
			else{
			   $permalink = get_permalink($post_id);
			}
			if($sslider_nolink=='1'){
			  $permalink='';
			}

//2.1 changes end	
					 
		    $smooth_sldr_i++;
			
			$html .= '<div class="smooth_slideri">
				<!-- smooth_slideri -->';
				
			$thumbnail = get_post_meta($post_id, 'slider_thumbnail', true);
			$image_control = get_post_meta($post_id, 'slider_image_control', true);
			
			if ($smooth_slider['content_from'] == "slider_content") {
				$slider_content = get_post_meta($post_id, 'slider_content', true);
			}
			if ($smooth_slider['content_from'] == "excerpt") {
				$slider_content = $post->post_excerpt;
			}
			
			$slider_content = stripslashes($slider_content);
			$slider_content = str_replace(']]>', ']]&gt;', $slider_content);
			
			$slider_content = str_replace("\n","<br />",$slider_content);
            $slider_content = strip_tags($slider_content, $smooth_slider['allowable_tags']);
						
			if($smooth_slider['img_pick'][0] == '1'){
			 $custom_key = array($smooth_slider['img_pick'][0]);
			}
			else {
			 $custom_key = '';
			}
			
			if($smooth_slider['img_pick'][2] == '1'){
			 $the_post_thumbnail = true;
			}
			else {
			 $the_post_thumbnail = false;
			}
			
			if($smooth_slider['img_pick'][3] == '1'){
			 $attachment = true;
			 $order_of_image = $smooth_slider['img_pick'][4];
			}
			else{
			 $attachment = false;
			 $order_of_image = '1';
			}
			
			if($smooth_slider['img_pick'][5] == '1'){
			 $image_scan = true;
			}
			else {
			 $image_scan = false;
			}
			
			if($smooth_slider['img_size'] == '1'){
			 $gti_width = false;
			}
			else {
			 $gti_width = $smooth_slider['img_width'];
			}
			
			if($smooth_slider['crop'] == '0'){
			 $extract_size = 'full';
			}
			elseif($smooth_slider['crop'] == '1'){
			 $extract_size = 'large';
			}
			elseif($smooth_slider['crop'] == '2'){
			 $extract_size = 'medium';
			}
			else{
			 $extract_size = 'thumbnail';
			}
			
			$img_args = array(
				'custom_key' => $custom_key,
				'attachment' => $attachment,
				'size' => $extract_size,
				'the_post_thumbnail' => $the_post_thumbnail,
				'default_image' => false,
				'order_of_image' => $order_of_image,
				'link_to_post' => false,
				'image_class' => 'smooth_slider_thumbnail',
				'image_scan' => $image_scan,
				'width' => $gti_width,
				'height' => false,
				'echo' => false,
				'permalink' => $permalink
			);
		
			$html .=  sslider_get_the_image($img_args);
		
		if(!$smooth_slider['content_limit'] or $smooth_slider['content_limit'] == '' or $smooth_slider['content_limit'] == ' ') 
		  $slider_excerpt = substr($slider_content,0,$smooth_slider['content_chars']);
		else 
		  $slider_excerpt = smooth_slider_word_limiter( $slider_content, $limit = $smooth_slider['content_limit'] );
			
			if ($smooth_slider['image_only'] == '1') { 
				$html .= '<!-- /smooth_slideri -->
				</div>';
			}
			else {
	          if($permalink!='') {
				$html .= '<h2 ><a href="'.$permalink.'">'.$post_title.'</a></h2><span> '.$slider_excerpt.'</span>
					<p class="more"><a href="'.$permalink.'">'.$smooth_slider['more'].'</a></p>
				
					<!-- /smooth_slideri -->
				</div>';
			  }
			  else{
			   $html .= '<h2 >'.$post_title.'</h2><span> '.$slider_excerpt.'</span>
					<!-- /smooth_slideri -->
				</div>';
			  }
		    }
	  } 
		if ($smooth_sldr_i >= $max_posts)
		   { break; }
	}
	echo $html;
	return $smooth_sldr_i;
}

function smooth_slider_wpmu_carousel_posts($max_posts, $offset=0) {
    global $smooth_slider;
	global $wpdb, $table_prefix, $blog_id;
	
	$html = '';
	$smooth_sldr_k = 0;
	
	$blogs = $wpdb->get_results( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered ASC", $wpdb->siteid), ARRAY_A );
    foreach($blogs as $details) {
	    
        switch_to_blog($details['blog_id']); 
		global $table_prefix;
		
		$table_name = $table_prefix.SLIDER_TABLE;
		$post_table = $table_prefix."posts";
				
		if(smooth_slider_table_exists($table_name, DB_NAME)){
		
		$myposts = $wpdb->get_results("SELECT a.post_id, a.date FROM 
	                             $table_name a LEFT OUTER JOIN $post_table b 
								 ON a.post_id = b.ID 
								 WHERE b.post_status = 'publish' OR (b.post_type='attachment' AND b.post_status = 'inherit') 
	                             ORDER BY a.slide_order ASC, a.date DESC LIMIT $offset, $max_posts", OBJECT);
		
		foreach($myposts as $mypost) {
			$posts_table = $table_prefix."posts";
			$id = $mypost->post_id;
			$post =  $wpdb->get_row("SELECT * FROM $posts_table WHERE ID = $id", OBJECT);
			
			$post_title = stripslashes($post->post_title);
			$post_title = str_replace('"', '', $post_title);
			$slider_content = $post->post_content;
			
//			$permalink = get_permalink($post->ID);			
			
			$post_id = $post->ID;

//2.1 changes start
            $slide_redirect_url = get_post_meta($post_id, 'slide_redirect_url', true);
			$sslider_nolink = get_post_meta($post_id,'sslider_nolink',true);
			trim($slide_redirect_url);
			if(!empty($slide_redirect_url) and isset($slide_redirect_url)) {
			   $permalink = $slide_redirect_url;
			}
			else{
			   $permalink = get_permalink($post_id);
			}
			if($sslider_nolink=='1'){
			  $permalink='';
			}

//2.1 changes end

		 	$smooth_sldr_k++;
			$html .= '<div class="smooth_slideri">
				<!-- smooth_slideri -->';
				
			$thumbnail = get_post_meta($post_id, 'slider_thumbnail', true);
			$image_control = get_post_meta($post_id, 'slider_image_control', true);
			
			if ($smooth_slider['content_from'] == "slider_content") {
				$slider_content = get_post_meta($post_id, 'slider_content', true);
			}
			if ($smooth_slider['content_from'] == "excerpt") {
				$slider_content = $post->post_excerpt;
			}
			
			$slider_content = stripslashes($slider_content);
			$slider_content = str_replace(']]>', ']]&gt;', $slider_content);
			
			$slider_content = str_replace("\n","<br />",$slider_content);
			$slider_content = strip_tags($slider_content, $smooth_slider['allowable_tags']);
						
			if($smooth_slider['img_pick'][0] == '1'){
			 $custom_key = array($smooth_slider['img_pick'][0]);
			}
			else {
			 $custom_key = '';
			}
			
			if($smooth_slider['img_pick'][2] == '1'){
			 $the_post_thumbnail = true;
			}
			else {
			 $the_post_thumbnail = false;
			}
			
			if($smooth_slider['img_pick'][3] == '1'){
			 $attachment = true;
			 $order_of_image = $smooth_slider['img_pick'][4];
			}
			else{
			 $attachment = false;
			 $order_of_image = '1';
			}
			
			if($smooth_slider['img_pick'][5] == '1'){
			 $image_scan = true;
			}
			else {
			 $image_scan = false;
			}
			
			if($smooth_slider['img_size'] == '1'){
			 $gti_width = false;
			}
			else {
			 $gti_width = $smooth_slider['img_width'];
			}
			
			if($smooth_slider['crop'] == '0'){
			 $extract_size = 'full';
			}
			elseif($smooth_slider['crop'] == '1'){
			 $extract_size = 'large';
			}
			elseif($smooth_slider['crop'] == '2'){
			 $extract_size = 'medium';
			}
			else{
			 $extract_size = 'thumbnail';
			}
			
			$img_args = array(
				'custom_key' => $custom_key,
				'attachment' => $attachment,
				'size' => $extract_size,
				'the_post_thumbnail' => $the_post_thumbnail,
				'default_image' => false,
				'order_of_image' => $order_of_image,
				'link_to_post' => false,
				'image_class' => 'smooth_slider_thumbnail',
				'image_scan' => $image_scan,
				'width' => $gti_width,
				'height' => false,
				'echo' => false,
				'permalink' => $permalink
			);
			
			$html .=  sslider_get_the_image($img_args);
				
		if(!$smooth_slider['content_limit'] or $smooth_slider['content_limit'] == '' or $smooth_slider['content_limit'] == ' ') 
		  $slider_excerpt = substr($slider_content,0,$smooth_slider['content_chars']);
		else 
		  $slider_excerpt = smooth_slider_word_limiter( $slider_content, $limit = $smooth_slider['content_limit'] );
			
			if ($smooth_slider['image_only'] == '1') { 
				$html .= '<!-- /smooth_slideri -->
				</div>';
			}
			else {
  	          if($permalink!='') {
				$html .= '<h2 ><a href="'.$permalink.'">'.$post_title.'</a></h2><span> '.$slider_excerpt.'</span>
					<p class="more"><a href="'.$permalink.'">'.$smooth_slider['more'].'</a></p>
				
					<!-- /smooth_slideri -->
				</div>'; }
			  else{
			    $html .= '<h2 >'.$post_title.'</h2><span> '.$slider_excerpt.'</span>
					<!-- /smooth_slideri -->
				</div>'; } 
			}
		if ($smooth_sldr_k >= $max_posts)
		   { break; }
	  }
		
	  if ($smooth_sldr_k >= $max_posts)
		   { break; }
      }//smooth slider table exists
    }
	restore_current_blog();
	echo $html;
	return $smooth_sldr_k;
}

function smooth_slider_css() {
global $smooth_slider,$post;
if(is_singular()) {
 $slider_style = get_post_meta($post->ID,'slider_style',true);
}
if((is_singular() and $slider_style == 'default.css') 
or (!is_singular() and $smooth_slider['stylesheet'] == 'default.css') 
or  (is_singular() and is_active_widget(false, false, 'sslider_wid', true) and (!isset($slider_style) or empty($slider_style) )) 
or (is_singular() and isset($smooth_slider['shortcode']) ) )
{
?>
<style type="text/css" media="screen">#smooth_sldr{width:<?php echo $smooth_slider['width']; ?>px;height:<?php echo $smooth_slider['height']; ?>px;background-color:<?php if ($smooth_slider['bg'] == '1') { echo "transparent";} else { echo $smooth_slider['bg_color']; } ?>;border:<?php echo $smooth_slider['border']; ?>px solid <?php echo $smooth_slider['brcolor']; ?>;}#smooth_sldr_items{padding:10px <?php if ($smooth_slider['prev_next'] == 1) {echo "18";} else {echo "12";} ?>px 0px <?php if ($smooth_slider['prev_next'] == 1) {echo "26";} else {echo "12";} ?>px;}#smooth_sliderc{width:<?php if ($smooth_slider['prev_next'] == 1) {echo ($smooth_slider['width'] - 44);} else {echo ($smooth_slider['width'] - 24);} ?>px;height:<?php if ($smooth_slider['goto_slide'] == "1"){$nav_size = $smooth_slider['content_fsize'];} elseif ($smooth_slider['goto_slide'] == "2"){$nav_size = $smooth_slider['navimg_ht'];} else {$nav_size = 10;} $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $nav_size + 5 + 18; } else { $extra_height = $nav_size + 5 + 5 + 18;  } echo ($smooth_slider['height'] - $extra_height); ?>px;}.smooth_slideri{width:<?php if ($smooth_slider['prev_next'] == 1) {echo ($smooth_slider['width'] - 54);} else {echo ($smooth_slider['width'] - 24);} ?>px;height:<?php if ($smooth_slider['goto_slide'] == "1"){$nav_size = $smooth_slider['content_fsize'];} elseif ($smooth_slider['goto_slide'] == "2"){$nav_size = $smooth_slider['navimg_ht'];} else {$nav_size = 10;} $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $nav_size + 5 + 18; } else { $extra_height = $nav_size + 5 + 5 + 18;  } echo ($smooth_slider['height'] - $extra_height); ?>px;}.sldr_title{font-family:<?php echo $smooth_slider['title_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['title_fsize']; ?>px;font-weight:<?php if ($smooth_slider['title_fstyle'] == "bold" or $smooth_slider['title_fstyle'] == "bold italic" ){echo "bold";} else { echo "normal"; } ?>;font-style:<?php if ($smooth_slider['title_fstyle'] == "italic" or $smooth_slider['title_fstyle'] == "bold italic" ){echo "italic";} else {echo "normal";} ?>;color:<?php echo $smooth_slider['title_fcolor']; ?>;}#smooth_sldr_body h2{line-height:<?php echo ($smooth_slider['ptitle_fsize'] + 3); ?>px;font-family:<?php echo $smooth_slider['ptitle_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['ptitle_fsize']; ?>px;font-weight:<?php if ($smooth_slider['ptitle_fstyle'] == "bold" or $smooth_slider['ptitle_fstyle'] == "bold italic" ){echo "bold";} else {echo "normal";} ?>;font-style:<?php if ($smooth_slider['ptitle_fstyle'] == "italic" or $smooth_slider['ptitle_fstyle'] == "bold italic"){echo "italic";} else {echo "normal";} ?>;color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;margin:<?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { echo "10"; } else {echo "0";} ?>px 0 5px 0;}#smooth_sldr_body h2 a{color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;}#smooth_sldr_body span{font-family:<?php echo $smooth_slider['content_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['content_fsize']; ?>px;font-weight:<?php if ($smooth_slider['content_fstyle'] == "bold" or $smooth_slider['content_fstyle'] == "bold italic" ){echo "bold";} else {echo "normal";} ?>;font-style:<?php if ($smooth_slider['content_fstyle']=="italic" or $smooth_slider['content_fstyle'] == "bold italic"){echo "italic";} else {echo "normal";} ?>;color:<?php echo $smooth_slider['content_fcolor']; ?>;}.smooth_slider_thumbnail{float:<?php echo $smooth_slider['img_align']; ?>;margin:<?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { echo "10"; } else {echo "0";} ?>px <?php if($smooth_slider['img_align'] == "left") {echo "5";} else {echo "0";} ?>px 0 <?php if($smooth_slider['img_align'] == "right") {echo "5";} else {echo "0";} ?>px;max-height:<?php echo $smooth_slider['img_height']; ?>px;border:<?php echo $smooth_slider['img_border']; ?>px solid <?php echo $smooth_slider['img_brcolor']; ?>;}#smooth_sldr_body p.more a{color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;font-family:<?php echo $smooth_slider['content_font']; ?>, Arial, Helvetica, sans-serif;font-size:<?php echo $smooth_slider['content_fsize']; ?>px;}#smooth_sliderc_nav li{border:1px solid <?php echo $smooth_slider['content_fcolor']; ?>;font-size:<?php echo $smooth_slider['content_fsize']; ?>px;font-family:<?php echo $smooth_slider['content_font']; ?>, Arial, Helvetica, sans-serif;}#smooth_sliderc_nav li a{color:<?php echo $smooth_slider['ptitle_fcolor']; ?>;}.sldrlink{padding-right:<?php if ($smooth_slider['prev_next'] == 1) {echo "40";} else {echo "25";} ?>px;}.sldrlink a{color:<?php echo $smooth_slider['content_fcolor']; ?>;}</style>
<?php  }
}

add_action('wp_head', 'smooth_slider_css');

function smooth_slider_enqueue_scripts() {
//	wp_register_script('jquery', false, false, false, false);
	wp_enqueue_script( 'stepcarousel', smooth_slider_plugin_url( 'js/stepcarousel.js' ),
		array('jquery'), SMOOTH_SLIDER_VER, false); 
}

add_action( 'init', 'smooth_slider_enqueue_scripts' );

function smooth_slider_enqueue_styles() {	
  global $post, $smooth_slider, $wp_registered_widgets,$wp_widget_factory;
  if(is_singular()) {
	 $slider_style = get_post_meta($post->ID,'slider_style',true);
	 if((is_active_widget(false, false, 'sslider_wid', true) or isset($smooth_slider['shortcode']) ) and (!isset($slider_style) or empty($slider_style))){
	   $slider_style='default.css';
	 }
	 if (!isset($slider_style) or empty($slider_style) ) {
	     wp_enqueue_style( 'smooth_slider_head_css', smooth_slider_plugin_url( 'css/styles/'.$smooth_slider['stylesheet'] ),
		false, SMOOTH_SLIDER_VER, 'all');
	 }
     else {
	     wp_enqueue_style( 'smooth_slider_head_css', smooth_slider_plugin_url( 'css/styles/'.$slider_style ),
		false, SMOOTH_SLIDER_VER, 'all');
	}
  }
  else {
     $slider_style = $smooth_slider['stylesheet'];
     wp_enqueue_style( 'smooth_slider_head_css', smooth_slider_plugin_url( 'css/styles/'.$slider_style ),
		false, SMOOTH_SLIDER_VER, 'all'); 
  }
}
add_action( 'wp', 'smooth_slider_enqueue_styles' );

function get_smooth_slider($slider_id='') {
 global $smooth_slider; 
 
 if($smooth_slider['multiple_sliders'] == '1' and is_singular() and (empty($slider_id) or !isset($slider_id))){
    global $post;
	$post_id = $post->ID;
    $slider_id = get_slider_for_the_post($post_id);
 }
if(!is_singular() or ($smooth_slider['multiple_sliders'] != '1' and (empty($slider_id) or !isset($slider_id)))){
  $slider_id = '1';
}
if(!empty($slider_id)){
?>
	<script type="text/javascript">
	stepcarousel.setup({
		galleryid: 'smooth_sliderc', //id of carousel DIV
		beltclass: 'smooth_sliderb', //class of inner "belt" DIV containing all the panel DIVs
		panelclass: 'smooth_slideri', //class of panel DIVs each holding content
		autostep: {<?php if ($smooth_slider['autostep'] == '1'){ echo "enable: true";} else {echo "enable: false";}?>, moveby:1, pause:<?php echo $smooth_slider['speed']*1000; ?>},
		panelbehavior: {speed:<?php echo $smooth_slider['transition']*100; ?>, wraparound: true, wrapbehavior: 'slide', persist:false},
		defaultbuttons: {enable: <?php if ($smooth_slider['prev_next'] == 1) {echo "true";} else {echo "false";} ?>, moveby: 1, leftnav: ['<?php echo smooth_slider_plugin_url( 'images/button_prev.png' ); ?>', -25, <?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $smooth_slider['content_fsize'] + 5 + 18; } else { $extra_height = $smooth_slider['content_fsize'] + 5 + 5 + 18;  } echo (($smooth_slider['height'] - $extra_height)/2); ?>], rightnav: ['<?php echo smooth_slider_plugin_url( 'images/button_next.png' ); ?>', 0, <?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $smooth_slider['content_fsize'] + 5 + 18; } else { $extra_height = $smooth_slider['content_fsize'] + 5 + 5 + 18;  } echo (($smooth_slider['height'] - $extra_height)/2); ?>]},
		statusvars: ['imageA', 'imageB', 'imageC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
		contenttype: ['inline'], //content setting ['inline'] or ['external', 'path_to_external_file']
		onslide:function(){
		  jQuery("#smooth_sliderc_nav li a").css("fontWeight", "normal");
		  jQuery("#smooth_sliderc_nav li a").css("fontSize", "<?php echo $smooth_slider['content_fsize']; ?>px");
		  var curr_slide = imageA;
		  jQuery("#sldr"+curr_slide).css("fontWeight", "bolder");
		  jQuery("#sldr"+curr_slide).css("fontSize", "<?php echo ($smooth_slider['content_fsize'] + 5); ?>px");
		  
		  <?php if ($smooth_slider['goto_slide'] == 2) { 
					
					global $sldr_nav_width;
					$sldr_nav_width = $smooth_slider['navimg_w'];
		  ?>
		  var nav_width = <?php global $sldr_nav_width; echo $sldr_nav_width; ?>;
		  jQuery("#smooth_sliderc_nav a").css("backgroundPosition", "0 0");
		  jQuery("#sldr"+curr_slide).css("backgroundPosition", "-"+nav_width+"px 0");
		  <?php } ?>
	  }
	})
	</script>
	<noscript><strong>This page is having a slideshow that uses Javascript. Your browser either doesn't support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.</strong></noscript>
			<div id="smooth_sldr">
			<div id="smooth_sldr_items">
				<div id="smooth_sldr_body">
					<?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { ?><div class="sldr_title"><?php echo $smooth_slider['title_text']; ?></div> <?php } ?>
					<div id="smooth_sliderc">
						<div class="smooth_sliderb">
						<?php global $smooth_sldr_j; $r_array = carousel_posts_on_slider($smooth_slider['no_posts'], $offset=0, $slider_id, '0'); $smooth_sldr_j = $r_array[0]; echo $r_array[1];?>
						</div>
					</div>
				</div>
				<?php if ($smooth_slider['goto_slide'] == 1) { ?>
				<ul id="smooth_sliderc_nav">
					<?php global $smooth_sldr_j; for($i=1; $i<=$smooth_sldr_j; $i++) { 
					echo "<li><a id=\"sldr".$i."\" href=\"javascript:stepcarousel.stepTo('smooth_sliderc', ".$i.")\" >".$i."</a></li>\n";
					 } ?>
				</ul>
				<?php } 
				if ($smooth_slider['goto_slide'] == 2) { ?>
				<div id="smooth_sliderc_nav">
					<?php global $smooth_sldr_j; for($i=1; $i<=$smooth_sldr_j; $i++) { 
					
					$width = $smooth_slider['navimg_w'];
					echo "<a class=\"smooth_sliderc_nav\" id=\"sldr".$i."\" style=\"background-image:url(".smooth_slider_plugin_url( 'images/' )."slide".$i.".png);background-position:0 0;width:".$width."px;height:".$smooth_slider['navimg_ht']."px;\" href=\"javascript:stepcarousel.stepTo('smooth_sliderc', ".$i.")\" ></a>\n";
					 } ?>
				  </div>
		  <?php }  
				 if ($smooth_slider['goto_slide'] == 3) { ?>	 
				 <div id="smooth_sliderc_nav"><li style="border:none;"><?php echo $smooth_slider['custom_nav']; ?></li></div>
		  <?php } ?>
				<div class="sldrlink"><a href="http://www.clickonf5.org/smooth-slider" target="_blank">Smooth Slider</a></div>
                <div class="sldr_clearlt"></div><div class="sldr_clearrt"></div>
			</div>
		</div>
<?php	
  } //end of not empty slider_id condition
}

//Smooth Slider template tag to get the Category specific posts in the slider.
function get_smooth_slider_cat($catg_slug) {
 global $smooth_slider; 
?>
<script type="text/javascript">
stepcarousel.setup({
	galleryid: 'smooth_sliderc', //id of carousel DIV
	beltclass: 'smooth_sliderb', //class of inner "belt" DIV containing all the panel DIVs
	panelclass: 'smooth_slideri', //class of panel DIVs each holding content
	autostep: {<?php if ($smooth_slider['autostep'] == '1'){ echo "enable: true";} else {echo "enable: false";}?>, moveby:1, pause:<?php echo $smooth_slider['speed']*1000; ?>},
	panelbehavior: {speed:<?php echo $smooth_slider['transition']*100; ?>, wraparound: true, wrapbehavior: 'slide', persist:false},
	defaultbuttons: {enable: <?php if ($smooth_slider['prev_next'] == 1) {echo "true";} else {echo "false";} ?>, moveby: 1, leftnav: ['<?php echo smooth_slider_plugin_url( 'images/button_prev.png' ); ?>', -25, <?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $smooth_slider['content_fsize'] + 5 + 18; } else { $extra_height = $smooth_slider['content_fsize'] + 5 + 5 + 18;  } echo (($smooth_slider['height'] - $extra_height)/2); ?>], rightnav: ['<?php echo smooth_slider_plugin_url( 'images/button_next.png' ); ?>', 0, <?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $smooth_slider['content_fsize'] + 5 + 18; } else { $extra_height = $smooth_slider['content_fsize'] + 5 + 5 + 18;  } echo (($smooth_slider['height'] - $extra_height)/2); ?>]},
	statusvars: ['imageA', 'imageB', 'imageC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
	contenttype: ['inline'], //content setting ['inline'] or ['external', 'path_to_external_file']
	onslide:function(){
	  jQuery("#smooth_sliderc_nav li a").css("fontWeight", "normal");
	  jQuery("#smooth_sliderc_nav li a").css("fontSize", "<?php echo $smooth_slider['content_fsize']; ?>px");
	  var curr_slide = imageA;
  	  jQuery("#sldr"+curr_slide).css("fontWeight", "bolder");
	  jQuery("#sldr"+curr_slide).css("fontSize", "<?php echo ($smooth_slider['content_fsize'] + 5); ?>px");
	  
	  <?php if ($smooth_slider['goto_slide'] == 2) { 
 				
				global $sldr_nav_width;
				$sldr_nav_width = $smooth_slider['navimg_w'];
	  ?>
	  var nav_width = <?php global $sldr_nav_width; echo $sldr_nav_width; ?>;
	  jQuery("#smooth_sliderc_nav a").css("backgroundPosition", "0 0");
	  jQuery("#sldr"+curr_slide).css("backgroundPosition", "-"+nav_width+"px 0");
	  <?php } ?>
	  
  }
})
</script>
<noscript><strong>This page is having a slideshow that uses Javascript. Your browser either doesn't support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.</strong></noscript>
    	<div id="smooth_sldr">
		<div id="smooth_sldr_items">
			<div id="smooth_sldr_body">
				<?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { ?><div class="sldr_title"><?php echo $smooth_slider['title_text']; ?></div> <?php } ?>
				<div id="smooth_sliderc">
					<div class="smooth_sliderb">
					<?php global $smooth_sldr_i; $smooth_sldr_i = carousel_posts_on_slider_cat($smooth_slider['no_posts'], $catg_slug); ?>
					</div>
				</div>
			</div>
            <?php if ($smooth_slider['goto_slide'] == 1) { ?>
            <ul id="smooth_sliderc_nav">
                <?php global $smooth_sldr_i; for($i=1; $i<=$smooth_sldr_i; $i++) { 
				echo "<li><a id=\"sldr".$i."\" href=\"javascript:stepcarousel.stepTo('smooth_sliderc', ".$i.")\" >".$i."</a></li>\n";
                 } ?>
			</ul>
            <?php } 
			if ($smooth_slider['goto_slide'] == 2) { ?>
            <div id="smooth_sliderc_nav">
                <?php global $smooth_sldr_i; for($i=1; $i<=$smooth_sldr_i; $i++) { 

				$width = $smooth_slider['navimg_w'];
				echo "<a class=\"smooth_sliderc_nav\" id=\"sldr".$i."\" style=\"background-image:url(".smooth_slider_plugin_url( 'images/' )."slide".$i.".png);background-position:0 0;width:".$width."px;height:".$smooth_slider['navimg_ht']."px;\" href=\"javascript:stepcarousel.stepTo('smooth_sliderc', ".$i.")\" ></a>\n";
                 } ?>
			</div>
       <?php }  
			 if ($smooth_slider['goto_slide'] == 3) { ?>	 
             <div id="smooth_sliderc_nav"><li style="border:none;"><?php echo $smooth_slider['custom_nav']; ?></li></div>
      <?php } ?>
            <div class="sldrlink"><a href="http://www.clickonf5.org/smooth-slider" target="_blank">Smooth Slider</a></div>
		</div>
	</div>
<?php	
}

//Smooth Slider especially for WPMU sites, to get the slider posts on the overall WPMU site
function get_smooth_slider_wpmu_all() {
 global $smooth_slider; 
?>
<script type="text/javascript">
stepcarousel.setup({
	galleryid: 'smooth_sliderc', //id of carousel DIV
	beltclass: 'smooth_sliderb', //class of inner "belt" DIV containing all the panel DIVs
	panelclass: 'smooth_slideri', //class of panel DIVs each holding content
	autostep: {<?php if ($smooth_slider['autostep'] == '1'){ echo "enable: true";} else {echo "enable: false";}?>, moveby:1, pause:<?php echo $smooth_slider['speed']*1000; ?>},
	panelbehavior: {speed:<?php echo $smooth_slider['transition']*100; ?>, wraparound: true, wrapbehavior: 'slide', persist:false},
	defaultbuttons: {enable: <?php if ($smooth_slider['prev_next'] == 1) {echo "true";} else {echo "false";} ?>, moveby: 1, leftnav: ['<?php echo smooth_slider_plugin_url( 'images/button_prev.png' ); ?>', -25, <?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $smooth_slider['content_fsize'] + 5 + 18; } else { $extra_height = $smooth_slider['content_fsize'] + 5 + 5 + 18;  } echo (($smooth_slider['height'] - $extra_height)/2); ?>], rightnav: ['<?php echo smooth_slider_plugin_url( 'images/button_next.png' ); ?>', 0, <?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $smooth_slider['content_fsize'] + 5 + 18; } else { $extra_height = $smooth_slider['content_fsize'] + 5 + 5 + 18;  } echo (($smooth_slider['height'] - $extra_height)/2); ?>]},
	statusvars: ['imageA', 'imageB', 'imageC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
	contenttype: ['inline'], //content setting ['inline'] or ['external', 'path_to_external_file']
	onslide:function(){
	  jQuery("#smooth_sliderc_nav li a").css("fontWeight", "normal");
	  jQuery("#smooth_sliderc_nav li a").css("fontSize", "<?php echo $smooth_slider['content_fsize']; ?>px");
	  var curr_slide = imageA;
  	  jQuery("#sldr"+curr_slide).css("fontWeight", "bolder");
	  jQuery("#sldr"+curr_slide).css("fontSize", "<?php echo ($smooth_slider['content_fsize'] + 5); ?>px");
	  
	  <?php if ($smooth_slider['goto_slide'] == 2) { 
				global $sldr_nav_width;
				$sldr_nav_width = $smooth_slider['navimg_w'];
	  ?>
	  var nav_width = <?php global $sldr_nav_width; echo $sldr_nav_width; ?>;
	  jQuery("#smooth_sliderc_nav a").css("backgroundPosition", "0 0");
	  jQuery("#sldr"+curr_slide).css("backgroundPosition", "-"+nav_width+"px 0");
	  <?php } ?>
	  
  }
})
</script>
<noscript><strong>This page is having a slideshow that uses Javascript. Your browser either doesn't support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.</strong></noscript>
    	<div id="smooth_sldr">
		<div id="smooth_sldr_items">
			<div id="smooth_sldr_body">
				<?php $sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { ?><div class="sldr_title"><?php echo $smooth_slider['title_text']; ?></div> <?php } ?>
				<div id="smooth_sliderc">
					<div class="smooth_sliderb">
					<?php global $smooth_sldr_k; $smooth_sldr_k = smooth_slider_wpmu_carousel_posts($smooth_slider['no_posts']); ?>
					</div>
				</div>
			</div>
            <?php if ($smooth_slider['goto_slide'] == 1) { ?>
            <ul id="smooth_sliderc_nav">
                <?php global $smooth_sldr_k; for($i=1; $i<=$smooth_sldr_k; $i++) { 
				echo "<li><a id=\"sldr".$i."\" href=\"javascript:stepcarousel.stepTo('smooth_sliderc', ".$i.")\" >".$i."</a></li>\n";
                 } ?>
			</ul>
            <?php } 
			if ($smooth_slider['goto_slide'] == 2) { ?>
            <div id="smooth_sliderc_nav">
                <?php global $smooth_sldr_k; for($i=1; $i<=$smooth_sldr_k; $i++) { 
				$width = $smooth_slider['navimg_w'];
				echo "<a class=\"smooth_sliderc_nav\" id=\"sldr".$i."\" style=\"background-image:url(".smooth_slider_plugin_url( 'images/' )."slide".$i.".png);background-position:0 0;width:".$width."px;height:".$smooth_slider['navimg_ht']."px;\" href=\"javascript:stepcarousel.stepTo('smooth_sliderc', ".$i.")\" ></a>\n";
                 } ?>
			</div>
       <?php }  
			 if ($smooth_slider['goto_slide'] == 3) { ?>	 
             <div id="smooth_sliderc_nav"><li style="border:none;"><?php echo $smooth_slider['custom_nav']; ?></li></div>
      <?php } ?>
            <div class="sldrlink"><a href="http://www.clickonf5.org/smooth-slider" target="_blank">Smooth Slider</a></div>
		</div>
	</div>
<?php	
}

add_filter( 'plugin_action_links', 'sslider_plugin_action_links', 10, 2 );

function sslider_plugin_action_links( $links, $file ) {
	if ( $file != SMOOTH_SLIDER_PLUGIN_BASENAME )
		return $links;

	$url = sslider_admin_url( array( 'page' => 'smooth-slider-settings' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">'
		. esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}
class Smooth_Slider_Simple_Widget extends WP_Widget {
	function Smooth_Slider_Simple_Widget() {
		$widget_options = array('classname' => 'sslider_wclass', 'description' => 'Insert Smooth Slider' );
		$this->WP_Widget('sslider_wid', 'Smooth Slider - Simple', $widget_options);
	}

	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
	    global $smooth_slider;
		
		echo $before_widget;
		if($smooth_slider['multiple_sliders'] == '1') {
		$slider_id = empty($instance['slider_id']) ? '1' : apply_filters('widget_slider_id', $instance['slider_id']);
		}
		else{
		 $slider_id = '1';
		}

		echo $before_title . $after_title; 
		 get_smooth_slider($slider_id);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
	    global $smooth_slider;
		$instance = $old_instance;
		if($smooth_slider['multiple_sliders'] == '1') {
		   $instance['slider_id'] = strip_tags($new_instance['slider_id']);
		}

		return $instance;
	}

	function form($instance) {
	    global $smooth_slider;
		if($smooth_slider['multiple_sliders'] == '1') {
			$instance = wp_parse_args( (array) $instance, array( 'slider_id' => '' ) );
			$slider_id = strip_tags($instance['slider_id']);
			$sliders = ss_get_sliders();
			$sname_html='<option value="0" selected >Select the Slider</option>';
	 
		  foreach ($sliders as $slider) { 
			 if($slider['slider_id']==$slider_id){$selected = 'selected';} else{$selected='';}
			 $sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';
		  } 
	?>
				<p><label for="<?php echo $this->get_field_id('slider_id'); ?>">Select Slider Name: <select class="widefat" id="<?php echo $this->get_field_id('slider_id'); ?>" name="<?php echo $this->get_field_name('slider_id'); ?>"><?php echo $sname_html;?></select></label></p>
<?php  }
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("Smooth_Slider_Simple_Widget");') );

function return_smooth_slider($slider_id='') {
 global $smooth_slider; 
 
 if($smooth_slider['multiple_sliders'] == '1' and is_singular() and (empty($slider_id) or !isset($slider_id))){
    global $post;
	$post_id = $post->ID;
    $slider_id = get_slider_for_the_post($post_id);
 }
if($smooth_slider['multiple_sliders'] != '1' and (empty($slider_id) or !isset($slider_id))){
  $slider_id = '1';
}
$slider_html='';
if(!empty($slider_id)){
	if ($smooth_slider['autostep'] == '1'){ $autostep = "enable: true";} else {$autostep = "enable: false";}
	if ($smooth_slider['prev_next'] == 1) {$defaultbuttons = "true";} else {$defaultbuttons = "false";} 
	$sldr_title = $smooth_slider['title_text']; if(!empty($sldr_title)) { $extra_height = $smooth_slider['title_fsize'] + $smooth_slider['content_fsize'] + 5 + 18; } else { $extra_height = $smooth_slider['content_fsize'] + 5 + 5 + 18;  } 
	$nav_ht = (($smooth_slider['height'] - $extra_height)/2); 
	$fontSize = $smooth_slider['content_fsize'] + 5;
	
	$slider_html=$slider_html.'<script type="text/javascript">
	stepcarousel.setup({
		galleryid: "smooth_sliderc", //id of carousel DIV
		beltclass: "smooth_sliderb", //class of inner "belt" DIV containing all the panel DIVs
		panelclass: "smooth_slideri", //class of panel DIVs each holding content
		autostep: {'.$autostep.', moveby:1, pause:'. $smooth_slider['speed']*1000 .'},
		panelbehavior: {speed:'. $smooth_slider['transition']*100 .', wraparound: true, wrapbehavior: "slide", persist:false},
		defaultbuttons: {enable: '.$defaultbuttons.', moveby: 1, leftnav: ["'. smooth_slider_plugin_url( 'images/button_prev.png' ) .'", -25, '.$nav_ht.'], rightnav: ["'. smooth_slider_plugin_url( 'images/button_next.png' ).'", 0, '.$nav_ht.']},
		statusvars: ["imageA", "imageB", "imageC"], //register 3 variables that contain current panel (start), current panel (last), and total panels
		contenttype: ["inline"], //content setting ["inline"] or ["external", "path_to_external_file"]
		onslide:function(){
		  jQuery("#smooth_sliderc_nav li a").css("fontWeight", "normal");
		  jQuery("#smooth_sliderc_nav li a").css("fontSize", "'.$smooth_slider['content_fsize'].'px");
		  var curr_slide = imageA;
		  jQuery("#sldr"+curr_slide).css("fontWeight", "bolder");
		  jQuery("#sldr"+curr_slide).css("fontSize", "'.$fontSize.'px");';
		  
		  if ($smooth_slider['goto_slide'] == 2) { 
					
					global $sldr_nav_width;
					$sldr_nav_width = $smooth_slider['navimg_w'];
			 // var nav_width = <?php global $sldr_nav_width; echo $sldr_nav_width; 
			  $slider_html = $slider_html.'jQuery("#smooth_sliderc_nav a").css("backgroundPosition", "0 0");
			  jQuery("#sldr"+curr_slide).css("backgroundPosition", "-"+'.$sldr_nav_width.'+"px 0")';
	      }
	$slider_html=$slider_html.' }
	 })
	</script>
	<noscript><strong>This page is having a slideshow that uses Javascript. Your browser either doesn\'t support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.</strong></noscript>
			<div id="smooth_sldr">
			<div id="smooth_sldr_items">
				<div id="smooth_sldr_body">';
				
				$sldr_title = $smooth_slider['title_text']; 
				if(!empty($sldr_title)) { 
                   $slider_html=$slider_html.'<div class="sldr_title">'. $smooth_slider['title_text'].'</div>'; 
				} 
				global $smooth_sldr_j; 
				$r_array = carousel_posts_on_slider($smooth_slider['no_posts'], $offset=0, $slider_id, $echo = '0'); 
				$smooth_sldr_j = $r_array[0];
						
		$slider_html=$slider_html.'<div id="smooth_sliderc">
						<div class="smooth_sliderb">
						  '.$r_array[1].'
						</div>
					</div>
				</div>';
		if ($smooth_slider['goto_slide'] == 1) { 
			$slider_html=$slider_html.'<ul id="smooth_sliderc_nav">';
				for($i=1; $i<=$smooth_sldr_j; $i++) { 
					$slider_html=$slider_html.'<li><a id="sldr'.$i.'" href="javascript:stepcarousel.stepTo(\'smooth_sliderc\', '.$i.')" >'.$i.'</a></li>';
				} 
		  $slider_html=$slider_html.'</ul>';
        } 
		if ($smooth_slider['goto_slide'] == 2) { 
			$slider_html=$slider_html.'<div id="smooth_sliderc_nav">';
			$width = $smooth_slider['navimg_w'];
			for($i=1; $i<=$smooth_sldr_j; $i++) { 
			    $slider_html=$slider_html.'<a class="smooth_sliderc_nav" id="sldr'.$i.'" style="background-image:url('.smooth_slider_plugin_url('images/').'slide'.$i.'.png);background-position:0 0;width:'.$width.'px;height:'.$smooth_slider['navimg_ht'].'px;" href="javascript:stepcarousel.stepTo(\'smooth_sliderc\', '.$i.')" ></a>';
	        } 
			$slider_html=$slider_html.'</div>';
		}  
	    if ($smooth_slider['goto_slide'] == 3) { 	 
			$slider_html=$slider_html.'<div id="smooth_sliderc_nav"><li style="border:none;">'.$smooth_slider["custom_nav"].'</li></div>';
		} 
		$slider_html=$slider_html.'<div class="sldrlink"><a href="http://www.clickonf5.org/smooth-slider" target="_blank">Smooth Slider</a></div>
			</div>
		</div>';
  } //end of not empty slider_id condition
  return $slider_html;
}

function smooth_slider_simple_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '',
	), $atts));

	return return_smooth_slider($id);
}
add_shortcode('smoothslider', 'smooth_slider_simple_shortcode');

require_once (dirname (__FILE__) . '/settings/settings.php');
require_once (dirname (__FILE__) . '/includes/media-images.php');
?>