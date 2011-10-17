<?php // This function displays the page content for the Smooth Slider Options submenu
function smooth_slider_create_multiple_sliders() {
global $smooth_slider;
?>

<div class="wrap" style="clear:both;">

<h2 style="float:left;">Sliders Created</h2>
<form  style="float:left;" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="8046056">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<?php 
if ($_POST['remove_posts_slider']) {
   if ( $_POST['slider_posts'] ) {
       global $wpdb, $table_prefix;
       $table_name = $table_prefix.SLIDER_TABLE;
	   $current_slider = $_POST['current_slider_id'];
	   foreach ( $_POST['slider_posts'] as $post_id=>$val ) {
		   $sql = "DELETE FROM $table_name WHERE post_id = '$post_id' AND slider_id = '$current_slider' LIMIT 1";
		   $wpdb->query($sql);
	   }
   }
   if ($_POST['remove_all'] == "Remove All at Once") {
       global $wpdb, $table_prefix;
       $table_name = $table_prefix.SLIDER_TABLE;
	   $current_slider = $_POST['current_slider_id'];
	   if(is_slider_on_slider_table($current_slider)) {
		   $sql = "DELETE FROM $table_name WHERE slider_id = '$current_slider';";
		   $wpdb->query($sql);
	   }
   }
   if ($_POST['remove_all'] == 'Delete Slider') {
       $slider_id = $_POST['current_slider_id'];
       global $wpdb, $table_prefix;
       $slider_table = $table_prefix.SLIDER_TABLE;
       $slider_meta = $table_prefix.SLIDER_META;
	   $slider_postmeta = $table_prefix.SLIDER_POST_META;
	   if(is_slider_on_slider_table($slider_id)) {
		   $sql = "DELETE FROM $slider_table WHERE slider_id = '$slider_id';";
		   $wpdb->query($sql);
	   }
	   if(is_slider_on_meta_table($slider_id)) {
		   $sql = "DELETE FROM $slider_meta WHERE slider_id = '$slider_id';";
		   $wpdb->query($sql);
	   }
	   if(is_slider_on_postmeta_table($slider_id)) {
		   $sql = "DELETE FROM $slider_postmeta WHERE slider_id = '$slider_id';";
		   $wpdb->query($sql);
	   }
   }
}
if ($_POST['create_new_slider']) {
   $slider_name = $_POST['new_slider_name'];
   global $wpdb,$table_prefix;
   $slider_meta = $table_prefix.SLIDER_META;
   $sql = "INSERT INTO $slider_meta (slider_name) VALUES('$slider_name');";
   $result = $wpdb->query($sql);
}
if ($_POST['reorder_posts_slider']) {
   $i=1;
   global $wpdb, $table_prefix;
   $table_name = $table_prefix.SLIDER_TABLE;
   foreach ($_POST['order'] as $slide_order) {
    $slide_order = intval($slide_order);
    $sql = 'UPDATE '.$table_name.' SET slide_order='.$i.' WHERE post_id='.$slide_order.'';
    $wpdb->query($sql);
    $i++;
  }
}
?>
<div style="clear:both"></div>
<?php $url = sslider_admin_url( array( 'page' => 'smooth-slider-settings' ) );?>
<a href="<?php echo $url; ?>" title="Settings Page for Smooth Slider where you can change the color, font etc. for the sliders">Go to Smooth Slider Settings page</a>
<?php $sliders = ss_get_sliders(); ?>

<div id="slider_tabs">
        <ul class="ui-tabs">
        <?php foreach($sliders as $slider){?>
            <li><a href="#tabs-<?php echo $slider['slider_id'];?>"><?php echo $slider['slider_name'];?></a></li>
        <?php } ?>
        <?php if($smooth_slider['multiple_sliders'] == '1') {?>
            <li><a href="#new_slider">Create New Slider</a></li>
        <?php } ?>
        </ul>

<?php foreach($sliders as $slider){
?>
<div id="tabs-<?php echo $slider['slider_id'];?>">
<form action="" method="post">
<?php settings_fields('smooth-slider-group'); ?>

<input type="hidden" name="remove_posts_slider" value="1" />
<div id="tabs-<?php echo $slider['slider_id'];?>">
<h3>Posts/Pages Added To <?php echo $slider['slider_name'];?>(Slider ID = <?php echo $slider['slider_id'];?>)</h3>
<p><em>Check the Post/Page and Press "Remove Selected" to remove them From <?php echo $slider['slider_name'];?>. Press "Remove All at Once" to remove all the posts from the <?php echo $slider['slider_name'];?>.</em></p>

    <table class="widefat">
    <thead><tr><th>Post/Page Title</th><th>Author</th><th>Post Date</th><th>Remove Post</th></tr></thead><tbody>

<?php  
	/*global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;*/
	$slider_id = $slider['slider_id'];
	//$slider_posts = $wpdb->get_results("SELECT post_id FROM $table_name WHERE slider_id = '$slider_id'", OBJECT); 
    $slider_posts=get_slider_posts_in_order($slider_id); ?>
	
    <input type="hidden" name="current_slider_id" value="<?php echo $slider_id;?>" />
    
<?php    $count = 0;	
	foreach($slider_posts as $slider_post) {
	  $slider_arr[] = $slider_post->post_id;
	  $post = get_post($slider_post->post_id);	  
	  if ( in_array($post->ID, $slider_arr) ) {
		  $count++;
		  $sslider_author = get_userdata($post->post_author);
          $sslider_author_dname = $sslider_author->display_name;
		  echo '<tr' . ($count % 2 ? ' class="alternate"' : '') . '><td><strong>' . $post->post_title . '</strong><a href="'.get_edit_post_link( $post->ID, $context = 'display' ).'" target="_blank"> (Edit)</a> <a href="'.get_permalink( $post->ID ).'" target="_blank"> (View) </a></td><td>By ' . $sslider_author_dname . '</td><td>' . date('l, F j. Y',strtotime($post->post_date)) . '</td><td><input type="checkbox" name="slider_posts[' . $post->ID . ']" value="1" /></td></tr>'; 
	  }
	}
		
	if ($count == 0) {
		echo '<tr><td colspan="4">No posts/pages have been added to the Slider - You can add respective post/page to slider on the Edit screen for that Post/Page</td></tr>';
	}
	echo '</tbody><tfoot><tr><th>Post/Page Title</th><th>Author</th><th>Post Date</th><th>Remove Post</th></tr></tfoot></table>'; 
    
	echo '<div class="submit">';
	
	if ($count) {echo '<input type="submit" value="Remove Selected" onclick="return confirmRemove()" /><input type="submit" name="remove_all" value="Remove All at Once" onclick="return confirmRemoveAll()" />';}
	
	if($slider_id != '1') {
	   echo '<input type="submit" value="Delete Slider" name="remove_all" onclick="return confirmSliderDelete()" />';
	}
	
	echo '</div>';
?>    
    </tbody></table>
 </form>
 
 
 <form action="" method="post">
    <input type="hidden" name="reorder_posts_slider" value="1" />
    <h3>Reorder the Posts/Pages Added To <?php echo $slider['slider_name'];?>(Slider ID = <?php echo $slider['slider_id'];?>)</h3>
    <p><em>Click on and drag the post/page title to a new spot within the list, and the other items will adjust to fit. </em></p>
    <ul id="sslider_sortable_<?php echo $slider['slider_id'];?>" style="color:#326078">    
    <?php  
    /*global $wpdb, $table_prefix;
	$table_name = $table_prefix.SLIDER_TABLE;*/
	$slider_id = $slider['slider_id'];
	//$slider_posts = $wpdb->get_results("SELECT post_id FROM $table_name WHERE slider_id = '$slider_id'", OBJECT); 
    $slider_posts=get_slider_posts_in_order($slider_id);?>
        
        <input type="hidden" name="current_slider_id" value="<?php echo $slider_id;?>" />
        
    <?php    $count = 0;	
        foreach($slider_posts as $slider_post) {
          $slider_arr[] = $slider_post->post_id;
          $post = get_post($slider_post->post_id);	  
          if ( in_array($post->ID, $slider_arr) ) {
              $count++;
              $sslider_author = get_userdata($post->post_author);
              $sslider_author_dname = $sslider_author->display_name;
              echo '<li id="'.$post->ID.'"><input type="hidden" name="order[]" value="'.$post->ID.'" /><strong> &raquo; &nbsp; ' . $post->post_title . '</strong></li>'; 
          }
        }
            
        if ($count == 0) {
            echo '<li>No posts/pages have been added to the Slider - You can add respective post/page to slider on the Edit screen for that Post/Page</li>';
        }
		        
        echo '</ul><div class="submit">';
        
        if ($count) {echo '<input type="submit" value="Save the order"  />';}
                
        echo '</div>';
    ?>    
       </div>       
  </form>
</div> 
 
<?php } ?>

<?php if($smooth_slider['multiple_sliders'] == '1') {?>
    <div id="new_slider">
    <form action="" method="post" onsubmit="return slider_checkform(this);" >
    <h3>Enter New Slider Name</h3>
    <input type="hidden" name="create_new_slider" value="1" />
    
    <input name="new_slider_name" class="regular-text code" value="" style="clear:both;" />
    
    <div class="submit"><input type="submit" value="Create New" name="create_new" /></div>
    
    </form>
    </div>
<?php }?> 
</div>

<div id="poststuff" class="metabox-holder has-right-sidebar"> 
   <div id="side-info-column" class="inner-sidebar" style="float:left;"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span>About this Plugin:</span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://www.clickonf5.org/smooth-slider" title="Smooth Slider Homepage" >Plugin Homepage</a></li>
                <li><a href="http://www.clickonf5.org" title="Visit Internet Techies" >Plugin Parent Site</a></li>
                <li><a href="http://www.clickonf5.org/phpbb/smooth-slider-f12/" title="Support Forum for Smooth Slider" >Support Forum</a></li>
                <li><a href="http://www.clickonf5.org/about/tejaswini" title="Smooth Slider Author Page" >About the Author</a></li>
                <li><a href="http://www.clickonf5.org/go/smooth-slider/" title="Donate if you liked the plugin and support in enhancing Smooth Slider and creating new plugins" >Donate with Paypal</a></li>
                </ul> 
              </div> 
			</div> 
     </div>
     
        <div id="side-info-column" class="inner-sidebar" style="float:left;margin-left:1em"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span></span>Our Facebook Fan Page</h3> 
			  <div class="inside">
                <script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_GB"></script><script type="text/javascript">FB.init("2aeebe9fb014836a6810ec4426d26f7e");</script><fb:fan profile_id="127760528543" stream="" connections="8" width="270" height="250"></fb:fan>
              </div> 
			</div> 
     </div>

     <div id="side-info-column" class="inner-sidebar" style="float:left;margin-left:1em"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span>Credits:</span></h3> 
			  <div class="inside">
                <ul>
                <li><a href="http://www.dynamicdrive.com" title="Step Carousel jQuery plugin by Dynamic Drive" >Step Carousel Viewer</a></li>
                <li><a href="http://www.bioxd.com/featureme" title="FeatureMe Wordpress Plugin by Oscar Alcalá" >FeatureMe Wordpress Plugin</a></li>
                <li><a href="http://acko.net/dev/farbtastic" title="Farbtastic Color Picker by Steven Wittens" >Farbtastic Color Picker</a></li>
                <li><a href="http://code.google.com/p/timthumb/" title="TimThumb script by Tim McDaniels and Darren Hoyt with tweaks by Ben Gillbanks" >TimThumb script</a></li>
                <li><a href="http://jquery.com/" title="jQuery JavaScript Library - John Resig" >jQuery JavaScript Library</a></li>
                </ul> 
              </div> 
			</div> 
     </div>
     
          <div id="side-info-column" class="inner-sidebar" style="float:left;margin-left:1em"> 
			<div class="postbox"> 
			  <h3 class="hndle"><span>Support &amp; Donations</span></h3> 
			  <div class="inside">
                <div id="smooth_sldr_donations">
                 <ul>
                    <li><a href="http://malamedconsulting.com/" target="_blank">Connie Malamed - $25</a></li>
                    <li><a href="http://www.jacobwiechman.com/wordpress/" target="_blank">Jacob Wiechman - $30</a></li>
                    <li><a href="http://www.whatsthebigidea.com/" target="_blank">WhatsTheBigIdea.com,Inc. - $20</a></li>
                    <li><a href="http://uwaterloo.ca/" target="_blank">Trevor Bain - $25</a></li>
                    <li><a href="http://thule-italia.com/wordpress/" target="_blank">Marco Linguardo - $10</a></li>
                    <li><a href="http://eircom.net" target="_blank">Paul Goode - $5</a></li>
                    <li><a href="http://www.windowsobserver.com/" target="_blank">Richard Hay - $10</a></li>
                    <li><a href="http://www.maximotimes.com/maximo/" target="_blank">Chonbury Neth - $10</a></li>
                    <li><a href="http://www.yobeat.com/" target="_blank">Brooke Geery - $10</a></li>
                 </ul>
					<script language="JavaScript" type="text/javascript">
                    <!--
                        // Customize the widget by editing the fields below
                        // All fields are required
                    
                        // Your Feedity RSS feed URL
                        feedity_widget_feed = "http://feedity.com/rss.aspx/clickonf5-org/UlVTUldR";
                    
                        // Number of items to display in the widget
                        feedity_widget_numberofitems = "10";
                    
                        // Show feed item published date (values: yes or no)
                        feedity_widget_showdate = "no";
                    
                        // Widget box width (in px, pt, em, or %)
                        feedity_widget_width = "220px";
                    
                        // Widget background color in hex or by name (eg: #ffffff or white)
                        feedity_widget_backcolor = "#ffffff";
                    
                        // Widget font/link color in hex or by name (eg: #000000 or black)
                        feedity_widget_fontcolor = "#000000";
                    //-->
                    </script>
                    <script language="JavaScript" type="text/javascript" src="http://feedity.com/js/widget.js"></script>
                </div>
              </div> 
			</div> 
     </div>  
     <div style="clear:left;"></div>
 </div> <!--end of poststuff --> 




</div> <!--end of float wrap -->
<?php	
}
?>